<?php defined( 'ABSPATH' ) || exit();

/**
 * Display Help Dialog AI Chat page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPHD_Chat_Page {

	/**
	 * Displays the Help Dialog AI Chat admin page
	 */
	public function display_page() {

		if ( ! current_user_can( EPHD_Admin_UI_Access::get_context_required_capability( array( 'admin_ephd_access_admin_pages_read' ) ) ) ) {
			echo '<p>' . esc_html__( 'You do not have permission to edit Help Dialog.', 'help-dialog' ) . '</p>';
			return;
		}

		$admin_page_views = $this->get_regular_views_config();

		EPHD_HTML_Admin::admin_page_css_missing_message( true );        ?>

		<!-- Admin Page Wrap -->
		<div id="ephd-admin-page-wrap">
			<div class="ephd-chat-page-container">				<?php
				/**
				 * ADMIN HEADER
				 */
				EPHD_HTML_Admin::admin_header();

				/**
				 * ADMIN TOP PANEL
				 */
				EPHD_HTML_Admin::admin_toolbar( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPHD_HTML_Admin::admin_settings_tab_content( $admin_page_views, 'ephd-chats-wrapper' );

				// Links to functional HD AI chat
				EPHD_HTML_Forms::notification_box_middle( array(
					'id'   => 'ephd-admin__ai-message',
					'type' => 'success-no-icon',
					'desc' => esc_html__( 'If you like to test AI Chat with your knowledge base, contact us', 'help-dialog' ) . ' ' .
						'<a href="https://www.helpdialog.com/contact-us/pre-sale-and-general-questions/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'here', 'help-dialog' ) . '</a>.'
				) );             ?>
			</div>
		</div>

		<div class="ephd-bottom-notice-message fadeOutDown"></div>  		<?php
	}

	/**
	 * Get configuration array for regular views of Help Dialog AI Chat admin page
	 *
	 * @return array[]
	 */
	private function get_regular_views_config() {

		/**
		 * VIEW: AI Dashboard
		 */
		$views_config[] = array(

			// Shared.
			'active'     => true,
			'list_key'   => 'chat-overview',

			// Top Panel Item.
			'label_text' => esc_html__( 'Overview', 'help-dialog' ),
			'icon_class' => 'ephdfa ephdfa-th-large',

			// Boxes List.
			'boxes_list' => $this->get_chat_dashboard_boxes(),
		);

		return $views_config;
	}

	/**
	 * Return configuration for AI Chat dashboard tab.
	 *
	 * @return array
	 */
	private function get_chat_dashboard_boxes() {

		$dashboard_form_boxes = array();

		// Initialize assistant.
		$dashboard_form_boxes[] = array(
			'title' => esc_html__( 'Try Our New AI Powered Chat', 'help-dialog' ),
			'class' => 'ephd-chat__overview',
			'html'  => $this->get_overview_box_html(),
		);

		return $dashboard_form_boxes;
	}

	/**
	 * Initialize assistant button
	 *
	 * @return string
	 */
	private function get_overview_box_html() {

		ob_start();     ?>

		<div class="ephd-chat-setup-container">
			<div class='ephd-admin__frontend-link'>
				<a href='https://www.echoknowledgebase.com/documentation/' target='_blank'>
					<?php esc_html_e( 'Test AI Chat on the Knowledge Base official site', 'help-dialog' ); ?>
					<span class="ephdfa ephdfa-external-link"></span>
				</a>
			</div>
			<br><br>
			<div class="ephd-admin__frontend-link">
				<a href="https://www.helpdialog.com/documentation/" target="_blank">
					<?php esc_html_e( 'Test AI Chat on the Help Dialog official site', 'help-dialog' ); ?>
					<span class="ephdfa ephdfa-external-link"></span>
				</a>
			</div>
			<br>
		</div>    <?php

		return ob_get_clean();
	}
}
