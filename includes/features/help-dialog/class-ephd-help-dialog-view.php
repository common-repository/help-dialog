<?php defined( 'ABSPATH' ) || exit();

/**
 * Display the Help dialog on the frontend
 */
class EPHD_Help_Dialog_View {

	private $widget_config;
	private $is_opened;
	private $is_admin_preview;
	private $global_config;
	private $pro_enabled;
	private $display_faqs;
	private $display_contact;
	private $display_resource;
	private $display_chat;
	private $chat_available;

	public function __construct( $widget_config = null, $is_opened = false, $is_admin_preview = false, $global_config = null ) {

		$this->widget_config    = $widget_config;
		$this->is_opened        = $is_opened;
		$this->is_admin_preview = $is_admin_preview;
		$this->global_config    = $global_config;
		$this->pro_enabled      = EPHD_Utilities::is_help_dialog_pro_enabled();
		$this->chat_available 	= EPHD_Utilities::is_hd_pro_chat_available();
		$this->display_faqs		= ! empty( $widget_config ) && in_array( 'faqs', array( $widget_config['tabs_position_1'], $widget_config['tabs_position_2'], $widget_config['tabs_position_3'], $widget_config['tabs_position_4'] ) );
		$this->display_contact	= ! empty( $widget_config ) && in_array( 'contact', array( $widget_config['tabs_position_1'], $widget_config['tabs_position_2'], $widget_config['tabs_position_3'], $widget_config['tabs_position_4'] ) );
		$this->display_resource = ! empty( $widget_config ) && in_array( 'resource', array( $widget_config['tabs_position_1'], $widget_config['tabs_position_2'], $widget_config['tabs_position_3'], $widget_config['tabs_position_4'] ) );
		$this->display_chat 	= ! empty( $widget_config ) && $this->pro_enabled && $this->chat_available && in_array( 'chat', array( $widget_config['tabs_position_1'], $widget_config['tabs_position_2'], $widget_config['tabs_position_3'], $widget_config['tabs_position_4'] ) );

		// if this is HD preview for backend then exit
		if ( ! empty( $widget_config ) ) {
			$this->is_admin_preview = false;
			return;
		}

		add_action( 'wp_footer', array( $this, 'output_help_dialog' ) );
	}

	/**
	 * Display Help Dialog on the current page
	 *
	 * @param bool $return_html
	 *
	 * @return false|string|void
	 */
	public function output_help_dialog( $return_html = false ) {

		$is_front_page = is_front_page();

		// for public frontend we need to initialize widget_config later than in constructor to have the global $post set up
		if ( empty( $this->widget_config ) ) {

			// return if HD should not be displayed
			$this->widget_config = self::get_help_dialog_if_can_be_displayed( $is_front_page );
			if ( empty( $this->widget_config ) ) {
				return;
			}
		}

		// for demo preview config we might have unsaved version of global config
		if ( empty( $this->global_config ) ) {
			$this->global_config = ephd_get_instance()->global_config_obj->get_config();
		}

		$this->chat_available 	= EPHD_Utilities::is_hd_pro_chat_available();
		$this->display_faqs		= empty( $this->display_faqs ) ? in_array( 'faqs', array( $this->widget_config['tabs_position_1'], $this->widget_config['tabs_position_2'], $this->widget_config['tabs_position_3'], $this->widget_config['tabs_position_4'] ) ) : $this->display_faqs;
		$this->display_contact	= empty( $this->display_contact ) ? in_array( 'contact', array( $this->widget_config['tabs_position_1'], $this->widget_config['tabs_position_2'], $this->widget_config['tabs_position_3'], $this->widget_config['tabs_position_4'] ) ) : $this->display_contact;
		$this->display_resource = empty( $this->display_resource ) ? in_array( 'resource', array( $this->widget_config['tabs_position_1'], $this->widget_config['tabs_position_2'], $this->widget_config['tabs_position_3'], $this->widget_config['tabs_position_4'] ) ) : $this->display_resource;
		$this->display_chat 	= empty( $this->display_chat ) ? $this->pro_enabled && $this->chat_available && in_array( 'chat', array( $this->widget_config['tabs_position_1'], $this->widget_config['tabs_position_2'], $this->widget_config['tabs_position_3'], $this->widget_config['tabs_position_4'] ) ) : $this->display_chat;

		if ( ! $return_html ) {
			do_action( 'ephd_enqueue_help_dialog_resources' );
			do_action( 'ephd_enqueue_help_dialog_scripts' );
			do_action( 'ephd_enqueue_help_dialog_pro_scripts' );
		}

		if ( ! empty( $return_html ) ) {
			ob_start();
		}

		$this->display_help_dialog( $is_front_page );

		if ( ! empty( $return_html ) ) {
			return ob_get_clean();
		}
	}

