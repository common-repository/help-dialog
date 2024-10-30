<?php

/**
 * Handles settings specifications.
 */
class EPHD_Config_Specs {
	
	const DEFAULT_ID = 1;

	/**
	 * Home Page always has ID = 0 in Widget locations (see 'location_pages_list' plugin setting).
	 * If user selected 'Home Page' in Widget locations, then the Widget will be displayed on the Home Page
	 * without dependency on 'Settings' -> 'Reading Settings' -> 'Your homepage displays'.
	 */
	const HOME_PAGE = 0;

	public static function get_defaults() {
		return array(
			'label'         => esc_html__( 'Label', 'help-dialog' ),
			'type'          => EPHD_Input_Filter::TEXT,
			'mandatory'     => true,
			'max'           => '20',
			'min'           => '3',
			'options'       => array(),
			'internal'      => false,
			'default'       => '',
			'is_pro'        => false
		);
	}

	/**
	 * Return fields specification for configuration accordingly to config name
	 *
	 * @param string $config_name
	 *
	 * @return array|array[]
	 */
	public static function get_fields_specification( $config_name='' ) {

		switch ( $config_name ) {

			case EPHD_Widgets_DB::EPHD_WIDGETS_CONFIG_NAME:
				return self::get_widget_fields_specification();

			case EPHD_Config_DB::EPHD_NOTIFICATION_RULES_CONFIG_NAME:
				return self::get_notification_rule_fields_specification();

			case EPHD_Config_DB::EPHD_GLOBAL_CONFIG_NAME:
			default:
				return self::get_global_fields_specification();
		}
	}

	public static function get_all_specs() {
		return self::get_widget_fields_specification() + self::get_global_fields_specification();
	}

	/**
	 * Defines data needed for display, initialization and validation/sanitation of settings
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => false )
	 *
	 * @return array with settings specification
	 */
	public static function get_global_fields_specification() {

		// all default settings are listed here
		return array(

			'first_plugin_version'                      => array(
				'label'       => 'first_plugin_version',
				'name'        => 'first_plugin_version',
				'type'        => EPHD_Input_Filter::TEXT,
				'internal'    => true,
				'default'     => Echo_Help_Dialog::$version
			),
			'upgrade_plugin_version'                    => array(
				'label'       => 'upgrade_plugin_version',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPHD_Input_Filter::TEXT,
				'internal'    => true,
				'default'     => '2.6.1' // TODO next release Echo_Help_Dialog::$version
			),

			'logo_image_url'                            => array(
				'label'     => esc_html__( 'Logo Image URL', 'help-dialog' ),
				'name'      => 'logo_image_url',
				'max'       => '300',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::TEXT,
				'default'   => Echo_Help_Dialog::$plugin_url . 'img/logo-placement.png'
			),
			'logo_image_width'                          => array(
				'label'       => esc_html__( 'Logo Width (px)', 'help-dialog' ),
				'name'        => 'logo_image_width',
				'max'         => 120,
				'min'         => 1,
				'type'        => EPHD_Input_Filter::NUMBER,
				'default'     => 70
			),
			'mobile_break_point'                        => array(
				'label'     => esc_html__( 'Mobile Break Point (px)', 'help-dialog' ),
				'name'      => 'mobile_break_point',
				'max'       => 2000,
				'min'       => 100,
				'type'      => EPHD_Input_Filter::NUMBER,
				'style'     => 'small',
				'default'   => 768
			),
			'main_title_font_size'                      => array(
				'label'       => esc_html__( 'Main Title Font Size (px)', 'help-dialog' ),
				'name'        => 'main_title_font_size',
				'max'         => 40,
				'min'         => 1,
				'type'        => EPHD_Input_Filter::NUMBER,
				'default'     => 20
			),

			// IDs sequence
			'last_widget_id'                            => array(
				'label'     => esc_html__( 'Last Widget ID', 'help-dialog' ),
				'name'      => 'last_widget_id',
				'max'       => 999999999999999,
				'min'       => self::DEFAULT_ID,
				'type'      => EPHD_Input_Filter::NUMBER,
				'internal'  => true,
				'default'   => self::DEFAULT_ID
			),

			// preview
			'preview_post_mode'                         => array(
				'label'     => esc_html__( 'Preview Post Mode', 'help-dialog' ),
				'name'      => 'preview_post_mode',
				'type'      => EPHD_Input_Filter::SELECTION,
				'is_pro'    => false,
				'options'   => array(
					'direct' => esc_html__( 'Direct to Post', 'help-dialog' ),
					'excerpt' => esc_html__( 'Excerpt', 'help-dialog' )
				),
				'default'   => 'excerpt',
			),
			'preview_kb_mode'                           => array(
				'label'     => esc_html__( 'Preview KB Article Mode', 'help-dialog' ),
				'name'      => 'preview_kb_mode',
				'type'      => EPHD_Input_Filter::SELECTION,
				'is_pro'    => false,
				'options'   => array(
					'iframe'      => esc_html__( 'Iframe', 'help-dialog' ),
					'excerpt' => esc_html__( 'Excerpt', 'help-dialog' ),
					'direct' => esc_html__( 'Direct to Article', 'help-dialog' )
				),
				'default'   => 'iframe'
			),

			// analytics
			// future todo
			'analytic_count_launcher_impression'        => array(
				'label'     => esc_html__( 'Count Launcher Impression', 'help-dialog' ),
				'name'      => 'analytic_count_launcher_impression',
				'type'      => EPHD_Input_Filter::CHECKBOX,
				'options'   => array(
					'off' => esc_html__( 'Disable Impression Counting', 'help-dialog' ),
					'on'  => esc_html__( 'Enable Impression Counting ', 'help-dialog' ),
				),
				'default'   => 'off'
			),
			'analytic_excluded_roles'                   => array(
				'label'       => esc_html__( 'Exclude Users', 'help-dialog' ),
				'name'        => 'widget_status',
				'type'        => EPHD_Input_Filter::CHECKBOXES_MULTI_SELECT,
				'options'     => array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ),
				'default'     => array( 'administrator', 'editor', 'author', 'contributor' )
			),

			'kb_article_hidden_classes'                 => array(
				'label'     => esc_html__( 'Classes to Hide Content', 'help-dialog' ),
				'name'      => 'kb_article_hidden_classes',
				'max'       => '1000',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::TEXT,
				'default'   => ''
			),
			'dialog_width'                              => array(
				'label'       => esc_html__( 'Dialog Width', 'help-dialog' ),
				'name'        => 'dialog_width',
				'type'        => EPHD_Input_Filter::SELECTION,
				'options'     => array(
					'small'  => esc_html__( 'Small', 'help-dialog' ),
					'medium' => esc_html__( 'Medium', 'help-dialog' ),
					'large'  => esc_html__( 'Large', 'help-dialog' )
				),
				'default'     => 'medium'
			),

			// Contact Form
			'contact_submission_email'                  => array(  // TODO FUTURE (move or keep as global / default one)
				'label'        => esc_html__( 'Email for User Submissions', 'help-dialog' ),
				'name'         => 'contact_submission_email',
				'max'          => '50',
				'min'          => '0',
				'mandatory'    => false,
				'type'         => EPHD_Input_Filter::EMAIL,
				'default'      => ''
			),

			// Other
			'private_faqs_included_roles'               => array(
				'label'       => esc_html__( 'Who Can Access Private FAQs', 'help-dialog' ),
				'name'        => 'private_faqs_included_roles',
				'type'        => EPHD_Input_Filter::CHECKBOXES_MULTI_SELECT,
				'options'     => array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ),
				'default'     => array( 'administrator', 'editor' )
			),
			'wpml_toggle'                               => array(
				'label'     => esc_html__( 'Polylang/WPML Setup', 'help-dialog' ),
				'name'      => 'wpml_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'is_pro'    => true,
				'options'   => array(
				    'off' => esc_html__( 'Disable', 'help-dialog' ),
				    'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'off'
			),
			'tabs_sequence'                             => array(	// TODO future: remove
				'label'     => esc_html__( 'Tabs Sequence', 'help-dialog' ),
				'name'      => 'tabs_sequence',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'chat_faqs_contact' => esc_html__( 'Chat, FAQs, Contact Us', 'help-dialog' ),
					'faqs_chat_contact' => esc_html__( 'FAQs, Chat, Contact Us', 'help-dialog' ),
				),
				'default'   => 'faqs_resource_contact'
			),

