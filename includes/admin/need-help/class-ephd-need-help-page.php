<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Need Help? admin page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPHD_Need_Help_Page {

	/**
	 * Display Need Help page
	 */
	public function display_need_help_page() {

		if ( ! current_user_can( EPHD_Admin_UI_Access::get_context_required_capability( ['admin_ephd_access_admin_pages_read'] ) ) ) {
			echo '<p>' . esc_html__( 'You do not have permission to edit Help Dialog.', 'help-dialog' ) . '</p>';
			return;
		}

		$admin_page_views = $this->get_regular_views_config();

		EPHD_HTML_Admin::admin_page_css_missing_message( true );   ?>

		<div id="ephd-admin-page-wrap">

			<div class="ephd-get-started-page-container">				<?php

				/**
				 * ADMIN HEADER (HD logo and list of HDs dropdown)
				 */
				EPHD_HTML_Admin::admin_header();

				/**
				 * ADMIN TOOLBAR
				 */
				EPHD_HTML_Admin::admin_toolbar( $admin_page_views );

				/**
				 * ADMIN SECONDARY TABS
				 */
				EPHD_HTML_Admin::admin_secondary_tabs( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPHD_HTML_Admin::admin_settings_tab_content( $admin_page_views );    ?>

				<div class="ephd-bottom-notice-message"></div>

			</div>

		</div>	    <?php
	}

	/**
	 * Get configuration for regular views
	 *
	 * @return array
	 */
	private function get_regular_views_config() {

		return array(

			// VIEW: Getting Started
			array(

				// Shared
				'active' => true,
				'list_key' => 'getting-started',

				// Top Panel Item
				'label_text' => esc_html__( 'Get Started', 'help-dialog' ),
				'icon_class' => 'ephdfa ephdfa-play',

				// Boxes List
				'boxes_list' => array(

					// Box: Getting Started
					array(
						'html' => $this->getting_started_tab(),
					),
				),
			),

			// VIEW: Features
			EPHD_Need_Help_Features::get_page_view_config(),

			// VIEW: Our Free Plugins
			array(

				// Shared
				'list_key' => 'our-free-plugins',

				// Top Panel Item
				'label_text' => esc_html__( 'Our Free Plugins', 'help-dialog' ),
				'icon_class' => 'ephdfa ephdfa-download',

				// Boxes List
				'boxes_list' => self::get_our_free_plugins_boxes(),
			),

			// VIEW: Contact Us
			EPHD_Need_Help_Contact_Us::get_page_view_config(),
		);
	}

	/**
	 * Get content for Getting Started tab
	 *
	 * @return false|string
	 */
	private function getting_started_tab() {

		foreach ( ephd_get_instance()->widgets_config_obj->get_config() as $widget ) {

			$widget_url = '';
			$widget_url = EPHD_Core_Utilities::get_first_widget_page_url( $widget );
			if ( empty( $widget_url ) ) {
				continue;
			}
			break;
		}

		ob_start();     ?>

		<div class="ephd-nh__getting-started-container">

            <!-- header -->
            <div class="ephd-nh__gs__header-container">

                <div class="ephd-nh__header__img">
                    <img src="<?php echo esc_url( Echo_Help_Dialog::$plugin_url . 'img/need-help/HD-Banner-1000x324.jpg' ); ?>">
                </div>

                <div class="ephd-nh__header__text">
                    <h2 class="ephd-nh__header__title"><?php esc_html_e( 'Welcome to Help Dialog Chat!', 'help-dialog' ); ?></h2>
	                <p class="ephd-nh__header__desc"><?php esc_html_e( 'Choose pages to show Help Dialog widgets and define the FAQs for each page.', 'help-dialog' ); ?></p>
                </div>

				<div class="ephd-nh__gs__body-container">	            <?php

		            EPHD_HTML_Forms::call_to_action_box( array(
			            'container_class'   => '',
			            'style' => 'style-1',
			            'title'         => esc_html__( 'Start with configuring Widgets to show on your pages.', 'help-dialog' ),
			            'btn_text'      => esc_html__( 'Configure Widgets', 'help-dialog' ),
			            'btn_url'       => esc_url( admin_url( 'admin.php?page=ephd-help-dialog-widgets' ) ),
			            'btn_target'    => '__blank',
		            ) );     ?>
				</div>
            </div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Get Our Free Plugins boxes
	 *
	 * @return array[]
	 */
	private static function get_our_free_plugins_boxes() {

		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		remove_all_filters( 'plugins_api' );

		$our_free_plugins = array();

		$args_list = array(
			array( 'slug' => 'echo-knowledge-base' ),
			array( 'slug' => 'creative-addons-for-elementor' ),
			array( 'slug' => 'echo-show-ids' ),
			array( 'slug' => 'scroll-down-arrow' ),
		);

		foreach( $args_list as $args ) {
			$args['fields'] = [
				'short_description' => true,
				'icons'             => true,
				'reviews'           => false,
				'banners'           => true,
			];
			$plugin_data = plugins_api( 'plugin_information', $args );
			if ( $plugin_data && ! is_wp_error( $plugin_data ) ) {
				$our_free_plugins[] = $plugin_data;
			}
		}

		ob_start(); ?>
		<div class="wrap recommended-plugins">
			<div class="wp-list-table widefat plugin-install">
				<div class="the-list">  <?php

					foreach( $our_free_plugins as $plugin ) {
						self::display_our_free_plugin_box_html( $plugin );
					}   ?>

				</div>
			</div>
		</div>  <?php

		$boxes_html = ob_get_clean();

		return array(
			array(
				'html' => $boxes_html,
			) );
	}

	/**
	 * Return HTML for a single box on Our Free Plugins tab
	 *
	 * @param $plugin
	 */
	private static function display_our_free_plugin_box_html( $plugin ) {

		$links_allowed_tags = array(
			'a' => array(
				'id'		=> true,
				'class'		=> true,
				'href'		=> true,
				'title'		=> true,
				'target'	=> true,
				'aria-*'	=> true,
				'data-*'	=> true,
			),
			'button' => array(
				'id'		=> true,
				'class'		=> true,
				'disabled'	=> true,
				'type'		=> true,
			),
		);
		$plugins_allowed_tags = array_merge_recursive( $links_allowed_tags, array(
			'abbr'		=> array( 'title' => true ),
			'acronym'	=> array( 'title' => true ),
			'code'		=> array(),
			'pre'		=> array(),
			'em'		=> array(),
			'strong'	=> array(),
			'ul'		=> array(),
			'ol'		=> array(),
			'li'		=> array(),
			'p'			=> array(),
			'br'		=> array(),
			'cite'		=> array(),
		) );

		if ( is_object( $plugin ) ) {
			$plugin = (array) $plugin;
		}

		$title = wp_kses( $plugin['name'], $plugins_allowed_tags );

		// remove any HTML from the description.
		$version = wp_kses( $plugin['version'], $plugins_allowed_tags );

		$name = empty( $plugin['short_description'] ) ? '' : esc_html( wp_strip_all_tags( $title . ' ' . $version ) );

		$author = wp_kses( $plugin['author'], $plugins_allowed_tags );
		if ( ! empty( $author ) ) {
			/* translators: %s: Plugin author. */
			$author = ' <cite>' . sprintf( esc_html__( 'By %s' ), $author ) . '</cite>';
		}

		$requires_php = isset( $plugin['requires_php'] ) ? $plugin['requires_php'] : null;
		$requires_wp  = isset( $plugin['requires'] ) ? $plugin['requires'] : null;

		$compatible_php = is_php_version_compatible( $requires_php );
		$compatible_wp  = is_wp_version_compatible( $requires_wp );
		$tested_wp = empty( $plugin['tested'] ) || version_compare( get_bloginfo( 'version' ), $plugin['tested'], '<=' );

		$details_link = esc_url( self_admin_url(
			'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] .
			'&amp;TB_iframe=true&amp;width=600&amp;height=550'
		) );

		$action_links = self::get_our_free_plugin_action_links( $plugin, $name, $compatible_php, $compatible_wp );

		$action_links[] = sprintf(
			'<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
			esc_url( $details_link ),
			/* translators: %s: Plugin name and version. */
			esc_attr( sprintf( esc_html__( 'More information about %s' ), $name ) ),
			esc_attr( $name ),
			__( 'More Details' )
		);

		if ( ! empty( $plugin['icons']['svg'] ) ) {
			$plugin_icon_url = $plugin['icons']['svg'];
		} elseif ( ! empty( $plugin['icons']['2x'] ) ) {
			$plugin_icon_url = $plugin['icons']['2x'];
		} elseif ( ! empty( $plugin['icons']['1x'] ) ) {
			$plugin_icon_url = $plugin['icons']['1x'];
		} else {
			$plugin_icon_url = $plugin['icons']['default'];
		}

		$action_links = apply_filters( 'plugin_install_action_links', $action_links, $plugin );
		$action_links = empty( $action_links ) || ! is_array( $action_links ) ? array() : $action_links;

		$last_updated_timestamp = strtotime( $plugin['last_updated'] ); ?>

		<div class="plugin-card plugin-card-<?php echo sanitize_html_class( $plugin['slug'] ); ?>"> <?php

			self::display_our_free_plugin_incompatible_links( $compatible_php, $compatible_wp );  ?>

			<div class="plugin-card-top">
				<div class="name column-name">
					<h3>
						<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal">							<?php
							echo esc_html( $title ); ?>
							<img src="<?php echo esc_url( $plugin_icon_url ); ?>" class="plugin-icon" alt="" />
						</a>
					</h3>
				</div>
				<div class="action-links">
					<ul class="plugin-action-buttons">	<?php
						foreach ( $action_links as $one_link ) {	?>
							<li><?php echo wp_kses( $one_link, $links_allowed_tags ); ?></li>	<?php
						}	?>
					</ul>
				</div>
				<div class="desc column-description">
					<p><?php echo ( empty( $plugin['short_description'] ) ? '' : esc_html( wp_strip_all_tags( $plugin['short_description'] ) ) ); ?></p>
					<p class="authors"><?php echo wp_kses( $author, $plugins_allowed_tags ); ?></p>
				</div>
			</div>

			<div class="plugin-card-bottom">
				<div class="vers column-rating">    <?php
					wp_star_rating(
						array(
							'rating' => $plugin['rating'],
							'type'   => 'percent',
							'number' => $plugin['num_ratings'],
						)
					);  ?>
					<span class="num-ratings" aria-hidden="true">(<?php echo esc_html( number_format_i18n( $plugin['num_ratings'] ) ); ?>)</span>
				</div>
				<div class="column-updated">
					<strong><?php esc_html_e( 'Last Updated:' ); ?></strong>    <?php
					/* translators: %s: Human-readable time difference. */
					printf( esc_html__( '%s ago' ), human_time_diff( $last_updated_timestamp ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped   ?>
				</div>
				<div class="column-downloaded"> <?php
					if ( $plugin['active_installs'] >= 1000000 ) {
						$active_installs_millions = floor( $plugin['active_installs'] / 1000000 );
						$active_installs_text     = sprintf(
						/* translators: %s: Number of millions. */
							_nx( '%s+ Million', '%s+ Million', $active_installs_millions, 'Active plugin installations' ),
							number_format_i18n( $active_installs_millions )
						);
					} elseif ( 0 == $plugin['active_installs'] ) {
						$active_installs_text = _x( 'Less Than 10', 'Active plugin installations' );
					} else {
						$active_installs_text = number_format_i18n( $plugin['active_installs'] ) . '+';
					}
					/* translators: %s: Number of installations. */
					printf( esc_html__( '%s Active Installations' ), esc_html( $active_installs_text ) );   ?>
				</div>
				<div class="column-compatibility">  <?php
					if ( ! $tested_wp ) {   ?>
						<span class="compatibility-untested"><?php esc_html_e( 'Untested with your version of WordPress' ); ?></span>   <?php
					} elseif ( ! $compatible_wp ) { ?>
						<span class="compatibility-incompatible"><?php esc_html_e( 'Incompatible with your version of WordPress' ); ?></span>   <?php
					} else {    ?>
						<span class="compatibility-compatible"><?php esc_html_e( 'Compatible with your version of WordPress' ); ?></span>   <?php
					}   ?>
				</div>
			</div>
		</div>  <?php
	}

	/**
	 * Display links in case if suggested plugin is incompatible with current WordPress or PHP version
	 *
	 * @param $compatible_php
	 * @param $compatible_wp
	 */
	private static function display_our_free_plugin_incompatible_links( $compatible_php, $compatible_wp ) {

		if ( $compatible_php && $compatible_wp ) {
			return;
		}   ?>

		<div class="notice inline notice-error notice-alt"><p>  <?php

			if ( ! $compatible_php && ! $compatible_wp ) {
				esc_html_e( 'This plugin doesn&#8217;t work with your versions of WordPress and PHP.' );
				if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
					/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
					printf(
						' ' . esc_html__( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
						esc_url( self_admin_url( 'update-core.php' ) ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				} elseif ( current_user_can( 'update_core' ) ) {
					printf(
					/* translators: %s: URL to WordPress Updates screen. */
						' ' . esc_html__( '<a href="%s">Please update WordPress</a>.' ),
						esc_url( self_admin_url( 'update-core.php' ) )
					);
				} elseif ( current_user_can( 'update_php' ) ) {
					printf(
					/* translators: %s: URL to Update PHP page. */
						' ' . esc_html__( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				}
			} elseif ( ! $compatible_wp ) {
				esc_html_e( 'This plugin doesn&#8217;t work with your version of WordPress.' );
				if ( current_user_can( 'update_core' ) ) {
					printf(
					/* translators: %s: URL to WordPress Updates screen. */
						' ' . esc_html__( '<a href="%s">Please update WordPress</a>.' ),
						esc_url( self_admin_url( 'update-core.php' ) )
					);
				}
			} elseif ( ! $compatible_php ) {
				__( 'This plugin doesn&#8217;t work with your version of PHP.' );
				if ( current_user_can( 'update_php' ) ) {
					printf(
					/* translators: %s: URL to Update PHP page. */
						' ' . esc_html__( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				}
			}   ?>

		</p></div>  <?php
	}

	/**
	 * Get action links for single plugin in Our Free Plugins list
	 *
	 * @param $plugin
	 * @param $name
	 * @param $compatible_php
	 * @param $compatible_wp
	 * @return array
	 */
	private static function get_our_free_plugin_action_links( $plugin, $name, $compatible_php, $compatible_wp ) {

		$action_links = [];

		if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'update_plugins' ) ) {
			return $action_links;
		}

		$status = install_plugin_install_status( $plugin );

		// not installed
		if ( $status['status'] == 'install' && $status['url'] ) {

			$action_links[] = $compatible_php && $compatible_wp
				? sprintf(
					'<a class="install-now button" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
					esc_attr( $plugin['slug'] ),
					esc_url( $status['url'] ),
					/* translators: %s: Plugin name and version. */
					esc_attr( sprintf( _x( 'Install %s now', 'plugin' ), $name ) ),
					esc_attr( $name ),
					__( 'Install Now' ) )
				: sprintf(
					'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
					_x( 'Cannot Install', 'plugin' ) );
		}

		// update is available
		if ( $status['status'] == 'update_available' && $status['url'] ) {

			$action_links[] = $compatible_php && $compatible_wp
				? sprintf(
					'<a class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
					esc_attr( $status['file'] ),
					esc_attr( $plugin['slug'] ),
					esc_url( $status['url'] ),
					/* translators: %s: Plugin name and version. */
					esc_attr( sprintf( _x( 'Update %s now', 'plugin' ), $name ) ),
					esc_attr( $name ),
					__( 'Update Now' ) )
				: sprintf(
					'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
					_x( 'Cannot Update', 'plugin' ) );
		}

		// installed
		if ( $status['status'] == 'latest_installed' || $status['status'] == 'newer_installed' ) {

			if ( is_plugin_active( $status['file'] ) ) {
				$action_links[] = sprintf(
					'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
					_x( 'Active', 'plugin' )
				);

			} elseif ( current_user_can( 'activate_plugin', $status['file'] ) ) {
				$button_text = esc_html__( 'Activate' );
				/* translators: %s: Plugin name. */
				$button_label = _x( 'Activate %s', 'plugin' );
				$activate_url = add_query_arg(
					array(
						'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $status['file'] ),
						'action'   => 'activate',
						'plugin'   => $status['file'],
					),
					network_admin_url( 'plugins.php' )
				);

				if ( is_network_admin() ) {
					$button_text = esc_html__( 'Network Activate' );
					/* translators: %s: Plugin name. */
					$button_label = _x( 'Network Activate %s', 'plugin' );
					$activate_url = add_query_arg( array( 'networkwide' => 1 ), $activate_url );
				}

				$action_links[] = sprintf(
					'<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
					esc_url( $activate_url ),
					esc_attr( sprintf( $button_label, $plugin['name'] ) ),
					$button_text
				);

			} else {
				$action_links[] = sprintf(
					'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
					_x( 'Installed', 'plugin' )
				);
			}
		}

		return $action_links;
	}

}
