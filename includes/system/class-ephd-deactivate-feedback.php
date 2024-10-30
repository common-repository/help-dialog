<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * If user is deactivating plugin, find out why
 */
class EPHD_Deactivate_Feedback {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_feedback_dialog_scripts' ] );
		add_action( 'wp_ajax_ephd_deactivate_feedback', [ $this, 'ajax_ephd_deactivate_feedback' ] );
	}

	/**
	 * Enqueue feedback dialog scripts.
	 */
	public function enqueue_feedback_dialog_scripts() {
		add_action( 'admin_footer', [ $this, 'output_deactivate_feedback_dialog' ] );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'ephd-admin-feedback', Echo_Help_Dialog::$plugin_url . 'js/admin-feedback' . $suffix . '.js', array('jquery'), Echo_Help_Dialog::$version );
		wp_register_style( 'ephd-admin-feedback-style', Echo_Help_Dialog::$plugin_url . 'css/admin-plugin-feedback' . $suffix . '.css', array(), Echo_Help_Dialog::$version );

		wp_enqueue_script( 'ephd-admin-feedback' );
		wp_enqueue_style( 'ephd-admin-feedback-style' );
	}

	/**
	 * Display a dialog box to ask the user why they deactivated the Help Dialog.
	 */
	public function output_deactivate_feedback_dialog() { ?>
        <div class="ephd-deactivate-modal" id="ephd-deactivate-modal" style="display:none;">
            <div class="ephd-deactivate-modal-wrap">
                <form id="ephd-deactivate-feedback-dialog-form" method="post">
                    <div class="ephd-deactivate-modal-header">
                        <h3><?php esc_html_e( 'Quick Feedback', 'help-dialog' ); ?></h3>
                    </div>
                    <div class="ephd-deactivate-modal-body">
                        <div class="ephd-deactivate-modal-reason-input-wrap">
                            <h4><?php esc_html_e( 'Please tell us what happened. Thank you!', 'help-dialog' ); ?></h4>
                            <textarea class="ephd_deactivate_feedback-text" name="ephd_deactivate_feedback"></textarea>
                        </div>
                    </div>
                    <div class="ephd-deactivate-modal-footer">
	                    <button class="ephd-deactivate-submit-modal"><?php echo esc_html__( 'Deactivate', 'help-dialog' ); ?></button>
	                    <button class="ephd-deactivate-button-secondary ephd-deactivate-cancel-modal"><?php echo esc_html__( 'Cancel', 'help-dialog' ); ?></button>
	                    <input type="hidden" name="action" value="ephd_deactivate_feedback" />  <?php
                        wp_nonce_field( '_ephd_deactivate_feedback_nonce' );    ?>
                    </div>
                </form>
            </div>
        </div>  <?php
	}

	/**
	 * Send the user feedback when Help Dialog is deactivated.
	 */
	public function ajax_ephd_deactivate_feedback() {
		global $wp_version;

		$wpnonce_value = EPHD_Utilities::post( '_wpnonce' );
		if ( empty( $wpnonce_value ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wpnonce_value ) ), '_ephd_deactivate_feedback_nonce' ) ) {
			wp_send_json_error();
		}

		// send email only if feedback is provided
		$feedback = EPHD_Utilities::post( 'ephd_deactivate_feedback' );
        if ( empty( $feedback ) ) {
            return;
        }

        // retrieve current user
        $user = EPHD_Utilities::get_current_user();
        $first_name = empty( $user ) ? 'Unknown' : ( empty( $user->user_firstname ) ? $user->display_name : $user->user_firstname );
        $contact_email = empty( $user ) ? 'N/A' : $user->user_email;

		//Theme Name and Version
		$active_theme = wp_get_theme();
		$theme_info = $active_theme->get( 'Name' ) . ' ' . $active_theme->get( 'Version' );

		// send feedback
		$api_params = array(
			'ep'.'kb_action'    => 'ep'.'kb_process_user_feedback',
			'feedback_type'     => get_transient( '_ephd_plugin_activated' ) ? 'Recently activated' : 'Some time ago',
			'feedback_input'    => $feedback,
			'plugin_name'       => 'Help Dialog',
			'plugin_version'    => class_exists( 'Echo_Help_Dialog' ) ? Echo_Help_Dialog::$version : 'N/A',
			'first_version'     => ephd_get_instance()->global_config_obj->get_value( 'first_plugin_version' ),
			'wp_version'        => $wp_version,
			'theme_info'        => $theme_info,
            'contact_user'      => $contact_email . ' - ' . $first_name,
            'first_name'        => $first_name,
		);

		// Call the API
        wp_remote_post(
			esc_url_raw( add_query_arg( $api_params, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout'   => 15,
				'body'      => $api_params,
				'sslverify' => false
			)
		);

		wp_send_json_success();
	}

	private function get_deactivate_reasons( $type ) {

		switch ( $type ) {
		   case 1:
		   	    $deactivate_reasons = [
			        'missing_feature'                => [
				        'title'             => esc_html__( 'I cannot find a feature', 'help-dialog' ),
				        'icon'              => 'ephdfa ephdfa-puzzle-piece',
				        'input_placeholder' => esc_html__( 'Please tell us what is missing', 'help-dialog' ),
				        'contact_email'     => [
                            'title'    => esc_html__( 'Let us help you find the feature. Please provide your contact email:', 'help-dialog' ),
                            'required' => false,
                        ],
			        ],
			        'couldnt_get_the_plugin_to_work' => [
				        'title'             => esc_html__( 'I couldn\'t get the plugin to work', 'help-dialog' ),
				        'icon'              => 'ephdfa ephdfa-question-circle-o',
				        'input_placeholder' => esc_html__( 'Please share the reason', 'help-dialog' ),
				        'contact_email'     => [
					        'title'    => esc_html__( 'Sorry to hear that. Let us help you. Please provide your contact email:', 'help-dialog' ),
					        'required' => false,
				        ],
			        ],
			        'bug_issue'                      => [
				        'title'             => esc_html__( 'Bug Issue', 'help-dialog' ),
				        'icon'              => 'ephdfa ephdfa-bug',
				        'input_placeholder' => esc_html__( 'Please describe the bug', 'help-dialog' ),
				        'contact_email'     => [
					        'title'    => esc_html__( 'We can fix the bug right away. Please provide your contact email:', 'help-dialog' ),
					        'required' => true,
				        ]
			        ],
			        'other'                          => [
				        'title'             => esc_html__( 'Other', 'help-dialog' ),
				        'icon'              => 'ephdfa ephdfa-ellipsis-h',
				        'input_placeholder' => esc_html__( 'Please share the reason', 'help-dialog' ),
				        'contact_email'     => [
					        'title'    => esc_html__( 'Can we talk to you about reason for removing the plugin?', 'help-dialog' ),
					        'required' => false,
				        ]
			        ],
			   ];
			   break;
		    case 2:
			default:
				$deactivate_reasons = [
					'no_longer_needed' => [
						'title'             => esc_html__( 'I no longer need the plugin', 'help-dialog' ),
						'icon'              => 'ephdfa ephdfa-question-circle-o',
						'custom_content'    => esc_html__( 'Thanks for using our products and have a great week', 'help-dialog' ) . '!',
						'input_placeholder' => '',
					],
					'missing_feature'  => [
						'title'             => esc_html__( 'I cannot find a feature', 'help-dialog' ),
						'icon'              => 'ephdfa ephdfa-puzzle-piece',
						'input_placeholder' => esc_html__( 'Please tell us what is missing', 'help-dialog' ),
						'contact_email'     => [
							'title'    => esc_html__( 'Let us help you find the feature. Please provide your contact email:', 'help-dialog' ),
							'required' => false,
						],
					],
					'bug_issue'                      => [
						'title'             => esc_html__( 'Bug Issue', 'help-dialog' ),
						'icon'              => 'ephdfa ephdfa-bug',
						'input_placeholder' => esc_html__( 'Please describe the bug', 'help-dialog' ),
						'contact_email'     => [
							'title'    => esc_html__( 'We can fix the bug right away. Please provide your contact email:', 'help-dialog' ),
							'required' => true,
						]
					],
					'other'            => [
						'title'             => esc_html__( 'Other', 'help-dialog' ),
						'icon'              => 'ephdfa ephdfa-ellipsis-h',
						'input_placeholder' => esc_html__( 'Please share the reason', 'help-dialog' ),
						'contact_email'     => [
							'title'    => esc_html__( 'Can we talk to you about reason to remove the plugin?', 'help-dialog' ),
							'required' => false,
						]
					]
			   ];
			   break;
	   }

		return $deactivate_reasons;
	}
}