	/**
	 * Display the Help Dialog box on frontend or admin pages.
	 *
	 * @param $is_front_page
	 */
	private function display_help_dialog( $is_front_page ) {
		global $post;

		// define active tab - the first enabled tab in the sequence
		$active_tab_key = 'faqs';
		for ( $i = 1; $i <= 4; $i++ ) {
			if ( empty( $this->widget_config['tabs_position_' . $i] ) || $this->widget_config['tabs_position_' . $i] == 'none' ) {
				continue;
			}
			if ( $this->widget_config['tabs_position_' . $i] == 'chat' && ! $this->chat_available ) {
				continue;
			}
			$active_tab_key = $this->widget_config['tabs_position_' . $i];
			break;
		}

		// Only administrators can see this message
		// if ( current_user_can( 'manage_options' ) ) {
			// EPHD_Core_Utilities::display_missing_css_message();
		// }

		$activeWPTheme = 'ephd-help-dialog-active-theme-' . EPHD_Utilities::get_wp_option( 'stylesheet', 'unknown' );
		$widget_specs  = EPHD_Config_Specs::get_fields_specification( EPHD_Widgets_DB::EPHD_WIDGETS_CONFIG_NAME );	?>

		<div id="ephd-help-dialog" class="ephd-help-dialog-container ephd-widget-preset--<?php echo esc_attr( $this->global_config['dialog_width'] ); ?>
		 			ephd-widget--<?php echo esc_attr( $this->widget_config['widget_id'] ) . ' ' . esc_attr( $activeWPTheme ) . ' ' . 'ephd-hd-' . esc_attr( $active_tab_key ) . '-tab--active'
					; echo $this->is_admin_preview ? ' ' . 'ephd-hd-admin-preview' : ''; echo $this->widget_config['dialog_initial_visibility'] == 'on' ? ' ' . 'ephd-widget--initial-on' : '' ?>"
				style="display:<?php echo $this->is_opened ? 'inline-flex' : 'none'; ?>;"
				data-ephd-tab="<?php echo esc_attr( $active_tab_key ); ?>"
				data-ephd-page-id="<?php echo empty( $post ) ? -1 : ( $is_front_page ? 0 : esc_attr( $post->ID ) ); ?>"
				data-ephd-count-analytics="<?php echo self::is_count_analytics( $post ) ? 'on' : ''; ?>"
				data-ephd-widget-id="<?php echo esc_attr( $this->widget_config['widget_id'] ); ?>"
				data-ephd-triggers-visibility="visible"
				data-ephd-trigger-delay-seconds="<?php echo $this->widget_config['trigger_delay_toggle'] == 'on' ? esc_attr( $this->widget_config['trigger_delay_seconds'] ) : ''; ?>"
				data-ephd-trigger-scroll-percent="<?php echo $this->widget_config['trigger_scroll_toggle'] == 'on' ? esc_attr( $this->widget_config['trigger_scroll_percent'] ) : ''; ?>"
				role="dialog" aria-labelledby="ephd-hd-header__welcome__title" aria-describedby="ephd-hd-header__welcome__text" tabindex="-1">            <?php

			$this->display_tabs_if_enabled();   ?>

			<!-- HEADER CONTAINER -->
			<div id="ephd-hd-header-container">

				<!-- MAIN HEADING -->
				<div class="ephd-hd-header__main-heading-container">					<?php

					if ( ! empty( $this->global_config['logo_image_url'] ) ) {  ?>
						<div class="ephd-hd-header__logo">
							<img class="ephd-hd-header__logo__img" src="<?php echo esc_url( $this->global_config['logo_image_url'] ); ?>" alt="">
						</div>      <?php
					} ?>

					<div class="ephd-hd-header__welcome <?php echo empty( $this->global_config['logo_image_url'] ) ? 'ephd-hd-header__welcome--centered' : ''; ?>">
						<div id="ephd-hd-header__welcome__title" class="ephd-hd-header__welcome__title"><?php echo wp_kses( $this->widget_config['welcome_title'], $widget_specs['welcome_title']['allowed_tags'] ); ?></div>
						<div id="ephd-hd-header__welcome__text" class="ephd-hd-header__welcome__text"><?php echo wp_kses( $this->widget_config['welcome_text'], $widget_specs['welcome_text']['allowed_tags'] ); ?></div> <?php
						// TODO future: display Chat header or use hook
						if ( $this->display_contact ) {  ?>
							<div id="ephd-hd-header__welcome__contact__title" class="ephd-hd-header__welcome__contact__title"><?php echo wp_kses( $this->widget_config['contact_welcome_title'], $widget_specs['contact_welcome_title']['allowed_tags'] ); ?></div>
							<div id="ephd-hd-header__welcome__contact__text" class="ephd-hd-header__welcome__contact__text"><?php echo wp_kses( $this->widget_config['contact_welcome_text'], $widget_specs['contact_welcome_text']['allowed_tags'] ); ?></div>   <?php
						}
						if ( $this->display_resource ) {  ?>
							<div id="ephd-hd-header__welcome__resource__title" class="ephd-hd-header__welcome__resource__title"><?php echo wp_kses( $this->widget_config['resource_header_title'], $widget_specs['resource_header_title']['allowed_tags'] ); ?></div>
							<div id="ephd-hd-header__welcome__resource__text" class="ephd-hd-header__welcome__resource__text"><?php echo wp_kses( $this->widget_config['resource_header_sub_title'], $widget_specs['resource_header_sub_title']['allowed_tags'] ); ?></div>   <?php
						}	?>
					</div>  <?php

					$this->display_search_results_header_if_enabled();  ?>

				</div>  <?php

				$this->display_sub_heading_if_enabled();   ?>

			</div>

			<!-- BODY CONTAINER -->
			<div id="ephd-hd-body-container">    <?php

				do_action( 'ephd_help_dialog_body_boxes', $post, $this->widget_config );

				$this->display_resource_box_if_enabled();

				$this->display_faqs_box_if_enabled();

				$this->display_contact_box_if_enabled( $post, $is_front_page );    ?>

				<div class="ephd-hd__loading-spinner__container">
					<div class="ephd-hd__loading-spinner"></div>
				</div>

			</div>

			<!-- FOOTER CONTAINER -->
			<div id="ephd-hd-footer-container">  <?php
				$powered_by_html = self::display_powered_by_box();
			if ( ! empty( $powered_by_html ) && is_string( $powered_by_html ) ) {
				echo wp_kses_post( $powered_by_html );
			}   ?>
			</div>
		</div>  <?php

		$help_dialog_launcher_analytics_delay = 5;
		$help_dialog_launcher_start_wait      = 0;
		if ( ! empty( $this->widget_config['launcher_start_wait'] ) ) {
			$help_dialog_launcher_start_wait = $this->widget_config['launcher_start_wait'];
		}

		// show notification to admin if widget is draft
		$draft_notification = '';
		if ( $this->widget_config['widget_status'] == EPHD_Help_Dialog_Handler::HELP_DIALOG_STATUS_DRAFT && ! $this->is_admin_preview ) {
			ob_start(); ?>
			<span class="ephd-hd-toggle__draft-notification">
				<?php esc_html_e( 'DRAFT - Currently Not Visible to the Public', 'help-dialog' ); ?>
			</span>    <?php
			$draft_notification = ob_get_clean();
		}

		// display launcher icon/text
		if ( EPHD_Utilities::is_help_dialog_pro_enabled() ) {
			echo apply_filters( 'ephd_help_dialog_show_launcher', '', array(
					'is_open'            => $this->is_opened,
					'is_shown'           => $this->is_admin_preview,
					'is_front_page'      => $is_front_page,
					'analytics_delay'    => $help_dialog_launcher_analytics_delay,
					'launcher_wait'      => $help_dialog_launcher_start_wait,
					'draft_notification' => $draft_notification,
					'widget_config'      => $this->widget_config,
			) );

			return;
		}   ?>

		<div
			class="ephd-hd-toggle <?php echo $this->is_opened ? 'ephd-hd-toggle--on' : 'ephd-hd-toggle--off'; ?> ephd-widget--<?php echo esc_attr( $this->widget_config['widget_id'] ); ?> <?php echo esc_attr( $activeWPTheme ) . '-toggle'; ?>"
			data-ephd-analytics-delay="<?php echo esc_attr( $help_dialog_launcher_analytics_delay ); ?>"
			data-ephd-start-wait="<?php echo esc_attr( $help_dialog_launcher_start_wait ); ?>"
			style="display:<?php echo ( $this->is_opened || $this->is_admin_preview ) ? 'block' : 'none'; ?>;"
			role="button" aria-label="Open Help Dialog" tabindex="0" aria-pressed="false">                <?php

			// Format icon based on Font Icon or HD custom Icon.
			if ( $this->widget_config['launcher_icon'] == 'ep_font_icon_help_dialog' ) { ?>
				<span class="ephd-hd-toggle__icon ephd-hd-icon ephdfa <?php echo $this->is_opened ? 'ephdfa-times' : esc_attr( $this->widget_config['launcher_icon'] ); ?>"
						data-ephd-toggle-icons="ephdfa-times <?php echo esc_attr( $this->widget_config['launcher_icon'] ); ?>">
				</span>                <?php
			} else {    ?>
				<span class="ephd-hd-toggle__icon ephdfa <?php echo $this->is_opened ? 'ephdfa-times' : 'ephdfa-' . esc_attr( $this->widget_config['launcher_icon'] ); ?>"
						data-ephd-toggle-icons="ephdfa-times <?php echo 'ephdfa-' . esc_attr( $this->widget_config['launcher_icon'] ); ?>">
				</span>                <?php
			}

			echo wp_kses_post( $draft_notification );   ?>
		</div> <?php
	}

