<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if plugin upgrade to a new version requires any actions like database upgrade
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPHD_Upgrades {

	public function __construct() {
		// will run after plugin is updated but not always like front-end rendering
		add_action( 'admin_init', array( 'EPHD_Upgrades', 'update_plugin_version' ) );

		// show initial page after install
		add_action( 'admin_init', array( 'EPHD_Upgrades', 'initial_setup' ), 20 );

		// show additional messages on the plugins page
		add_action( 'in_plugin_update_message-help-dialog/echo-help-dialog.php', array( $this, 'in_plugin_update_message' ) );
	}

	/**
	 * Trigger display of wizard setup screen on plugin first activation or upgrade; does NOT work if multiple plugins installed at the same time
	 */
	public static function initial_setup() {

		$hd_version = EPHD_Utilities::get_wp_option( 'ephd_version', null );
		if ( empty( $hd_version ) ) {
			return;
		}

		// return if activating from network or doing bulk activation
		if ( is_network_admin() || isset($_GET['activate-multi']) ) {
			return;
		}

		// if setup ran then do not proceed
		$run_setup = EPHD_Utilities::get_wp_option( 'ephd_run_setup', null );
		if ( empty( $run_setup ) ) {
			return;
		}

		delete_option( 'ephd_run_setup' );

		// create default Widget
		EPHD_Help_Dialog_Handler::add_default_faqs();

		// redirect to Getting Started
		wp_safe_redirect( admin_url( 'admin.php?page=ephd-help-dialog#getting-started' ) );
		exit;
	}

	/**
	 * If necessary run plugin database updates
	 */
	public static function update_plugin_version() {

		// ensure the plugin version and configuration is set
		$last_version = EPHD_Utilities::get_wp_option( 'ephd_version', null );
		if ( empty( $last_version ) ) {
			EPHD_Utilities::save_wp_option( 'ephd_version', Echo_Help_Dialog::$version );
			return;
		}

		$last_upgrade_version = ephd_get_instance()->global_config_obj->get_value( 'upgrade_plugin_version' );
		if ( empty( $last_upgrade_version ) ) {
			$last_upgrade_version = $last_version;
			ephd_get_instance()->global_config_obj->set_value( 'upgrade_plugin_version', $last_upgrade_version );
		}

		// if plugin is up-to-date then return
		if ( version_compare( $last_upgrade_version, Echo_Help_Dialog::$version, '>=' ) ) {
			return;
		}

		// upgrade the plugin
		self::invoke_upgrades( $last_upgrade_version );

		EPHD_Utilities::save_wp_option( 'ephd_version', Echo_Help_Dialog::$version );
	}

	/**
	 * Invoke each database update as necessary.
	 *
	 * @param $last_version
	 */
	private static function invoke_upgrades( $last_version ) {

		self::run_upgrades( $last_version );

		//$global_config = ephd_get_instance()->global_config_obj->get_config();
		//$widgets_config = ephd_get_instance()->widgets_config_obj->get_config();

		// ensure default KB is updated
		ephd_get_instance()->global_config_obj->set_value( 'upgrade_plugin_version', Echo_Help_Dialog::$version );
	}

	public static function run_upgrades( $last_version ) {

		if ( version_compare( $last_version, '2.10.0', '<' ) ) {
			self::upgrade_to_v2_10_0();
		}
	}

	private static function upgrade_to_v2_10_0() {

		$api_key = ephd_get_instance()->global_config_obj->get_value( 'openai_api_key' );

		if ( empty( $api_key ) || ! is_string( $api_key ) ) {
			$api_key = '';
		}
		$api_key = EPHD_Utilities::encrypt_data( $api_key );
		EPHD_Utilities::save_wp_option('ephd_openai_key', $api_key );

		$tabs_sequence = ephd_get_instance()->global_config_obj->get_value( 'tabs_sequence' );
		$widgets_config = ephd_get_instance()->widgets_config_obj->get_config();
		foreach ( $widgets_config as $widget_id => $one_widget ) {

			// refactor tabs sequence to tabs positions
			switch ( $tabs_sequence ) {
				case 'chat_faqs_contact':
				case 'resource_faqs_contact':
				$widgets_config[ $widget_id ]['tabs_position_1'] = 'resource';
					$widgets_config[ $widget_id ]['tabs_position_2'] = 'faqs';
					$widgets_config[ $widget_id ]['tabs_position_3'] = 'contact';
					$widgets_config[ $widget_id ]['tabs_position_4'] = 'none';
					break;
				case 'faqs_chat_contact':
				case 'faqs_resource_contact':
					$widgets_config[ $widget_id ]['tabs_position_1'] = 'faqs';
					$widgets_config[ $widget_id ]['tabs_position_2'] = 'resource';
					$widgets_config[ $widget_id ]['tabs_position_3'] = 'contact';
					$widgets_config[ $widget_id ]['tabs_position_4'] = 'none';
					break;
				default:
					$widgets_config[ $widget_id ]['tabs_position_1'] = 'faqs';
					$widgets_config[ $widget_id ]['tabs_position_3'] = 'contact';
					$widgets_config[ $widget_id ]['tabs_position_4'] = 'none';
					break;
			}

			// refactor display tabs to tabs positions
			for ( $i = 1; $i < 4; $i++ ) {
				switch ( $widgets_config[ $widget_id ]['tabs_position_' . $i] ) {
					case 'resource':
						$widgets_config[ $widget_id ]['tabs_position_' . $i] = $one_widget['display_channels_tab'] == 'on' ? $widgets_config[ $widget_id ]['tabs_position_' . $i] : 'none';
						break;
					case 'faqs':
						$widgets_config[ $widget_id ]['tabs_position_' . $i] = $one_widget['display_faqs_tab'] == 'on' ? $widgets_config[ $widget_id ]['tabs_position_' . $i] : 'none';
						break;
					case 'contact':
						$widgets_config[ $widget_id ]['tabs_position_' . $i] = $one_widget['display_contact_tab'] == 'on' ? $widgets_config[ $widget_id ]['tabs_position_' . $i] : 'none';
						break;
					default:
						break;
				}
			}

			if ( isset( $one_widget['channel_phone_toggle'] ) ) {
				$widgets_config[ $widget_id ]['resource_phone_toggle'] = $one_widget['channel_phone_toggle'];
			}

			if ( isset( $one_widget['channel_phone_country_code'] ) ) {
				$widgets_config[ $widget_id ]['resource_phone_country_code'] = $one_widget['channel_phone_country_code'];
			}

			if ( isset( $one_widget['channel_phone_number'] ) ) {
				$widgets_config[ $widget_id ]['resource_phone_number'] = $one_widget['channel_phone_number'];
			}

			if ( isset( $one_widget['channel_phone_number_image_url'] ) ) {
				$widgets_config[ $widget_id ]['resource_phone_number_image_url'] = $one_widget['channel_phone_number_image_url'];
			}

			if ( isset( $one_widget['channel_custom_link_toggle'] ) ) {
				$widgets_config[ $widget_id ]['resource_custom_link_toggle'] = $one_widget['channel_custom_link_toggle'];
			}

			if ( isset( $one_widget['channel_custom_link_url'] ) ) {
				$widgets_config[ $widget_id ]['resource_custom_link_url'] = $one_widget['channel_custom_link_url'];
			}

			if ( isset( $one_widget['channel_custom_link_image_url'] ) ) {
				$widgets_config[ $widget_id ]['resource_custom_link_image_url'] = $one_widget['channel_custom_link_image_url'];
			}

			if ( isset( $one_widget['channel_phone_color'] ) ) {
				$widgets_config[ $widget_id ]['resource_phone_color'] = $one_widget['channel_phone_color'];
			}

			if ( isset( $one_widget['channel_phone_hover_color'] ) ) {
				$widgets_config[ $widget_id ]['resource_phone_hover_color'] = $one_widget['channel_phone_hover_color'];
			}

			if ( isset( $one_widget['channel_label_color'] ) ) {
				$widgets_config[ $widget_id ]['resource_label_color'] = $one_widget['channel_label_color'];
			}

			if ( isset( $one_widget['channel_link_color'] ) ) {
				$widgets_config[ $widget_id ]['resource_link_color'] = $one_widget['channel_link_color'];
			}

			if ( isset( $one_widget['channel_link_hover_color'] ) ) {
				$widgets_config[ $widget_id ]['resource_link_hover_color'] = $one_widget['channel_link_hover_color'];
			}

			if ( isset( $one_widget['channel_header_top_tab'] ) ) {
				$widgets_config[ $widget_id ]['resource_header_top_tab'] = $one_widget['channel_header_top_tab'];
			}

			if ( isset( $one_widget['channel_header_title'] ) ) {
				$widgets_config[ $widget_id ]['resource_header_title'] = $one_widget['channel_header_title'];
			}

			if ( isset( $one_widget['channel_header_sub_title'] ) ) {
				$widgets_config[ $widget_id ]['resource_header_sub_title'] = $one_widget['channel_header_sub_title'];
			}

			if ( isset( $one_widget['chat_welcome_text'] ) ) {
				$widgets_config[ $widget_id ]['resource_welcome_text'] = $one_widget['chat_welcome_text'];
			}

			if ( isset( $one_widget['channel_phone_label'] ) ) {
				$widgets_config[ $widget_id ]['resource_phone_label'] = $one_widget['channel_phone_label'];
			}

			if ( isset( $one_widget['channel_custom_link_label'] ) ) {
				$widgets_config[ $widget_id ]['resource_custom_link_label'] = $one_widget['channel_custom_link_label'];
			}
		}
		ephd_get_instance()->widgets_config_obj->update_config( $widgets_config );
	}

	/**
	 * Function for major updates
	 *
	 * @param $args
	 */
	public function in_plugin_update_message( $args ) {

		$current_version = Echo_Help_Dialog::$version;
		$new_version = empty( $args['new_version'] ) ? $current_version : $args['new_version'];

		// versions x.y0.z are major releases
		if ( ! preg_match( '/.*\.\d0\..*/', $new_version ) ) {
			return;
		}

		echo '<style> .ephd-update-warning+p { opacity: 0; height: 0;} </style> ';
		echo '<hr style="clear:left"><div class="ephd-update-warning"><span class="dashicons dashicons-info" style="float:left;margin-right: 6px;color: #d63638;"></span>';
		echo '<div class="ephd-update-warning__title">' . esc_html__( 'We highly recommend you back up your site before upgrading. Next, run the update in a staging environment.', 'help-dialog' ) . '</div>';
		echo '<div class="ephd-update-warning__message">' .	esc_html__( 'After you run the update, clear your browser cache, hosting cache, and caching plugins.', 'help-dialog' ) . '</div>';
		echo '<div class="ephd-update-warning__message">' .	esc_html__( 'The latest update includes some substantial changes across different areas of the plugin', 'help-dialog' ) . '</div>';
	}
}
