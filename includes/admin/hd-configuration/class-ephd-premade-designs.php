<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Contains Pre-made designs
 *
 * @copyright   Copyright (C) 2021, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPHD_Premade_Designs {

	/**
	 * Get pre-made Design settings
	 *
	 * @param $chosen_colors_preset_id
	 * @param $dialog_width_id
	 * @param $design_config
	 *
	 * @return array
	 */
	public static function get_premade_design( $chosen_colors_preset_id, $dialog_width_id, $design_config ) {

		// if user chooses a color preset then use default preset colors + rest of default colors
		$color_sets = self::get_color_sets();
		if ( ! empty( $chosen_colors_preset_id ) && isset( $color_sets[$chosen_colors_preset_id] ) ) {

			$default_designs_config = EPHD_Config_Specs::get_default_hd_config( EPHD_Widgets_DB::EPHD_WIDGETS_CONFIG_NAME );

			// get default colors
			$default_colors = array_filter( $default_designs_config,
				function ( $key ) {
					return preg_match( '/_color$/', $key );
				},
				ARRAY_FILTER_USE_KEY
			);

			$color_set = array_merge( $default_colors, $color_sets[$chosen_colors_preset_id]['config'] );
			$design_config = array_merge( $design_config, $color_set );
		}

		// Style / Features
		$dialog_widths = self::get_dialog_widths();
		if ( ! empty( $dialog_width_id ) || isset( $dialog_widths[$dialog_width_id] ) ) {
			$design_config = array_merge( $design_config, $dialog_widths[$dialog_width_id]['global_config'] );
		}

		return $design_config;
	}

	/**
	 * Get pre-made Global settings
	 *
	 * @param $dialog_width_id
	 * @param $global_config
	 *
	 * @return array
	 */
	public static function get_premade_global_config( $dialog_width_id, $global_config ) {

		if ( empty( $global_config ) ) {
			$global_config = EPHD_Config_Specs::get_default_hd_config( EPHD_Config_DB::EPHD_GLOBAL_CONFIG_NAME );
		}

		// Style / Features
		$dialog_widths = self::get_dialog_widths();

		return empty( $dialog_width_id ) || ! isset( $dialog_widths[$dialog_width_id] )
			? $global_config
			: array_merge( $global_config, $dialog_widths[$dialog_width_id]['global_config'] );
	}

	/**
	 * Return array of color sets
	 *
	 * @return array
	 */
	public static function get_color_sets() {

		$colors_sets = [];

		$colors_sets['light-green'] = [
			'title' => esc_html__( 'Light Green', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#4DAB58',
				'background_color'                          => '#4DAB58',
				'not_active_tab_color'                      => '#2E6548',
				'breadcrumb_background_color'               => '#D8EDDE',
				'contact_submit_button_color'               => '#4DAB58',
				'contact_submit_button_hover_color'         => '#2E6548',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['blue-magenta'] = [
			'title' => esc_html__( 'Blue-Magenta', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#545190',
				'background_color'                          => '#545190',
				'not_active_tab_color'                      => '#453D4D',
				'breadcrumb_background_color'               => '#D9E1F1',
				'contact_submit_button_color'               => '#545190',
				'contact_submit_button_hover_color'         => '#453D4D',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['green'] = [
			'title' => esc_html__( 'Green', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#378B84',
				'background_color'                          => '#378B84',
				'not_active_tab_color'                      => '#3D575D',
				'breadcrumb_background_color'               => '#E7F4F1',
				'contact_submit_button_color'               => '#378B84',
				'contact_submit_button_hover_color'         => '#3D575D',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['orange-light'] = [
			'title' => esc_html__( 'Light Orange', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#EE9C22',
				'background_color'                          => '#EE9C22',
				'not_active_tab_color'                      => '#C66D2A',
				'breadcrumb_background_color'               => '#E6D8C9',
				'contact_submit_button_color'               => '#EE9C22',
				'contact_submit_button_hover_color'         => '#C66D2A',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['blue-bright'] = [
			'title' => esc_html__( 'Bright Blue', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#1E60E0',
				'background_color'                          => '#1E60E0',
				'not_active_tab_color'                      => '#3E5291',
				'breadcrumb_background_color'               => '#DEF0FE',
				'contact_submit_button_color'               => '#1E60E0',
				'contact_submit_button_hover_color'         => '#3E5291',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['orange'] = [
			'title' => esc_html__( 'Orange', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#D46837',
				'background_color'                          => '#D46837',
				'not_active_tab_color'                      => '#804440',
				'breadcrumb_background_color'               => '#FDE4DF',
				'contact_submit_button_color'               => '#D46837',
				'contact_submit_button_hover_color'         => '#804440',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['blue'] = [
			'title' => esc_html__( 'Blue', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#0f4874',
				'background_color'                          => '#0f4874',
				'not_active_tab_color'                      => '#132e59',
				'breadcrumb_background_color'               => '#DFEFFB',
				'contact_submit_button_color'               => '#2D7EBE',
				'contact_submit_button_hover_color'         => '#4D4986',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['red'] = [
			'title' => esc_html__( 'Red', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#C3466B',
				'background_color'                          => '#C3466B',
				'not_active_tab_color'                      => '#833E69',
				'breadcrumb_background_color'               => '#FBDEEB',
				'contact_submit_button_color'               => '#C3466B',
				'contact_submit_button_hover_color'         => '#833E69',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['light-purple'] = [
			'title' => esc_html__( 'Light Purple', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#BC81F6',
				'background_color'                          => '#BC81F6',
				'not_active_tab_color'                      => '#7E6BA9',
				'breadcrumb_background_color'               => '#DFEFFB',
				'contact_submit_button_color'               => '#BC81F6',
				'contact_submit_button_hover_color'         => '#7E6BA9',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['gray'] = [
			'title' => esc_html__( 'Gray', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#788180',
				'background_color'                          => '#788180',
				'not_active_tab_color'                      => '#3D575D',
				'breadcrumb_background_color'               => '#EDF4F2',
				'contact_submit_button_color'               => '#788180',
				'contact_submit_button_hover_color'         => '#3D575D',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['purple'] = [
			'title' => esc_html__( 'Purple', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#7E6BA9',
				'background_color'                          => '#7E6BA9',
				'not_active_tab_color'                      => '#4B4354',
				'breadcrumb_background_color'               => '#D8D8F5',
				'contact_submit_button_color'               => '#7E6BA9',
				'contact_submit_button_hover_color'         => '#4B4354',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		$colors_sets['black'] = [
			'title' => esc_html__( 'Black', 'help-dialog' ),
			'config' => [
				'launcher_background_color'                 => '#424241',
				'background_color'                          => '#424241',
				'not_active_tab_color'                      => '#1E1F1D',
				'breadcrumb_background_color'               => '#E5E9EC',
				'contact_submit_button_color'               => '#424241',
				'contact_submit_button_hover_color'         => '#1E1F1D',
				'faqs_qa_border_color'                      => '#CCCCCC',
				'faqs_question_background_color'            => '#f7f7f7',
				'faqs_question_active_text_color'           => '#000000',
				'faqs_question_active_background_color'     => '#ffffff',
				'faqs_answer_text_color'                    => '#000000',
				'faqs_answer_background_color'              => '#ffffff',
			],
		];

		return $colors_sets;
	}

	/**
	 * Return array of presets for style features
	 *
	 * @return array
	 */
	private static function get_dialog_widths() {

		$global_config_specs = EPHD_Config_Specs::get_fields_specification( EPHD_Config_DB::EPHD_GLOBAL_CONFIG_NAME );

		$dialog_widths = [];
		$dialog_widths['small'] = [
			'title'         => $global_config_specs['dialog_width']['options']['small'],
			'global_config' => [
				'dialog_width'              => 'small',
				'main_title_font_size'      => '16',
				'logo_image_width'          => '60',
			],
			'config' => [],
		];
		$dialog_widths['medium'] = [
			'title'         => $global_config_specs['dialog_width']['options']['medium'],
			'global_config' => [
				'dialog_width'              => $global_config_specs['dialog_width']['default'],
				'main_title_font_size'      => $global_config_specs['main_title_font_size']['default'],
				'logo_image_width'          => $global_config_specs['logo_image_width']['default'],
			],
			'config' => [],
		];
		$dialog_widths['large'] = [
			'title'         => $global_config_specs['dialog_width']['options']['large'],
			'global_config' => [
				'dialog_width'              => 'large',
				'main_title_font_size'      => $global_config_specs['main_title_font_size']['default'],
				'logo_image_width'          => $global_config_specs['logo_image_width']['default'],
			],
			'config' => [],
		];


		/* $dialog_widths['alternative-text'] = [
			'title'         => esc_html__( 'Alternate Labels', 'help-dialog' ),
			'global_config' => [
				'main_title_font_size'      => $global_config_specs['main_title_font_size']['default'],
				'logo_image_width'          => $global_config_specs['logo_image_width']['default'],
			],
			'design_config' => [
				'welcome_title'             => esc_html__( 'Instant Help', 'help-dialog' ),
				'welcome_text'              => esc_html__( 'How can we help?', 'help-dialog' ),
				'search_input_placeholder'  => esc_html__( 'Have a Question?', 'help-dialog' ),
				'search_results_title'      => esc_html__( 'Start', 'help-dialog' ),
				'breadcrumb_home_text'      => esc_html__( 'Search Results Title', 'help-dialog' ),
				'contact_us_top_tab'        => esc_html__( 'Message Us', 'help-dialog' ),
				'contact_title'             => esc_html__( 'Contact Us', 'help-dialog' ),
				'contact_name_text'         => esc_html__( 'First Name', 'help-dialog' ),
				'contact_user_email_text'   => esc_html__( 'Email Address', 'help-dialog' ),
				'contact_subject_text'      => esc_html__( 'Subject', 'help-dialog' ),
				'contact_comment_text'      => esc_html__( 'Describe your issue', 'help-dialog' ),
				'contact_button_title'      => esc_html__( 'Send', 'help-dialog' ),
			],
		]; */

		return $dialog_widths;
	}
}