	/**
	 * Display Tabs
	 */
	private function display_tabs_if_enabled() {

		// do not display tabs if there are less than 2 tabs are enabled
		$tabs_state = array_count_values( array_map( 'intval', [ $this->display_resource, $this->display_faqs, $this->display_contact, $this->display_chat ] ) );
		if ( ! isset( $tabs_state[1] ) || $tabs_state[1] < 2 ) {
			return;
		}	?>

		<!-- TAB CONTAINER -->
		<div id="ephd-hd-top-tab-container" role="tablist" aria-label="Help Dialog Top Tabs">	<?php
			$is_tab_active = true;
			for ( $i = 1; $i <= 4; $i++ ) {
				if ( empty( $this->widget_config['tabs_position_' . $i] ) || $this->widget_config['tabs_position_' . $i] == 'none' ) {
					continue;
				}
				$this->display_top_tabs_if_enabled( $this->widget_config['tabs_position_' . $i], $is_tab_active );
			}	?>
		</div>	<?php
	}

	/**
	 * Display Top Tab
	 *
	 * @param $tab_key
	 * @param $is_tab_active
	 *
	 * @return void
	 */
	private function display_top_tabs_if_enabled( $tab_key, &$is_tab_active ) {

		switch ( $tab_key ) {

			// Resource
			case 'resource':
				$tab_args = array(
					'display_tab'	=> $this->display_resource,
					'title'			=> $this->widget_config['resource_header_top_tab'],
					'key'			=> 'resource',
					'target_key'	=> 'resource',
				);
				break;

			// FAQs
			case 'faqs':
				$tab_args = array(
					'display_tab'	=> $this->display_faqs,
					'title'			=> $this->widget_config['faqs_top_tab'],
					'key'			=> 'faq',
					'target_key'	=> 'faqs',
				);
				break;

			// Contact
			case 'contact':
				$tab_args = array(
					'display_tab'	=> $this->display_contact,
					'title'			=> $this->widget_config['contact_us_top_tab'],
					'key'			=> 'contact',
					'target_key'	=> 'contact',
				);
				break;

			// HD PRO add-on
			default:
				$tab_args = has_filter( 'ephd_help_dialog_top_tab_args' ) ? apply_filters( 'ephd_help_dialog_top_tab_args', [], $tab_key, $this->widget_config ) : [];
				break;
		}

		$tab_args = wp_parse_args( $tab_args, array(
			'display_tab' => '',
			'title' => '',
			'key' => '',
			'target_key' => '',
		) );

		if ( empty( $tab_args['display_tab'] ) ) {
			return;
		}	?>

		<div id="ephd-hd-<?php echo esc_attr( $tab_args['key'] ); ?>-tab" role="tab" aria-selected="<?php echo $is_tab_active ? 'true' : 'false'; ?>"
			 tabindex="0" class="ephd-hd-tab ephd-hd-tab__<?php echo esc_attr( $tab_args['key'] ); ?>-btn <?php echo $is_tab_active ? 'ephd-hd-tab--active' : ''; ?>" data-ephd-target-tab="<?php echo esc_attr( $tab_args['target_key'] ); ?>">
			<span class="ephd-hd-tab__<?php echo esc_attr( $tab_args['key'] ); ?>-btn__text"><?php echo esc_html( $tab_args['title'] ); ?></span>
		</div>  <?php

		$is_tab_active = false;
	}

	/**
	 * Display Resource box if it is enabled
	 */
	private function display_resource_box_if_enabled() {

		if ( ! $this->display_resource ) {
			return;
		}	?>

		<!-- Resource Container -->
		<div id="ephd-hd-body__resource-container" role="tabpanel" tabindex="0" aria-labelledby="ephd-hd-resource-tab" data-ephd-tab="resource">

			<!-- Resource List -->
			<div class="ephd-hd-resource-container">
				<div class="ephd-hd-resource-welcome-text"><?php echo esc_html( $this->widget_config['resource_welcome_text'] ); ?></div>
				<div class="ephd-hd-resource-list">    <?php

					$widget_specs = EPHD_Config_Specs::get_fields_specification( EPHD_Widgets_DB::EPHD_WIDGETS_CONFIG_NAME );

					// Phone Resource
					if ( $this->widget_config['resource_phone_toggle'] == 'on' ) {
						$resource_phone_number           = $this->widget_config['resource_phone_country_code'] . $this->widget_config['resource_phone_number'];
						$resource_phone_number_image_url = $this->widget_config['resource_phone_number_image_url'];

                        ?>
						<div class="ephd-hd-resource ephd-hd-resource--phone">
							<a href="tel:<?php echo esc_attr( $resource_phone_number ); ?>" class="ephd-hd-resource-logo">   <?php
							if ( $resource_phone_number_image_url != $widget_specs['resource_phone_number_image_url']['default'] ) { ?>
									<img src="<?php echo esc_url( $resource_phone_number_image_url ); ?>" alt="" />  <?php
							} else {    ?>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="ephd-hd-resource-phone-number-icon">
										<path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z" />
									</svg>  <?php
							}   ?>
							</a>
							<div class="ephd-hd-resource-name" id="ephd-hd-resource-phone-number-name"><?php echo esc_html( $this->widget_config['resource_phone_label'] ); ?></div>
						</div>  <?php
					}

					// Custom Link Resource
					if ( $this->widget_config['resource_custom_link_toggle'] == 'on' ) {
						$resource_custom_link_image_url = $this->widget_config['resource_custom_link_image_url'];   ?>
						<div class="ephd-hd-resource ephd-hd-resource--custom-link">
							<a href="<?php echo esc_url( $this->widget_config['resource_custom_link_url'] ); ?>" class="ephd-hd-resource-logo" target="_blank">  <?php
							if ( $resource_custom_link_image_url != $widget_specs['resource_custom_link_image_url']['default'] ) {  ?>
									<img src="<?php echo esc_url( $resource_custom_link_image_url ); ?>" alt="" />   <?php
							} else {    ?>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="ephd-hd-resource-link-icon">
										<path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z"/>
									</svg>  <?php
							}   ?>
							</a>
							<div class="ephd-hd-resource-name" id="ephd-hd-resource-link-name"><?php echo esc_html( $this->widget_config['resource_custom_link_label'] ); ?></div>
						</div>  <?php
					}   ?>

				</div>
			</div>

		</div>      <?php
	}

	/**
	 * Display FAQs box if it is enabled
	 */
	private function display_faqs_box_if_enabled() {

		if ( ! $this->display_faqs ) {
			return;
		}

		// Adjust FAQ height If Powered By or Image URL is visible.
		$height = $this->widget_config['launcher_powered_by'] == 'hide' ? 344 : 368;
		$height = empty( $this->global_config['logo_image_url'] ) ? $height + 30 : $height; ?>


		<!-- FAQs Container -->
		<div id="ephd-hd-body__content-container" role="tabpanel" tabindex="0" aria-labelledby="ephd-hd-faq-tab" data-ephd-tab="faqs">

			<!-- FAQ List -->
			<div class="ephd-hd-faq-container" style="height: <?php echo esc_attr( $height ); ?>px;">

				<div class="ephd-hd-faq__list"> <?php

					$this->display_faqs_list_or_no_faqs_message();

					$this->display_search_result_box_if_enabled();   ?>

				</div>

			</div>  <?php

			$this->display_search_input_box_if_enabled(); ?>

		</div>      <?php
	}