			// OpenAI
			'openai_api_key'                            => array(       // TODO 2025 remove
				'label'     => esc_html__( 'OpenAI API Key', 'help-dialog' ),
				'name'      => 'openai_api_key',
				'max'       => '500',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::TEXT,
				'default'   => ''
			),

			// Access to Admin Pages (read)
			'admin_ephd_access_admin_pages_read'        => array(
				'label'       => esc_html__( 'Access to Admin Pages', 'help-dialog' ),
				'name'        => 'admin_ephd_access_admin_pages_read',
				'type'        => EPHD_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access' => array( EPHD_Admin_UI_Access::EPHD_WP_AUTHOR_CAPABILITY, EPHD_Admin_UI_Access::EPHD_WP_EDITOR_CAPABILITY ),
				'default'     => EPHD_Admin_UI_Access::EPHD_WP_EDITOR_CAPABILITY
			),
		);
	}

	/**
	 * Fields specifications for each Widget
	 *
	 * @return array[]
	 */
	private static function get_widget_fields_specification() {

		return array(

			/******************************************************************************
			 *
			 *  Internal Settings
			 *
			 ******************************************************************************/
			'widget_id'                                 => array(
				'label'       => esc_html__( 'Widget ID', 'help-dialog' ),
				'name'        => 'widget_id',
				'max'         => 1000000000,
				'min'         => 0,
				'type'        => EPHD_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => self::DEFAULT_ID
			),
			'widget_status'                             => array(
				'label'       => esc_html__( 'Visibility', 'help-dialog' ),
				'name'        => 'widget_status',
				'type'        => EPHD_Input_Filter::SELECTION,
				'options'     => array(
					'draft'     => esc_html__( 'Draft', 'help-dialog' ),
					'published' => esc_html__( 'Published', 'help-dialog' ),
				),
				'default'     => 'draft'
			),
			'faqs_name'                                 => array(
				'label'       => esc_html__( 'FAQs', 'help-dialog' ),
				'name'        => 'faqs_name',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Default Questions', 'help-dialog' )
			),
			'faqs_sequence'                             => array(
				'name'       => 'faqs_sequence',
				'type'        => EPHD_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'initial_message_id'                        => array(
				'label'     => esc_html__( 'Message ID', 'help-dialog' ),
				'name'      => 'initial_message_id',
				'max'       => 999999999999999,
				'min'       => 1,
				'type'      => EPHD_Input_Filter::NUMBER,
				'internal'  => true,
				'default'   => 1
			),

			/******************************************************************************
			 *
			 *  Locations/Triggers: Pages
			 *
			 ******************************************************************************/
			'location_page_filtering'                   => array(
				'label'       => esc_html__( 'Page Filtering', 'help-dialog' ),
				'name'        => 'location_page_filtering',
				'type'        => EPHD_Input_Filter::SELECTION,
				'options'     => array(
					'include'  => esc_html__( 'Include Specific Pages', 'help-dialog' ),
					'exclude'  => esc_html__( 'Include All Pages with Exceptions', 'help-dialog' ),
				),
				'default'     => 'include'
			),
			'location_pages_list'                       => array(
				'name'        => 'location_pages_list',
				'type'        => EPHD_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'location_posts_list'                       => array(
				'name'        => 'location_posts_list',
				'type'        => EPHD_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'location_cpts_list'                        => array(
				'name'        => 'location_cpts_list',
				'type'        => EPHD_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'location_language_filtering'               => array(
				'label'       => esc_html__( 'Filter by Language', 'help-dialog' ),
				'name'        => 'location_language_filtering',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => 'all'
			),
			'location_wildcard_url'                     => array(
				'label'         => esc_html__( 'Pages Matching URL', 'help-dialog' ),
				'name'          => 'location_wildcard_url',
				'max'           => '300',
				'min'           => '2',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( '', 'help-dialog' )
			),

			/******************************************************************************
			 *
			 *  Locations/Triggers: Triggers
			 *
			 ******************************************************************************/

			// triggers
			'trigger_delay_toggle'                      => array(
				'label'     => esc_html__( 'Delay Trigger', 'help-dialog' ),
				'name'      => 'trigger_delay_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'off'
			),
			'trigger_delay_seconds'                     => array(
				'label'     => esc_html__( 'Display After (sec)', 'help-dialog' ),
				'name'      => 'trigger_delay_seconds',
				'max'       => '3600',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::NUMBER,
				'style'     => 'small',
				'default'   => '0'
			),
			'trigger_scroll_toggle'                     => array(
				'label'     => esc_html__( 'Scroll Trigger', 'help-dialog' ),
				'name'      => 'trigger_scroll_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'off'
			),
			'trigger_scroll_percent'                    => array(
				'label'     => esc_html__( 'Display on Page Scroll (%)', 'help-dialog' ),
				'name'      => 'trigger_scroll_percent',
				'max'       => '100',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::NUMBER,
				'style'     => 'small',
				'default'   => '0'
			),
			'trigger_days_and_hours_toggle'             => array(
				'label'     => esc_html__( 'Days and Hours Trigger', 'help-dialog' ),
				'name'      => 'trigger_days_and_hours_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'off'
			),
			'trigger_days'                              => array(
				'label'     => esc_html__( 'Display on Days', 'help-dialog' ),
				'name'      => 'trigger_days',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'everyday_of_week'      => esc_html__( 'Everyday of week', 'help-dialog' ),
					'sunday'                => esc_html__( 'Sunday', 'help-dialog' ),
					'monday'                => esc_html__( 'Monday', 'help-dialog' ),
					'tuesday'               => esc_html__( 'Tuesday', 'help-dialog' ),
					'wednesday'             => esc_html__( 'Wednesday', 'help-dialog' ),
					'thursday'              => esc_html__( 'Thursday', 'help-dialog' ),
					'friday'                => esc_html__( 'Friday', 'help-dialog' ),
					'saturday'              => esc_html__( 'Saturday', 'help-dialog' ),
					'sunday_to_thursday'    => esc_html__( 'Sunday to Thursday', 'help-dialog' ),
					'monday_to_friday'      => esc_html__( 'Monday to Friday', 'help-dialog' ),
					'weekend'               => esc_html__( 'Weekend', 'help-dialog' ),
				),
				'default'   => 'everyday_of_week'
			),
			'trigger_hours_from'                        => array(
				'label'     => esc_html__( 'Display from Hours', 'help-dialog' ),
				'name'      => 'trigger_hours_from',
				'max'       => '23',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::NUMBER,
				'style'     => 'small',
				'default'   => '0'
			),
			'trigger_hours_to'                          => array(
				'label'     => esc_html__( 'Display to Hours', 'help-dialog' ),
				'name'      => 'trigger_hours_to',
				'max'       => '24',
				'min'       => '1',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::NUMBER,
				'style'     => 'small',
				'default'   => '24'
			),

			/******************************************************************************
			 *
			 *  Structure: Launcher
			 *
			 ******************************************************************************/
			'launcher_mode'                             => array(
				'label'     => esc_html__( 'Launcher Mode', 'help-dialog' ),
				'name'      => 'launcher_mode',
				'type'      => EPHD_Input_Filter::SELECTION,
				'is_pro'    => true,
				'options'   => array(
					'icon'      => esc_html__( 'Icon', 'help-dialog' ),
					'icon_text' => esc_html__( 'Icon + Text', 'help-dialog' ),
					'text_icon' => esc_html__( 'Text + Icon', 'help-dialog' )
				),
				'default'   => 'icon'
			),
			'launcher_icon'                             => array(
				'label'     => esc_html__( 'Launcher Icon', 'help-dialog' ),
				'name'      => 'launcher_icon',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'ep_font_icon_help_dialog'   => esc_html__( 'HD Icon (default)', 'help-dialog' ),
					'comments-o'   => esc_html__( 'Icon 1 (default)', 'help-dialog' ),
					'comments'     => esc_html__( 'Icon 2', 'help-dialog' ),
					'commenting-o' => esc_html__( 'Icon 3', 'help-dialog' ),
					'commenting'   => esc_html__( 'Icon 4', 'help-dialog' ),
					'comment-o'    => esc_html__( 'Icon 5', 'help-dialog' ),
				),
				'default'   => 'comments-o'
			),
			'dialog_initial_visibility'                         => array(
				'label'     => esc_html__( 'Initial Dialog Visibility', 'help-dialog' ),
				'name'      => 'dialog_initial_visibility',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'on' => esc_html__( 'Opened', 'help-dialog' ),
					'off' => esc_html__( 'Closed', 'help-dialog' ),
				),
				'default'   => 'off'
			),
			'launcher_location'                         => array(
				'label'     => esc_html__( 'Launcher Location', 'help-dialog' ),
				'name'      => 'launcher_location',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'left'  => esc_html__( 'Left', 'help-dialog' ),
					'right' => esc_html__( 'Right ', 'help-dialog' ),
				),
				'default'   => 'right',
			),
			'launcher_bottom_distance'                  => array(
				'label'     => esc_html__( 'Launcher Bottom Distance (px)', 'help-dialog' ),
				'name'      => 'launcher_bottom_distance',
				'max'       => '2000',
				'min'       => '0',
				'type'      => EPHD_Input_Filter::NUMBER,
				'style'     => 'small',
				'default'   => '10'
			),
			'launcher_start_wait'                       => array(
				'label'       => esc_html__( 'Delay Displaying Launcher (sec)', 'help-dialog' ),
				'name'        => 'launcher_start_wait',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPHD_Input_Filter::NUMBER,
				'default'     => '0'
			),

			// initial message
			'initial_message_toggle'                    => array(
				'label'     => esc_html__( 'Initial Message', 'help-dialog' ),
				'name'      => 'initial_message_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'is_pro'    => true,
				'options'   => array(
					'show' => esc_html__( 'Show', 'help-dialog' ),
					'hide' => esc_html__( 'Hide', 'help-dialog' ),
				),
				'default'   => 'hide'
			),
			'initial_message_mode'                      => array(
				'label'     => esc_html__( 'Message Mode', 'help-dialog' ),
				'name'      => 'initial_message_mode',
				'type'      => EPHD_Input_Filter::SELECTION,
				'is_pro'    => true,
				'options'   => array(
					'text' => esc_html__( 'Text', 'help-dialog' ),
					'icon_text' => esc_html__( 'Text + Icon', 'help-dialog' )
				),
				'default'   => 'icon_text'
			),
			'initial_message_text'                      => array(
				'label'     => esc_html__( 'Message Text', 'help-dialog' ),
				'name'      => 'initial_message_text',
				'max'       => '300',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::WP_EDITOR,
				'is_pro'    => true,
				'default'   => esc_html__( 'Need help?', 'help-dialog' )
			),
			'initial_message_image_url'                 => array(
				'label'     => esc_html__( 'Message Image URL', 'help-dialog' ),
				'name'      => 'initial_message_image_url',
				'max'       => '300',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::TEXT,
				'is_pro'    => true,
				'default'   => Echo_Help_Dialog::$plugin_url . 'img/kb-icon.png'
			),

			/******************************************************************************
			 *
			 *  Structure: Dialog
			 *
			 ******************************************************************************/
			'launcher_powered_by'                       => array(
				'label'     => esc_html__( 'Powered By Text', 'help-dialog' ),
				'name'      => 'launcher_powered_by',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'show' => esc_html__( 'Show', 'help-dialog' ),
					'hide' => esc_html__( 'Hide', 'help-dialog' ),
				),
				'default'   => 'hide'
			),

			/******************************************************************************
			 *
			 *  Structure: Search
			 *
			 ******************************************************************************/
			'search_option'                             => array(
				'label'       => esc_html__( 'Search Input Box', 'help-dialog' ),
				'name'        => 'search_option',
				'type'        => EPHD_Input_Filter::SELECTION,
				'options'     => array(
					'show_search'  => esc_html__( 'Show Search', 'help-dialog' ),
					'hide_search'  => esc_html__( 'Hide Search', 'help-dialog' ),
				),
				'default'     => 'show_search'
			),
			'search_posts'                              => array(
				'label'       => esc_html__( 'Search Posts', 'help-dialog' ),
				'name'        => 'search_posts',
				'type'        => EPHD_Input_Filter::SELECTION,
				'options'     => array(
					'off'  => esc_html__( 'Off', 'help-dialog' ),
					'on'   => esc_html__( 'On', 'help-dialog' ),
				),
				'default'     => 'off'
			),
			'search_kb'                                 => array(
				'label'       => esc_html__( 'Search Knowledge Base', 'help-dialog' ),
				'name'        => 'search_kb',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::SELECTION,
				// do not automatically populate options here. do it in UI
				'default'     => 'off'
			),

			/******************************************************************************
			 *
			 *  Structure: General
			 *
			 ******************************************************************************/
			'widget_name'                               => array(
				'label'       => esc_html__( 'Nickname', 'help-dialog' ),
				'name'        => 'widget_name',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Home Page', 'help-dialog' )
			),

			/******************************************************************************
			 *
			 *  Main Features: Phone
			 *
			 ******************************************************************************/
			'display_resource_tab'                      => array(	// TODO future: remove
				'label'     => esc_html__( 'Resource', 'help-dialog' ),
				'name'      => 'display_resource_tab',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Off', 'help-dialog' ),
					'on'  => esc_html__( 'On', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			// Phone Resource
			'resource_phone_toggle'                      => array(
				'label'     => esc_html__( 'Phone Resource', 'help-dialog' ),
				'name'      => 'resource_phone_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Off', 'help-dialog' ),
					'on'  => esc_html__( 'On', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			'resource_phone_country_code'                => array(
				'label'         => esc_html__( 'Phone Country Code', 'help-dialog' ),
				'name'          => 'resource_phone_country_code',
				'max'           => '10',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( '', 'help-dialog' )
			),
			'resource_phone_number'                      => array(
				'label'         => esc_html__( 'Phone Number', 'help-dialog' ),
				'name'          => 'resource_phone_number',
				'max'           => '100',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( '', 'help-dialog' )
			),
			'resource_phone_number_image_url'            => array(
				'label'     => esc_html__( 'Phone Image URL', 'help-dialog' ),
				'name'      => 'resource_phone_number_image_url',
				'max'       => '300',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::TEXT,
				'default'   => esc_html__( '', 'help-dialog' )
			),
			// Custom Link Resource
			'resource_custom_link_toggle'                => array(
				'label'     => esc_html__( 'Custom Link Resource', 'help-dialog' ),
				'name'      => 'resource_custom_link_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Off', 'help-dialog' ),
					'on'  => esc_html__( 'On', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			'resource_custom_link_url'                   => array(
				'label'         => esc_html__( 'Custom Link URL', 'help-dialog' ),
				'name'          => 'resource_custom_link_url',
				'max'           => '300',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => 'https://www.helpdialog.com/'
			),
			'resource_custom_link_image_url'             => array(
				'label'     => esc_html__( 'Custom Link Image URL', 'help-dialog' ),
				'name'      => 'resource_custom_link_image_url',
				'max'       => '300',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::TEXT,
				'default'   => esc_html__( '', 'help-dialog' )
			),

			/******************************************************************************
			 *
			 *  Main Features: FAQs
			 *
			 ******************************************************************************/
			'display_faqs_tab'                          => array(	// TODO future: remove
				'label'     => esc_html__( 'FAQs Tab', 'help-dialog' ),
				'name'      => 'display_faqs_tab',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'on'
			),

			/******************************************************************************
			 *
				 *  Main Features: Contact Form
			 *
			 ******************************************************************************/
			'display_contact_tab'                       => array(	// TODO future: remove
				'label'     => esc_html__( 'Contact Form Tab', 'help-dialog' ),
				'name'      => 'display_contact_tab',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			'contact_name_toggle'                       => array(
				'label'     => esc_html__( 'Name Input', 'help-dialog' ),
				'name'      => 'contact_name_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			'contact_subject_toggle'                    => array(
				'label'     => esc_html__( 'Subject Input', 'help-dialog' ),
				'name'      => 'contact_subject_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			'contact_acceptance_checkbox'               => array(
				'label'     => esc_html__( 'Acceptance Checkbox', 'help-dialog' ),
				'name'      => 'contact_acceptance_checkbox',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'off'
			),
			'contact_acceptance_title_toggle'           => array(
				'label'     => esc_html__( 'Acceptance Checkbox Title', 'help-dialog' ),
				'name'      => 'contact_acceptance_title_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'help-dialog' ),
					'on'  => esc_html__( 'Enable', 'help-dialog' ),
				),
				'default'   => 'off'
			),

			/******************************************************************************
			 *
			 *  Tabs Sequence
			 *
			 ******************************************************************************/
			'tabs_position_1'                             => array(
				'label'     => esc_html__( 'Tabs Position 1', 'help-dialog' ),
				'name'      => 'tabs_position_1',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'none'		=> esc_html__( 'None', 'help-dialog' ),
					'chat'		=> esc_html__( 'Chat', 'help-dialog' ),
					'faqs'		=> esc_html__( 'FAQs', 'help-dialog' ),
					'resource'	=> esc_html__( 'Resource', 'help-dialog' ),
					'contact'	=> esc_html__( 'Contact', 'help-dialog' ),
				),
				'default'   => 'faqs'
			),
			'tabs_position_2'                             => array(
				'label'     => esc_html__( 'Tabs Position 2', 'help-dialog' ),
				'name'      => 'tabs_position_2',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'none'		=> esc_html__( 'None', 'help-dialog' ),
					'chat'		=> esc_html__( 'Chat', 'help-dialog' ),
					'faqs'		=> esc_html__( 'FAQs', 'help-dialog' ),
					'resource'	=> esc_html__( 'Resource', 'help-dialog' ),
					'contact'	=> esc_html__( 'Contact', 'help-dialog' ),
				),
				'default'   => 'chat'
			),
			'tabs_position_3'                             => array(
				'label'     => esc_html__( 'Tabs Position 3', 'help-dialog' ),
				'name'      => 'tabs_position_3',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'none'		=> esc_html__( 'None', 'help-dialog' ),
					'chat'		=> esc_html__( 'Chat', 'help-dialog' ),
					'faqs'		=> esc_html__( 'FAQs', 'help-dialog' ),
					'resource'	=> esc_html__( 'Resource', 'help-dialog' ),
					'contact'	=> esc_html__( 'Contact', 'help-dialog' ),
				),
				'default'   => 'resource'
			),
			'tabs_position_4'                             => array(
				'label'     => esc_html__( 'Tabs Position 4', 'help-dialog' ),
				'name'      => 'tabs_position_4',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'none'		=> esc_html__( 'None', 'help-dialog' ),
					'chat'		=> esc_html__( 'Chat', 'help-dialog' ),
					'faqs'		=> esc_html__( 'FAQs', 'help-dialog' ),
					'resource'	=> esc_html__( 'Resource', 'help-dialog' ),
					'contact'	=> esc_html__( 'Contact', 'help-dialog' ),
				),
				'default'   => 'contact'
			),

			/******************************************************************************
			 *
			 *  Design: Colors
			 *
			 ******************************************************************************/
			// - Top buttons
			'back_text_color'                           => array(
				'label'       => esc_html__( 'Text/Icon Color', 'help-dialog' ),
				'name'        => 'back_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'back_text_color_hover_color'               => array(
				'label'       => esc_html__( 'Text/Icon Hover Color', 'help-dialog' ),
				'name'        => 'back_text_color_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'back_background_color'                     => array(
				'label'       => esc_html__( 'Background Color', 'help-dialog' ),
				'name'        => 'back_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#066fc0"
			),
			'back_background_color_hover_color'         => array(
				'label'       => esc_html__( 'Background Color Hover Color', 'help-dialog' ),
				'name'        => 'back_background_color_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#066fc0"
			),

			// - Launcher
			'launcher_background_color'                 => array(
				'label'       => esc_html__( 'Background', 'help-dialog' ),
				'name'        => 'launcher_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#0f4874"
			),
			'launcher_background_hover_color'           => array(
				'label'       => esc_html__( 'Background Hover', 'help-dialog' ),
				'name'        => 'launcher_background_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#a5a5a5"
			),
			'launcher_icon_color'                       => array(
				'label'       => esc_html__( 'Icon', 'help-dialog' ),
				'name'        => 'launcher_icon_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'launcher_icon_hover_color'                 => array(
				'label'       => esc_html__( 'Icon Hover', 'help-dialog' ),
				'name'        => 'launcher_icon_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'background_color'                          => array(
				'label'       => esc_html__( 'Main Background / Active Tab', 'help-dialog' ),
				'name'        => 'background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#0f4874"
			),
			'not_active_tab_color'                      => array(
				'label'       => esc_html__( 'Not Active Tab', 'help-dialog' ),
				'name'        => 'not_active_tab_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#132e59"
			),
			'tab_text_color'                            => array(
				'label'       => esc_html__( 'Tab text', 'help-dialog' ),
				'name'        => 'tab_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'main_title_text_color'                     => array(
				'label'       => esc_html__( 'Main Title / Search Results', 'help-dialog' ),
				'name'        => 'main_title_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#FFFFFF"
			),
			'welcome_title_color'                       => array(
				'label'       => esc_html__( 'Welcome Title', 'help-dialog' ),
				'name'        => 'welcome_title_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#FFFFFF"
			),
			'welcome_title_link_color'                  => array(
				'label'       => esc_html__( 'Welcome Title Links', 'help-dialog' ),
				'name'        => 'welcome_title_link_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#FFFFFF"
			),
			'breadcrumb_color'                          => array(
				'label'       => esc_html__( 'Breadcrumb Color', 'help-dialog' ),
				'name'        => 'breadcrumb_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#333333"
			),
			'breadcrumb_background_color'               => array(
				'label'       => esc_html__( 'Breadcrumb Background Color', 'help-dialog' ),
				'name'        => 'breadcrumb_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#e6e6e6"
			),
			'breadcrumb_arrow_color'                    => array(
				'label'       => esc_html__( 'Breadcrumb Arrow', 'help-dialog' ),
				'name'        => 'breadcrumb_arrow_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'faqs_qa_border_color'                      => array(
				'label'       => esc_html__( 'Question Border', 'help-dialog' ),
				'name'        => 'faqs_qa_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#CCCCCC"
			),
			'faqs_question_text_color'                  => array(
				'label'       => esc_html__( 'Question Text', 'help-dialog' ),
				'name'        => 'faqs_question_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'faqs_question_background_color'            => array(
				'label'       => esc_html__( 'Question Background', 'help-dialog' ),
				'name'        => 'faqs_question_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#f7f7f7"
			),
			'faqs_question_active_text_color'           => array(
				'label'       => esc_html__( 'Question Active text', 'help-dialog' ),
				'name'        => 'faqs_question_active_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'faqs_question_active_background_color'     => array(
				'label'       => esc_html__( 'Question Active Background', 'help-dialog' ),
				'name'        => 'faqs_question_active_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'faqs_answer_text_color'                    => array(
				'label'       => esc_html__( 'Answer Text', 'help-dialog' ),
				'name'        => 'faqs_answer_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'faqs_answer_background_color'              => array(
				'label'       => esc_html__( 'Answer Background', 'help-dialog' ),
				'name'        => 'faqs_answer_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'found_faqs_article_active_tab_color'       => array(
				'label'       => esc_html__( 'Active Tab', 'help-dialog' ),
				'name'        => 'found_faqs_article_active_tab_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#0f9beb"
			),
			'found_faqs_article_tab_color'              => array(
				'label'       => esc_html__( 'Inactive Tabs', 'help-dialog' ),
				'name'        => 'found_faqs_article_tab_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'article_post_list_title_color'             => array(
				'label'       => esc_html__( 'Article/Post Title Color', 'help-dialog' ),
				'name'        => 'article_post_list_title_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'article_post_list_icon_color'              => array(
				'label'       => esc_html__( 'Article/Post Icon Color', 'help-dialog' ),
				'name'        => 'article_post_list_icon_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),

			// - Single Article
			'single_article_read_more_text_color'       => array(
				'label'       => esc_html__( 'Read More Text Color', 'help-dialog' ),
				'name'        => 'single_article_read_more_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#0f9beb"
			),
			'single_article_read_more_text_hover_color' => array(
				'label'       => esc_html__( 'Read More Text Hover Color', 'help-dialog' ),
				'name'        => 'single_article_read_more_text_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#007eed"
			),

			// - Contact Form Tab
			'contact_submit_button_color'               => array(
				'label'       => esc_html__( 'Submit Button Color', 'help-dialog' ),
				'name'        => 'contact_submit_button_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#2D7EBE"
			),
			'contact_submit_button_hover_color'         => array(
				'label'       => esc_html__( 'Submit Button Hover Color', 'help-dialog' ),
				'name'        => 'contact_submit_button_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#4D4986"
			),
			'contact_submit_button_text_color'          => array(
				'label'       => esc_html__( 'Submit Button Text Color', 'help-dialog' ),
				'name'        => 'contact_submit_button_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'contact_submit_button_text_hover_color'    => array(
				'label'       => esc_html__( 'Submit Button Text Hover Color', 'help-dialog' ),
				'name'        => 'contact_submit_button_text_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'contact_acceptance_background_color'       => array(
				'label'       => esc_html__( 'Acceptance Checkbox Background Color', 'help-dialog' ),
				'name'        => 'contact_acceptance_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),

			'resource_phone_color'                       => array(
				'label'       => esc_html__( 'Phone Icon Color', 'help-dialog' ),
				'name'        => 'resource_phone_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#03e78b"
			),
			'resource_phone_hover_color'                 => array(
				'label'       => esc_html__( 'Phone Icon Hover Color', 'help-dialog' ),
				'name'        => 'resource_phone_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#03e78b"
			),
			'resource_label_color'                       => array(
				'label'       => esc_html__( 'Label Color', 'help-dialog' ),
				'name'        => 'resource_label_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'resource_link_color'                        => array(
				'label'       => esc_html__( 'Link Icon Color', 'help-dialog' ),
				'name'        => 'resource_link_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#03e78b"
			),
			'resource_link_hover_color'                  => array(
				'label'       => esc_html__( 'Link Icon Hover Color', 'help-dialog' ),
				'name'        => 'resource_link_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#03e78b"
			),

			/******************************************************************************
			 *
			 *  Design: Labels
			 *
			 ******************************************************************************/
			// launcher
			'launcher_text'                             => array(
				'label'     => esc_html__( 'Launcher Text', 'help-dialog' ),
				'name'      => 'launcher_text',
				'max'       => '300',
				'min'       => '1',
				'type'      => EPHD_Input_Filter::TEXT,
				'is_pro'    => true,
				'default'   => esc_html__( 'Need help?', 'help-dialog' )
			),
			'welcome_title'                             => array(
				'label'       => esc_html__( 'FAQs Title', 'help-dialog' ),
				'name'        => 'welcome_title',
				'max'         => '200',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Welcome to Support', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
			),
			'contact_us_top_tab'                        => array(
				'label'       => esc_html__( 'Contact Us Tab Text', 'help-dialog' ),
				'name'        => 'contact_us_top_tab',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Contact Us', 'help-dialog' )
			),

			// - Chat Tab
			'chat_top_tab'                 				=> array(
				'label'       => esc_html__( 'Chat Tab Text', 'help-dialog' ),
				'name'        => 'chat_top_tab',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Chat', 'help-dialog' )
			),
			'chat_welcome_text'                         => array(
				'label'     => esc_html__( 'Chat Welcome Text', 'help-dialog' ),
				'name'      => 'chat_welcome_text',
				'max'       => '300',
				'min'       => '1',
				'type'      => EPHD_Input_Filter::TEXT,
				'default'   => esc_html__( 'Explore the resources listed below or reach out to us for direct assistance.', 'help-dialog' )
			),

			// - Resource Tab
			'resource_header_top_tab'                    => array(
				'label'       => esc_html__( 'Resource Tab Text', 'help-dialog' ),
				'name'        => 'resource_header_top_tab',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Resource', 'help-dialog' )
			),
			'resource_header_title'                      => array(
				'label'       => esc_html__( 'Resource Title', 'help-dialog' ),
				'name'        => 'resource_header_title',
				'max'         => '200',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Welcome to Support', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
			),
			'resource_header_sub_title'                  => array(
				'label'       => esc_html__( 'Resource Header Sub Title', 'help-dialog' ),
				'name'        => 'resource_header_sub_title',
				'max'         => '200',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'How can we help you?', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
			),
			'resource_welcome_text'                         => array(
				'label'     => esc_html__( 'Resource Welcome Text', 'help-dialog' ),
				'name'      => 'resource_welcome_text',
				'max'       => '300',
				'min'       => '1',
				'type'      => EPHD_Input_Filter::TEXT,
				'default'   => esc_html__( 'Explore the resources listed below or reach out to us for direct assistance.', 'help-dialog' )
			),

			// - FAQ List Tab
			'faqs_top_tab'                              => array(
				'label'       => esc_html__( 'FAQs Tab Text', 'help-dialog' ),
				'name'        => 'faqs_top_tab',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'FAQs', 'help-dialog' )
			),
			'welcome_text'                              => array(
				'label'       => esc_html__( 'FAQs Sub Title', 'help-dialog' ),
				'name'        => 'welcome_text',
				'max'         => '200',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'How can we help you?', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
			),
			'search_input_label'                        => array(
				'label'       => esc_html__( 'Search Label', 'help-dialog' ),
				'name'        => 'search_input_label',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search for an Answer', 'help-dialog' )
			),
			'search_input_placeholder'                  => array(
				'label'       => esc_html__( 'Search Placeholder', 'help-dialog' ),
				'name'        => 'search_input_placeholder',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Enter one or two keywords', 'help-dialog' )
			),
			'article_read_more_text'                    => array(
				'label'       => esc_html__( 'Read More Text', 'help-dialog' ),
				'name'        => 'article_read_more_text',
				'max'         => '100',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Read More', 'help-dialog' )
			),

			// Search Results
			'search_results_title'                      => array(
				'label'       => esc_html__( 'Search Results Title', 'help-dialog' ),
				'name'        => 'search_results_title',
				'max'         => '20',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search Results', 'help-dialog' )
			),
			'breadcrumb_home_text'                      => array(
				'label'       => esc_html__( 'Breadcrumb - Home', 'help-dialog' ),
				'name'        => 'breadcrumb_home_text',
				'max'         => '20',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Home', 'help-dialog' )
			),
			'breadcrumb_search_result_text'             => array(
				'label'       => esc_html__( 'Breadcrumb - Search Results', 'help-dialog' ),
				'name'        => 'breadcrumb_search_result_text',
				'max'         => '20',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search Results', 'help-dialog' )
			),
			'breadcrumb_article_text'                   => array(
				'label'       => esc_html__( 'Breadcrumb - Article', 'help-dialog' ),
				'name'        => 'breadcrumb_article_text',
				'max'         => '20',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Article', 'help-dialog' )
			),
			'found_faqs_tab_text'                       => array(
				'label'       => esc_html__( 'Found FAQs Tab', 'help-dialog' ),
				'name'        => 'found_faqs_tab_text',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'FAQs', 'help-dialog' )
			),
			'found_articles_tab_text'                   => array(
				'label'       => esc_html__( 'Found Articles Tab', 'help-dialog' ),
				'name'        => 'found_articles_tab_text',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Articles', 'help-dialog' )
			),
			'found_posts_tab_text'                      => array(
				'label'       => esc_html__( 'Found Posts Tab', 'help-dialog' ),
				'name'        => 'found_posts_tab_text',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Posts', 'help-dialog' )
			),
			'no_results_found_title_text'               => array(
				'label'       => esc_html__( 'No Results Title', 'help-dialog' ),
				'name'        => 'no_results_found_title_text',
				'max'         => '70',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'No Matches Found For', 'help-dialog' )
			),

			// Articles
			'protected_article_placeholder_text'        => array(
				'label'       => esc_html__( 'Password Protected Article Placeholder', 'help-dialog' ),
				'name'        => 'protected_article_placeholder_text',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Article is protected by password', 'help-dialog' )
			),
			'no_results_found_content_html'             => array(
				'label'       => esc_html__( 'No Results Found', 'help-dialog' ),
				'name'        => 'no_results_found_content_html',
				'max'         => '800',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::WP_EDITOR,
				'default'     => esc_html__( 'Search hints', 'help-dialog' ) . ':' .
								 '<ol>' .
								 '<li>' . esc_html__( "Use specific, rather than generic, search terms.", 'help-dialog' ) . '</li>' .
								 '<li>' . esc_html__( 'Try using fewer words.', 'help-dialog' ) . '</li>' .
								 '<li>' . esc_html__( 'Make sure the spelling is correct.', 'help-dialog' ) . '</li>' .
								 '</ol>'
			),
			'article_back_button_text'                  => array(
				'label'       => esc_html__( 'Back Button Text', 'help-dialog' ),
				'name'        => 'article_back_button_text',
				'max'         => '50',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Back', 'help-dialog' )
			),
			'search_instruction_text'                   => array(
				'label'       => esc_html__( 'Search Instructions text', 'help-dialog' ),
				'name'        => 'search_instruction_text',
				'max'         => '120',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search for your question below', 'help-dialog' )
			),
			'no_result_contact_us_text'                 => array(
				'label'       => esc_html__( 'Contact Us Link', 'help-dialog' ),
				'name'        => 'no_result_contact_us_text',
				'max'         => '70',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Contact Us', 'help-dialog' )
			),
			'contact_user_email_text'                   => array(
				'label'       => esc_html__( 'Email Text', 'help-dialog' ),
				'name'        => 'contact_user_email_text',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Email', 'help-dialog' )
			),
			'contact_welcome_title'                     => array(
				'label'       => esc_html__( 'Contact Us Title', 'help-dialog' ),
				'name'        => 'contact_welcome_title',
				'max'         => '200',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Welcome to Support', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
			),
			'contact_welcome_text'                      => array(
				'label'       => esc_html__( 'Contact Us Sub Title', 'help-dialog' ),
				'name'        => 'contact_welcome_text',
				'max'         => '200',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Get in Touch', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
			),
			'contact_name_text'                         => array(
				'label'       => esc_html__( 'Name Text', 'help-dialog' ),
				'name'        => 'contact_name_text',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Name', 'help-dialog' )
			),
			'contact_subject_text'                      => array(
				'label'       => esc_html__( 'Subject Text', 'help-dialog' ),
				'name'        => 'contact_subject_text',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Subject', 'help-dialog' )
			),
			'contact_comment_text'                      => array(
				'label'       => esc_html__( 'Comment Text', 'help-dialog' ),
				'name'        => 'contact_comment_text',
				'max'         => '250',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'How can we help you?', 'help-dialog' )
			),
			'contact_acceptance_title'                  => array(
				'label'       => esc_html__( 'Acceptance Checkbox Title', 'help-dialog' ),
				'name'        => 'contact_acceptance_title',
				'max'         => '75',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'GDPR Agreement', 'help-dialog' )
			),
			'contact_acceptance_text'                   => array(
				'label'       => esc_html__( 'Acceptance Checkbox Text', 'help-dialog' ),
				'name'        => 'contact_acceptance_text',
				'max'         => '1000',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
				'default'     => esc_html__( 'I accept the terms and conditions.', 'help-dialog' )
			),
			'contact_button_title'                      => array(
				'label'       => esc_html__( 'Submit Button Text', 'help-dialog' ),
				'name'        => 'contact_button_title',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Submit', 'help-dialog' )
			),
			'contact_success_message'                   => array(
				'label'        => esc_html__( 'Email Sent Success Message', 'help-dialog' ),
				'name'         => 'contact_success_message',
				'max'          => '150',
				'min'          => '0',
				'mandatory'    => false,
				'type'         => EPHD_Input_Filter::TEXT,
				'default'      => esc_html__( 'Thank you. We will get back to you soon.', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
				),
			),
			'resource_phone_label'                       => array(
				'label'         => esc_html__( 'Phone Label', 'help-dialog' ),
				'name'          => 'resource_phone_label',
				'max'           => '100',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( 'Phone', 'help-dialog' )
			),
			'resource_custom_link_label'                 => array(
				'label'         => esc_html__( 'Custom Link Label', 'help-dialog' ),
				'name'          => 'resource_custom_link_label',
				'max'           => '100',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( 'Custom Link', 'help-dialog' )
			),

			// TODO future: remove all settings with 'channel_' prefix
			'channel_phone_label'                       => array(
				'label'         => esc_html__( 'Phone Label', 'help-dialog' ),
				'name'          => 'channel_phone_label',
				'max'           => '100',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( 'Phone', 'help-dialog' )
			),
			'channel_custom_link_label'                 => array(
				'label'         => esc_html__( 'Custom Link Label', 'help-dialog' ),
				'name'          => 'channel_custom_link_label',
				'max'           => '100',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( 'Custom Link', 'help-dialog' )
			),
			'channel_header_top_tab'                    => array(
				'label'       => esc_html__( 'Resource Tab Text', 'help-dialog' ),
				'name'        => 'channel_header_top_tab',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Resource', 'help-dialog' )
			),
			'channel_header_title'                      => array(
				'label'       => esc_html__( 'Chat Title', 'help-dialog' ),
				'name'        => 'channel_header_title',
				'max'         => '200',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'Welcome to Chat', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
			),
			'channel_header_sub_title'                  => array(
				'label'       => esc_html__( 'Channel Header Sub Title', 'help-dialog' ),
				'name'        => 'channel_header_sub_title',
				'max'         => '200',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPHD_Input_Filter::TEXT,
				'default'     => esc_html__( 'How can we help you?', 'help-dialog' ),
				'allowed_tags' => array(
					'a' => array(
						'href'  => true,
						'title' => true,
					),
					'strong' => array(),
					'i' => array(),
					'br' => array(),
				),
			),
			'channel_phone_color'                       => array(
				'label'       => esc_html__( 'Phone Icon Color', 'help-dialog' ),
				'name'        => 'channel_phone_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#03e78b"
			),
			'channel_phone_hover_color'                 => array(
				'label'       => esc_html__( 'Phone Icon Hover Color', 'help-dialog' ),
				'name'        => 'channel_phone_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#03e78b"
			),
			'channel_label_color'                       => array(
				'label'       => esc_html__( 'Label Color', 'help-dialog' ),
				'name'        => 'channel_label_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'channel_link_color'                        => array(
				'label'       => esc_html__( 'Link Icon Color', 'help-dialog' ),
				'name'        => 'channel_link_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#03e78b"
			),
			'channel_link_hover_color'                  => array(
				'label'       => esc_html__( 'Link Icon Hover Color', 'help-dialog' ),
				'name'        => 'channel_link_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPHD_Input_Filter::COLOR_HEX,
				'default'     => "#03e78b"
			),
			'display_channels_tab'                      => array(	// TODO future: remove
				'label'     => esc_html__( 'Resource', 'help-dialog' ),
				'name'      => 'display_channels_tab',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Off', 'help-dialog' ),
					'on'  => esc_html__( 'On', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			// Phone Channel
			'channel_phone_toggle'                      => array(
				'label'     => esc_html__( 'Phone Channel', 'help-dialog' ),
				'name'      => 'channel_phone_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Off', 'help-dialog' ),
					'on'  => esc_html__( 'On', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			'channel_phone_country_code'                => array(
				'label'         => esc_html__( 'Phone Country Code', 'help-dialog' ),
				'name'          => 'channel_phone_country_code',
				'max'           => '10',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( '', 'help-dialog' )
			),
			'channel_phone_number'                      => array(
				'label'         => esc_html__( 'Phone Number', 'help-dialog' ),
				'name'          => 'channel_phone_number',
				'max'           => '100',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => esc_html__( '', 'help-dialog' )
			),
			'channel_phone_number_image_url'            => array(
				'label'     => esc_html__( 'Phone Image URL', 'help-dialog' ),
				'name'      => 'channel_phone_number_image_url',
				'max'       => '300',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::TEXT,
				'is_pro'    => true,
				'default'   => esc_html__( '', 'help-dialog' )
			),
			// Custom Link Channel
			'channel_custom_link_toggle'                => array(
				'label'     => esc_html__( 'Custom Link Channel', 'help-dialog' ),
				'name'      => 'channel_custom_link_toggle',
				'type'      => EPHD_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Off', 'help-dialog' ),
					'on'  => esc_html__( 'On', 'help-dialog' ),
				),
				'default'   => 'on'
			),
			'channel_custom_link_url'                   => array(
				'label'         => esc_html__( 'Custom Link URL', 'help-dialog' ),
				'name'          => 'channel_custom_link_url',
				'max'           => '300',
				'min'           => '0',
				'mandatory'     => false,
				'type'          => EPHD_Input_Filter::TEXT,
				'default'       => 'https://www.helpdialog.com/'
			),
			'channel_custom_link_image_url'             => array(
				'label'     => esc_html__( 'Custom Link Image URL', 'help-dialog' ),
				'name'      => 'channel_custom_link_image_url',
				'max'       => '300',
				'min'       => '0',
				'mandatory' => false,
				'type'      => EPHD_Input_Filter::TEXT,
				'is_pro'    => true,
				'default'   => esc_html__( '', 'help-dialog' )
			),
		);
	}

	/**
	 * Fields specifications for Notification Rule
	 *
	 * @return array[]
	 */
	private static function get_notification_rule_fields_specification() {
		return array();
	}

	/**
	 * Get Plugin default configuration
	 *
	 * @param string $config_name
	 *
	 * @return array contains default setting values
	 */
	public static function get_default_hd_config( $config_name='' ) {

		$setting_specs = self::get_fields_specification( $config_name );

		$default_configuration = array();
		foreach( $setting_specs as $key => $spec ) {
			$default = isset( $spec['default'] ) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}

		return $default_configuration;
	}

	/**
	 * Get names of all configuration items for Plugin settings
	 *
	 * @param string $config_name
	 *
	 * @return int[]|string[]
	 */
	public static function get_specs_item_names( $config_name='' ) {
		return array_keys( self::get_fields_specification( $config_name ) );
	}
}