	/**
	 * Display Contact Box if it is enabled
	 *
	 * @param $post
	 * @param $is_front_page
	 */
	private function display_contact_box_if_enabled( $post, $is_front_page ) {

		if ( ! $this->display_contact ) {
			return;
		}   ?>

		<!-- Contact form -->
		<div id="ephd-hd-body__contact-container" role="tabpanel" tabindex="0" aria-labelledby="ephd-hd-contact-us-tab" data-ephd-tab="contact">
			<form id="ephd-hd__contact-form" method="post" enctype="multipart/form-data">
				<div class="ephd-hd__contact-form-response"></div>          <?php
				wp_nonce_field( '_wpnonce_ephd_ajax_action' );              ?>
				<input type="hidden" name="action" value="ephd_help_dialog_contact">
				<input type="hidden" name="widget_id" value="<?php echo esc_attr( $this->widget_config['widget_id'] ); ?>">
				<input type="hidden" name="widget_name" value="<?php echo esc_attr( $this->widget_config['widget_name'] ); ?>">
				<input type="hidden" name="page_id" value="<?php echo empty( $post ) ? -1 : ( $is_front_page ? 0 : esc_attr( $post->ID ) ); ?>">
				<input type="hidden" name="page_name" value="<?php echo empty( $post ) ? '' : esc_attr( $post->post_title ); ?>">
				<input type="hidden" name="contact_form_id" value="<?php echo esc_attr( $this->widget_config['widget_id'] ); ?>">
				<div id="ephd-hd__contact-form-body">   <?php

					if ( $this->widget_config['contact_name_toggle'] == 'on' ) {   ?>
						<div class="ephd-hd__contact-form-field">
							<label class="ephd-hd__contact-form-user_first_name_label" for="ephd-hd__contact-form-user_first_name">     <?php
							echo esc_html( $this->widget_config['contact_name_text'] );     ?>
								<span class="ephd-hd__contact-form-field__required-tag">*</span>
							</label>
							<input name="user_first_name" type="text" value="" required id="ephd-hd__contact-form-user_first_name" placeholder="" maxlength="<?php echo esc_attr( EPHD_Submissions_DB::NAME_LENGTH ); ?>">
						</div>      <?php
					}

					// Set fake input field that is visible only for spam bots     ?>
					<div class="ephd-hd__contact-form-field ephd-hd__contact-form-field--catch-details">
						<label class="ephd-hd__contact-form-comment_label" for="ephd-hd__contact-form-catch-details">
							<span class="ephd-hd__contact-form-field__label-text"><?php esc_html_e( 'Catch Details', 'help-dialog' ); ?></span>
						</label>
						<input name="catch_details" type="text" value="" id="ephd-hd__contact-form-catch-details" placeholder="" maxlength="100" tabindex="-1" autocomplete="off">
					</div>

					<div class="ephd-hd__contact-form-field">
						<label class="ephd-hd__contact-form-email_label" for="ephd-hd__contact-form-email">
							<span class="ephd-hd__contact-form-field__label-text"><?php echo esc_html( $this->widget_config['contact_user_email_text'] ); ?></span>
							<span class="ephd-hd__contact-form-field__required-tag">*</span>
						</label>
						<input name="email" type="email" value="" required id="ephd-hd__contact-form-email" placeholder="" maxlength="<?php echo esc_attr( EPHD_Submissions_DB::EMAIL_LENGTH ); ?>">
					</div>  <?php

					if ( $this->widget_config['contact_subject_toggle'] == 'on' ) {   ?>
						<div class="ephd-hd__contact-form-field">
							<label class="ephd-hd__contact-form-subject_label" for="ephd-hd__contact-form-subject">
								<span class="ephd-hd__contact-form-field__label-text"><?php echo esc_html( $this->widget_config['contact_subject_text'] ); ?></span>
								<span class="ephd-hd__contact-form-field__required-tag">*</span>
							</label>
							<input name="subject" type="text" value="" required id="ephd-hd__contact-form-subject" placeholder="" maxlength="<?php echo esc_attr( EPHD_Submissions_DB::SUBJECT_LENGTH ); ?>">
						</div>  <?php
					}   ?>

					<div class="ephd-hd__contact-form-field">
						<label class="ephd-hd__contact-form-comment_label" for="ephd-hd__contact-form-comment">
							<span class="ephd-hd__contact-form-field__label-text"><?php echo esc_html( $this->widget_config['contact_comment_text'] ); ?></span>
							<span class="ephd-hd__contact-form-field__required-tag">*</span>
						</label>
						<textarea name="comment" required id="ephd-hd__contact-form-comment" rows="4" placeholder="" maxlength="<?php echo esc_attr( EPHD_Submissions_DB::COMMENT_LENGTH ); ?>"></textarea>
					</div>                    <?php

					// Acceptance Checkbox
					if ( $this->widget_config['contact_acceptance_checkbox'] == 'on' ) {
						$widget_specs = EPHD_Config_Specs::get_fields_specification( EPHD_Widgets_DB::EPHD_WIDGETS_CONFIG_NAME );  ?>

						<div class="ephd-hd__contact-form-field ephd-hd__contact-form-acceptance-container">   <?php
						if ( $this->widget_config['contact_acceptance_title_toggle'] == 'on' ) {   ?>
								<span class="ephd-hd__contact-form-field__label-title">
									<span class="ephd-hd-acceptance-title"><?php echo esc_html( $this->widget_config['contact_acceptance_title'] ); ?></span>
									<span class="ephd-hd__contact-form-field__required-tag">*</span>
								</span>    <?php
						}   ?>
							<label class="ephd-hd-acceptance-label" for="ephd-hd__contact-form-acceptance">
								<input name="acceptance" type="checkbox" value="1" required id="ephd-hd__contact-form-acceptance" placeholder="">   <?php
								if ( $this->widget_config['contact_acceptance_title_toggle'] == 'off' ) {   ?>
									<span class="ephd-hd__contact-form-field__required-tag">*</span>    <?php
								}   ?>
								<span class="ephd-hd__contact-form-field__label-text"><?php echo wp_kses( $this->widget_config['contact_acceptance_text'], $widget_specs['contact_acceptance_text']['allowed_tags'] ); ?></span>
							</label>
						</div>  <?php
					}   ?>

					<div class="ephd-hd__contact-form-btn-wrap">
						<div class="ephd-hd__contact-form-error"></div>
						<input type="submit" name="submit" value="<?php echo esc_attr( $this->widget_config['contact_button_title'] ); ?>" class="ephd-hd__contact-form-btn">
					</div>

				</div>
			</form>
		</div>		<?php
	}

	/**
	 * Display Sub Heading if FAQs tab and Search option are enabled
	 */
	private function display_sub_heading_if_enabled() {

		if ( ! $this->display_faqs && $this->widget_config['search_option'] != 'show_search' ) {
			return;
		}   ?>

		<!-- SUB HEADING -->
		<div class="ephd-hd-header__sub-heading-container">

			<!-- BREADCRUMB -->
			<div class="ephd-hd-sub-heading__breadcrumb-container">
				<div class="ephd-hd-sub-heading__breadcrumb-wrap">

					<div class="ephd-hd__faq__back-btn">
						<div class="ephd-hd__faq__back-btn__icon ephdfa ephdfa-arrow-left"></div>
						<div class="ephd-hd__faq__back-btn__text"><?php esc_html_e( $this->widget_config['article_back_button_text'] ); ?></div>
					</div>

					<nav class="ephd-hd__breadcrumb__nav" aria-label="Breadcrumb">
						<ol>
							<li>
								<span id="ephd-hd__breadcrumb__home" class="ephd-hd__breadcrumb_text" data-ephd-breadcrumb="home"><?php echo esc_html( $this->widget_config['breadcrumb_home_text'] ); ?></span>
								<span  aria-hidden="true" id="ephd-search-home-arrow" class=" ephd-hd-faq__header__title-arrow ephdfa ephdfa-caret-right"></span>
							</li>
							<li>
								<span id="ephd-hd__breadcrumb__search-results" class="ephd-hd__breadcrumb_text" data-ephd-breadcrumb="search_results"><?php echo esc_html( $this->widget_config['breadcrumb_search_result_text'] ); ?></span>
								<span aria-hidden="true" id="ephd-search-result-arrow" class=" ephd-hd-faq__header__title-arrow ephdfa ephdfa-caret-right"></span>
							</li>
							<li>
								<span id="ephd-hd__breadcrumb__article" class="ephd-hd__breadcrumb_text" data-ephd-breadcrumb="article"><?php echo esc_html( $this->widget_config['breadcrumb_article_text'] ); ?></span>
							</li>
						</ol>
					</nav>

				</div>
			</div>  <?php

			$this->display_search_tab(); ?>
		</div>  <?php
	}

	/**
	 * Display List Questions in Help Dialog or No FAQs message
	 */
	private function display_faqs_list_or_no_faqs_message() {

		$faqs_db_handler = new EPHD_FAQs_DB();
		$questions       = $faqs_db_handler->get_faqs_by_ids( $this->widget_config['faqs_sequence'] );    ?>

		<!-- FAQ Wrap -->
		<div class="ephd-hd-faq__faqs-container<?php echo ( empty( $questions ) || is_wp_error( $questions ) ) ? ' ephd-hd-faq__faqs-container--no-faqs' : ''; ?>">  <?php

			// No Questions found or error
		if ( empty( $questions ) || is_wp_error( $questions ) ) {    ?>

				<div class="ephd-hd__no-questions-set">
					<div><img src="<?php echo esc_url( Echo_Help_Dialog::$plugin_url . 'img/no-faqs-defined.jpeg' ); ?>" alt="No FAQs Defined" /></div>     <?php
					if ( ! empty( $this->widget_config['search_instruction_text'] ) ) { ?>
							<span class="ephd-hd__contact-us__message"><?php echo wp_kses_post( wp_unslash( $this->widget_config['search_instruction_text'] ) ); ?></span>     <?php
					}
					if ( ! empty( $this->widget_config['no_result_contact_us_text'] ) ) { ?>
							<button class="ephd-hd__contact-us__link" data-ephd-target-tab="contact"><?php echo wp_kses_post( wp_unslash( $this->widget_config['no_result_contact_us_text'] ) ); ?></button>
							<?php
					} ?>
				</div>      <?php

				// Display Questions
		} else {
			foreach ( $this->widget_config['faqs_sequence'] as $question_id ) {
				foreach ( $questions as $question ) {
					if ( ! empty( $question->faq_id ) && $question_id == $question->faq_id ) {
						$faq_item_html_escaped = EPHD_FAQs_Page::get_faq_item_html( $question->faq_id, $question->question, $question->answer );
						echo $faq_item_html_escaped;
						break;
					}
				}
			}
		}   ?>

		</div>  <?php
	}

	/**
	 * Display Search Input if Search option is enabled
	 */
	private function display_search_input_box_if_enabled() {

		if ( $this->widget_config['search_option'] != 'show_search' ) {
			return;
		}

		global $post;   ?>

		<!-- Search Box -->
		<div class="ephd-hd-search-container">

			<!----- Search Box ------>
			<div class="ephd-hd__search-box">
				<div class="ephd-hd__search-box__search-label"><?php echo esc_html( $this->widget_config['search_input_label'] ); ?></div>
				<form id="ephd-hd__search-form" method="post" action="" onSubmit="return false;">
					<input type="text" id="ephd-hd__search-terms" name="ephd-hd__search-terms" value=""
							placeholder="<?php echo esc_attr( $this->widget_config['search_input_placeholder'] ); ?>"
							data-ephd-location-id="<?php echo empty( $post ) ? 0 : esc_attr( $post->ID ); ?>"
							maxlength="<?php echo esc_attr( EPHD_Search::SEARCH_INPUT_LENGTH ); ?>"
							data-ephd-widget-id="<?php echo esc_attr( $this->widget_config['widget_id'] ); ?>" autocomplete="off"/>
					<div class="ephd-hd__search-tooltip">
						<div class="ephd-hd__search-tooltip__header"><?php
							esc_html_e( 'Search Guideline', 'help-dialog' ); ?>
						</div>
						<div class="ephd-hd__search-tooltip__body">
							<p><?php esc_html_e( 'Use up to three keywords instead of using a full sentence for the best search results.', 'help-dialog' ); ?></p>
							<div class="ephd-hd__search-tooltip__body--columns">
								<div class="ephd-hd__search-tooltip__body--left"><?php esc_html_e( 'Examples:', 'help-dialog' ); ?></div>
								<div class="ephd-hd__search-tooltip__body--right">
									<p><?php esc_html_e( 'product', 'help-dialog' ); ?></p>
									<p><?php esc_html_e( 'product warranty', 'help-dialog' ); ?></p>
									<p><?php esc_html_e( 'free shipping offer', 'help-dialog' ); ?></p>
								</div>
							</div>
						</div>
					</div>
					<span class="ephd-hd__search-terms__icon ephdfa ephdfa-search"></span>
				</form>
			</div>
		</div> <?php
	}

	/**
	 * Display Search Tab
	 */
	private function display_search_tab() {  ?>

		<!-- SEARCH TAB CONTAINER -->
		<div id="ephd-hd-search-results__tab-container">

			<!-- FAQs Tab -->
			<span id="ephd-hd__search-results-faqs-tab" class="ephd-hd-results__tab ephd-hd-results__tab--active" data-ephd-tab="faq" tabindex="0">
				<span class="ephd-hd-results__tab__text"><?php echo esc_html( $this->widget_config['found_faqs_tab_text'] ); ?></span>
				<span class="ephd-hd-results__tab--active__icon"></span>
			</span> <?php

			if ( $this->widget_config['search_kb'] != 'off' ) {   ?>
				<!-- Articles Tab -->
				<span id="ephd-hd__search-results-articles-tab" class="ephd-hd-results__tab" data-ephd-tab="articles" tabindex="0">
					<span class="ephd-hd-results__tab__text"><?php echo esc_html( $this->widget_config['found_articles_tab_text'] ); ?></span>
					<span class="ephd-hd-results__tab--active__icon"></span>
				</span> <?php
			}

			if ( $this->widget_config['search_posts'] == 'on' ) {   ?>
				<!-- Post Tab -->
				<span id="ephd-hd__search-results-post-tab" class="ephd-hd-results__tab" data-ephd-tab="post" tabindex="0">
					<span class="ephd-hd-results__tab__text"><?php echo esc_html( $this->widget_config['found_posts_tab_text'] ); ?></span>
					<span class="ephd-hd-results__tab--active__icon"></span>
				</span> <?php
			}   ?>

		</div>  <?php
	}

	/**
	 * Display Search Results box if Search option is enabled
	 */
	private function display_search_result_box_if_enabled() {

		if ( $this->widget_config['search_option'] != 'show_search' ) {
			return;
		}   ?>

		<!-- Search Results Container -->
		<div class="ephd-hd-kb__search-results-container">

			<!----- Search Box Results ------>
			<div class="ephd-hd-search-results-container">

				<!-- Tab Content Container -->
				<div class="ephd-hd-search-results__tab-content-container">

					<div id="ephd-hd__search_results__faqs" class="ephd-hd-results__tab-content ephd-hd-search-results__faqs-list" data-ephd-tab-content="faq"></div>

					<div id="ephd-hd__search_results__articles" class="ephd-hd-results__tab-content ephd-hd-search-results__article-list" data-ephd-tab-content="articles"></div>

					<div id="ephd-hd__search_results__posts" class="ephd-hd-results__tab-content ephd-hd-search-results__post-list" data-ephd-tab-content="post"></div>

				</div>

				<div id="ephd-hd__search_results__errors"></div>

				<div id="ephd-hd__search_results-cat-article-details" class="ephd-hd__search_step">

					<div class="ephd-hd_article-item-details">
						<div id="ephd-hd_article-desc_excerpt" class="ephd-hd_article-desc"></div>
					</div>

					<div class="ephd-hd_article-item-footer">
						<a class="ephd-hd_article-link" target="_blank"><?php esc_html_e( $this->widget_config['article_read_more_text'] ); ?></a>
					</div>

				</div>

			</div>

		</div> <?php
	}

	/**
	 * Display Header for Search Results if FAQs tab and Search are enabled
	 */
	private function display_search_results_header_if_enabled() {

		if ( ! $this->display_faqs || $this->widget_config['search_option'] != 'show_search' ) {
			return;
		}   ?>

		<div id="ephd-hd-header__search-container">
			<div class="ephd-hd__search-back-btn ephdfa ephdfa-angle-left"></div>
			<div class="ephd-hd__search-text"><?php echo esc_html( $this->widget_config['search_results_title'] ); ?></div>
		</div>  <?php
	}

	/**
	 * Display Powered By box
	 *
	 * @return false|string
	 */
	private function display_powered_by_box() {
		// Hide powered by text
		if ( $this->widget_config['launcher_powered_by'] == 'hide' ) {
			return '';
		}

		ob_start();  ?>
		<span class="ephd-hd-footer__poweredBy"><?php esc_html_e( 'Powered By', 'help-dialog' ); ?></span>
		<img class="ephd-hd-footer__icon" src="<?php echo esc_url( Echo_Help_Dialog::$plugin_url . 'img/HD-logo-footer-light.png' ); ?>" alt="">
		<a class="ephd-hd-footer__link" href="https://www.helpdialog.com/" target="_blank"><?php esc_html_e( 'Help Dialog Chat', 'help-dialog' ); ?></a>  <?php
		return ob_get_clean();
	}

	/**
	 * Should we count analytics? Exclude admin pages, drafts etc.
	 *
	 * @param $post
	 * @return bool
	 */
	private static function is_count_analytics( $post ) {

		// is admin pages
		if ( is_admin() ) {
			return false;
		}

		// is this published/private post
        if ( empty( $post->post_status ) || ! in_array( $post->post_status, ['publish', 'private'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Return current widget
	 *
	 * @param $is_front_page
	 * @return array|null
	 */
	private static function get_help_dialog_if_can_be_displayed( $is_front_page ) {

		// is this page or post or main page to display the Help Dialog on?
		$post = get_queried_object();

		// woocommerce shop page. Queried object is not WP_Post for woo shop page, so we need special code for edge case
		if ( function_exists( 'is_shop' ) && function_exists( 'wc_get_page_id' ) && is_shop() ) {
			$page_id = wc_get_page_id( 'shop' );
			if ( empty( $page_id ) || $page_id < 1 ) {
				return null;
			}
			$post = get_post( $page_id );
		}

		if ( ! $is_front_page && ( empty( $post ) || ( get_class( $post ) != 'WP_Post' && get_class( $post ) != 'WP_Post_Type' && get_class( $post ) != 'WP_Term' ) ) ) {
			return null;
		}

		if ( ! empty( $post ) && get_class( $post ) == 'WP_Term' ) {
			$post_type = 'taxonomy';
			$key       = '';
		} elseif ( $is_front_page ) {
			$post_type = 'page';
			$key       = 0; // 'Home Page' is always signed to '0' ID as it is not dependent to any page ID
		} elseif ( $post->post_type == 'post' || $post->post_type == 'page' ) {
			$post_type = $post->post_type;
			$key       = empty( $post->ID ) ? '' : $post->ID;
		} else {
			$post_type = 'cpt';
			$key       = $post->post_type;
		}

		// try to match by CPT or post id
		$matching_widget = EPHD_Core_Utilities::get_widget_by_page( $key, $post_type );

		// did we find matching post or page
		if ( empty( $matching_widget ) ) {
			return null;
		}

		// hide HD set as Draft (except required capability)
		$required_capability = EPHD_Admin_UI_Access::get_context_required_capability( ['admin_ephd_access_admin_pages_read'] );
		if ( ! empty( $matching_widget['widget_status'] ) && $matching_widget['widget_status'] == EPHD_Help_Dialog_Handler::HELP_DIALOG_STATUS_DRAFT && ! current_user_can( $required_capability ) ) {
			return null;
		}

		return $matching_widget;
	}

	/**
	 * Insert public inline styles for all designs
	 *
	 * @param array $global_config
	 * @param array $widget_config
	 * @param bool  $is_demo
	 * @param bool  $output_all
	 * @return string|void
	 */
	public static function insert_widget_inline_styles( $global_config=[], $widget_config=[], $is_demo=false, $output_all=false ) {

		if ( empty( $global_config ) ) {
			$global_config = ephd_get_instance()->global_config_obj->get_config();
		}

		// if widget should not be displayed then don't insert inline styles
		if ( ! $output_all && empty( $widget_config ) ) {
			$is_front_page = is_front_page();
			$widget_config = self::get_help_dialog_if_can_be_displayed( $is_front_page );
			if ( empty( $widget_config ) ) {
				return;
			}
		}

		// add all Widgets design for admin
		if ( $output_all ) {
			$widgets_config = ephd_get_instance()->widgets_config_obj->get_config();
			// frontend - add only style for particular widget
		} else {
			$widgets_config = [$widget_config];
		}

		$all_designs_style_in = '';
		foreach ( $widgets_config as $widget_config ) {
			$all_designs_style_in .= self::get_design_inline_styles( $global_config, $widget_config );
			$all_designs_style_in .= self::get_position_inline_styles( $global_config, $widget_config );
		}

		// PRO can add design
		$all_designs_style = apply_filters( 'ephd_help_dialog_view_designs_style', $all_designs_style_in, [ 'global_config' => $global_config ] );
		if ( empty( $all_designs_style ) ) {
			$all_designs_style = $all_designs_style_in;
		}

		// Add article hidden classes if kb core enabled
		if ( EPHD_KB_Core_Utilities::is_kb_or_amag_enabled() ) {
			$hidden_element_classes = self::get_hidden_element_classes_inline_styles( $global_config );
			$hidden_element_classes = EPHD_Utilities::minify_css( $hidden_element_classes );
			wp_add_inline_style( 'ephd-user-defined-values', $hidden_element_classes );
		}

		if ( $is_demo ) {
			return $all_designs_style;
		}

		$all_designs_style = EPHD_Utilities::minify_css( $all_designs_style );
		wp_add_inline_style( 'ephd-public-styles', $all_designs_style );
	}

	/**
	 * Get CSS for certain Widget
	 *
	 * @param $global_config
	 * @param $widget_config
	 * @return string
	 */
	private static function get_design_inline_styles( $global_config, $widget_config ) {

		$widget_id = $widget_config['widget_id'];

		$display_chat = ! empty( $widget_config ) && EPHD_Utilities::is_hd_pro_chat_available() && in_array( 'chat', array( $widget_config['tabs_position_1'], $widget_config['tabs_position_2'], $widget_config['tabs_position_3'], $widget_config['tabs_position_4'] ) );
		$display_faqs = ! empty( $widget_config ) && in_array( 'faqs', array( $widget_config['tabs_position_1'], $widget_config['tabs_position_2'], $widget_config['tabs_position_3'], $widget_config['tabs_position_4'] ) );
		$display_resource = ! empty( $widget_config ) && in_array( 'resource', array( $widget_config['tabs_position_1'], $widget_config['tabs_position_2'], $widget_config['tabs_position_3'], $widget_config['tabs_position_4'] ) );
		$display_contact = ! empty( $widget_config ) && in_array( 'contact', array( $widget_config['tabs_position_1'], $widget_config['tabs_position_2'], $widget_config['tabs_position_3'], $widget_config['tabs_position_4'] ) );

		$tabs_state = array_count_values( array_map( 'intval', [ $display_chat, $display_faqs, $display_resource, $display_contact ] ) );

		return
			'#ephd-help-dialog.ephd-widget--' . $widget_id . ' {
				background-color: ' . $widget_config['background_color'] . ';
			}' .

			/* Display styles only if more than one tabs are enabled */
			( isset( $tabs_state[1] ) || $tabs_state[1] > 1 ?
				'#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-tab {
					background-color: ' . $widget_config['not_active_tab_color'] . ';
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-tab--active {
					background-color: ' . $widget_config['background_color'] . ';
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-tab__chat-btn__text,
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-tab__resource-btn__text,
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-tab__faq-btn__text,
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-tab__contact-btn__text {
					color: ' . $widget_config['tab_text_color'] . ';
				}'
				: '' ) . '

			/* Launcher */
			.ephd-hd-toggle.ephd-widget--' . $widget_id . ' .ephd-hd-toggle__icon {
				background-color: ' . $widget_config['launcher_background_color'] . ';
			}
			.ephd-hd-toggle.ephd-widget--' . $widget_id . ':hover .ephd-hd-toggle__icon {
				background-color: ' . $widget_config['launcher_background_hover_color'] . ';
			}
			.ephd-hd-toggle.ephd-widget--' . $widget_id . ' .ephd-hd-toggle__icon:before {
				color: ' . $widget_config['launcher_icon_color'] . ';
			}
			.ephd-hd-toggle.ephd-widget--' . $widget_id . ':hover .ephd-hd-toggle__icon:before {
				color: ' . $widget_config['launcher_icon_hover_color'] . ';
			}

			/* General*/
			#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-header__welcome {
				color: ' . $widget_config['welcome_title_color'] . ';
			}
            #ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-header__welcome a {
				color: ' . $widget_config['welcome_title_link_color'] . ';
			}
			#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-header__logo {
				width: ' . $global_config['logo_image_width'] . 'px !important;
			}' .

			/* Display styles only if FAQs tab and Search are enabled */
			( $display_faqs && $widget_config['search_option'] == 'show_search' ?
				'/* Sub Heading */
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__breadcrumb__nav {
					color: ' . $widget_config['breadcrumb_color'] . ';
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-sub-heading__breadcrumb-container {
					background-color: ' . $widget_config['breadcrumb_background_color'] . ';
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__breadcrumb__nav .ephd-hd-faq__header__title-arrow {
					color: ' . $widget_config['breadcrumb_arrow_color'] . ';
				}

				/* Back Navigation */
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__faq__back-btn .ephd-hd__faq__back-btn__icon,
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__faq__back-btn .ephd-hd__faq__back-btn__text {
					color: ' . $widget_config['back_text_color'] . ';
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__faq__back-btn:hover .ephd-hd__faq__back-btn__icon,
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__faq__back-btn:hover .ephd-hd__faq__back-btn__text {
					color: ' . $widget_config['back_text_color_hover_color'] . ';
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__faq__back-btn {
					background-color: ' . $widget_config['back_background_color'] . ';
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__faq__back-btn:hover {
					background-color: ' . $widget_config['back_background_color_hover_color'] . ';
				}

				/* Search results tab indicator */
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-results__tab--active__icon {
	                background-color: ' . $widget_config['background_color'] . ';
	            }

				/* Search Results */
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__search-text, 
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__search-back-btn{
					color: ' . $widget_config['main_title_text_color'] . ';
					font-size: ' . $global_config['main_title_font_size'] . 'px !important;
				}

				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-results__tab {
					color: ' . $widget_config['found_faqs_article_tab_color'] . ' !important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-results__tab--active {
					color: ' . $widget_config['found_faqs_article_active_tab_color'] . ' !important;
				}

				#ephd-help-dialog #ephd-hd__search_results__articles .ephd-hd_article-item__text,
				#ephd-help-dialog #ephd-hd__search_results__posts .ephd-hd_article-item__text {
					color: ' . $widget_config['article_post_list_title_color'] . ' !important;
				}
				#ephd-help-dialog #ephd-hd__search_results__articles .ephd-hd_article-item__icon,
				#ephd-help-dialog #ephd-hd__search_results__posts .ephd-hd_article-item__icon {
					color: ' . $widget_config['article_post_list_icon_color'] . ' !important;
				}
				
				/* Single Article */
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' #ephd-hd__search_results-cat-article-details .ephd-hd_article-link {
					color: ' . $widget_config['single_article_read_more_text_color'] . '!important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' #ephd-hd__search_results-cat-article-details .ephd-hd_article-link:hover {
					color: ' . $widget_config['single_article_read_more_text_hover_color'] . '!important;
				}
				
				/* Excerpt */
				#ephd-help-dialog .ephd-hd-article__excerpt-container .ephd-hd_article-title {
					color: #000 !important;
				}
				#ephd-help-dialog .ephd-hd-article__excerpt-container .ephd-hd-excerpt__body,
				#ephd-help-dialog .ephd-hd-article__excerpt-container .ephd-hd-excerpt__body * {
					color: #000 !important;
				}'
				: '' ) .

			/* Display styles only if FAQs is enabled */
			( $display_faqs ?
				'/* FAQs */
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-faq__list__item-container {
					border-color: ' . $widget_config['faqs_qa_border_color'] . ' !important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__question {
					color: ' . $widget_config['faqs_question_text_color'] . ' !important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__element--active .ephd-hd__item__question {
					color: ' . $widget_config['faqs_question_active_text_color'] . ' !important;
				}

	            /* FAQs - Answer Text Colors */	    
	            #ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__answer__text h1,
	            #ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__answer__text h2,
	            #ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__answer__text h3,
	            #ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__answer__text h4,
	            #ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__answer__text h5,
	            #ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__answer__text h6,
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__answer__text {
					color: ' . $widget_config['faqs_answer_text_color'] . ' !important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__item__answer {
					background-color: ' . $widget_config['faqs_answer_background_color'] . ' !important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-faq__list__item-container {
					background-color: ' . $widget_config['faqs_question_background_color'] . ' !important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__element--active {
					background-color: ' . $widget_config['faqs_question_active_background_color'] . ' !important;
				}'
				: '' ) .

			/* Display styles only if Contact Form is enabled */
			( $display_contact ?
				'/* Contact Form */
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__contact-form-btn {
					background-color: ' . $widget_config['contact_submit_button_color'] . ' !important;
					color: ' . $widget_config['contact_submit_button_text_color'] . ' !important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__contact-form-btn:hover {
					background-color: ' . $widget_config['contact_submit_button_hover_color'] . ' !important;
					color: ' . $widget_config['contact_submit_button_text_hover_color'] . ' !important;
				}'
				. ( $widget_config['contact_acceptance_checkbox'] == 'on' ?
				'#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd__contact-form-acceptance-container {
					background-color: ' . $widget_config['contact_acceptance_background_color'] . ' !important;
				}' : '' )
				: '' ) .
				/* Display styles only if Chat Tab is enabled */
			( $display_resource ?
				( $widget_config['resource_phone_toggle'] == 'on' ?
				'#ephd-hd-resource-phone-number-icon {
					fill: ' . $widget_config['resource_phone_color'] . ';
				}
				#ephd-hd-resource-phone-number-icon:hover {
					fill: ' . $widget_config['resource_phone_hover_color'] . ';
				}

				#ephd-hd-resource-phone-number-name,
				#ephd-hd-resource-link-name {
					color: ' . $widget_config['resource_label_color'] . ';
				}' : '' )

				. ( $widget_config['resource_custom_link_toggle'] == 'on' ?
				'#ephd-hd-resource-link-icon {
					fill: ' . $widget_config['resource_link_color'] . ';
				}
				#ephd-hd-resource-link-icon:hover {
					fill: ' . $widget_config['resource_link_hover_color'] . ';
				}

				' : '' )
			: '' ) . '
			/* --- Mobile Settings ---*/

			/* MOBILE */
			@media only screen and ( max-width: ' . $global_config['mobile_break_point'] . 'px ) {
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' {
					width: 98%;
					margin: 0px 1%;
					right:0 !important;
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' #ephd-hd-body-container {
					/*height: calc(100vh - 286px) !important;*/
				}
				#ephd-help-dialog.ephd-widget--' . $widget_id . ' .ephd-hd-header__welcome__title {
					font-size: 20px !important;
				}
			}';
	}

	/**
	 * Get CSS for dialog position
	 *
	 * @param $global_config
	 * @param $widget_config
	 *
	 * @return string
	 */
	private static function get_position_inline_styles( $global_config, $widget_config ) {

		$is_admin_preview = is_admin();

		// Add Help Dialog bottom position styles
		$styles = $is_admin_preview ? '' : '
        /* HELP DIALOG BOTTOM DISTANCE */
        #ephd-help-dialog.ephd-widget--' . $widget_config['widget_id'] . ' {
            bottom: ' . ( $widget_config['launcher_bottom_distance'] + 80 ) . 'px !important;
        }
        .ephd-hd-toggle.ephd-widget--' . $widget_config['widget_id'] . ' {
            bottom: ' . $widget_config['launcher_bottom_distance'] . 'px !important;
        }';

		// If Initial Message is Active, display CSS
		if ( $widget_config['initial_message_toggle'] == 'show' ) {
			$styles .= '
			.ephd-widget--' . $widget_config['widget_id'] . ' .ephd-hd__initial-message {
                bottom: ' . ( $widget_config['launcher_bottom_distance'] + 80 ) . 'px !important;
            }
			';
		}

		// Add Help Dialog left position styles
		if ( $widget_config['launcher_location'] == 'left' ) {
			$styles .= '
	        /* HELP DIALOG LOCATION */
	        #ephd-help-dialog, .ephd-hd-toggle {
	            right: unset !important;
	            left: 20px;
	        }';

			$styles .= '
	        .ephd-hd-toggle .ephd-hd-toggle__draft-notification {
	            left: 0px;
	        }
	        .ephd-hd-toggle .ephd-hd-toggle__draft-notification:after {
                left: 25px;
	        }
	        .ephd-hd__initial-message {
	            left: 20px;
	        }';

			$styles .= $is_admin_preview ? '' : '
	        /* MOBILE HELP DIALOG LOCATION */
            @media only screen and ( max-width: ' . $global_config['mobile_break_point'] . 'px ) {
                #ephd-help-dialog {
                    left: 0 !important;                
                }
            }';
		}

		return $styles;
	}

	/**
	 * Get CSS for hidden Help Dialog elements
	 *
	 * @param $global_config
	 *
	 * @return string
	 */
	private static function get_hidden_element_classes_inline_styles( $global_config ) {

		if ( empty( $global_config['kb_article_hidden_classes'] ) ) {
			return '';
		}

		$classes = explode( ',', $global_config['kb_article_hidden_classes'] );

		// Parent element selector (iframe article body)
		$parent_selector = 'body.ephd-hd_article-desc__body';

		// sanitize classes string
		$result_classes = array();
		foreach ( $classes as $class ) {
			$text  = EPHD_Utilities::sanitize_english_text( $class );
			$class = trim( $text );
			if ( ! empty( $class ) && ! is_numeric( $class ) ) {
				$result_classes[] = $parent_selector . ' .' . strtolower( $class );
			}
		}

		if ( empty( $result_classes ) ) {
			return '';
		}

		$result_selector = implode( ', ', $result_classes );

		return '
        /* CUSTOM HIDDEN CLASSES */
        ' . $result_selector . ' {
            display: none !important;
        }';
	}

	/**
	 * TODO future: unused CSS classes until we enable KB articles
	 * Return inline styles for article details shown in iframe (defined in configs)
	 *
	 * @return string
	 */
	/*
	public function get_public_article_details_styles() {

		return
			'
			.ephd-hd_article-desc__body.ephd-widget--' . $this->design_configs['widget_id'] . ',
			.ephd-hd_article-desc__body.ephd-widget--' . $this->design_configs['widget_id'] . ' p{
				color: ' . $this->design_configs['single_article_desc_color'] . ' !important;
			}';
	}*/
}
