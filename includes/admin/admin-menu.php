<?php

/**
 * Setup WordPress menu for this plugin
 */

/**
 *  Register plugin menus
 */
function ephd_add_plugin_menus() {

	add_menu_page( esc_html__( 'Help Dialog', 'help-dialog' ), esc_html__( 'Help Dialog', 'help-dialog' ),
		EPHD_Admin_UI_Access::get_context_required_capability( ['admin_ephd_access_admin_pages_read'] ), 'ephd-help-dialog', array( new EPHD_Need_Help_Page(), 'display_need_help_page' ), '', 7 );

	add_submenu_page( 'ephd-help-dialog', esc_html__( 'Help Dialog Widgets', 'help-dialog' ), esc_html__( 'Widgets', 'help-dialog' ),
		EPHD_Admin_UI_Access::get_context_required_capability( ['admin_ephd_access_admin_pages_read'] ), 'ephd-help-dialog-widgets', array( new EPHD_Widgets_Display(), 'display_page' ), 10 );

	if ( class_exists( 'EPHP_Chat_Page' ) ) {
		add_submenu_page( 'ephd-help-dialog', esc_html__( 'Help Dialog AI Chat', 'help-dialog' ), esc_html__( 'AI Chat', 'help-dialog' ),
			EPHD_Admin_UI_Access::get_context_required_capability( ['admin_ephd_access_admin_pages_read'] ), 'ephp-help-dialog-chat', array( new EPHP_Chat_Page(), 'display_page' ), 13 );
	} else {
		add_submenu_page( 'ephd-help-dialog', esc_html__( 'Help Dialog AI Chat', 'help-dialog' ), esc_html__( 'AI Chat', 'help-dialog' ),
			EPHD_Admin_UI_Access::get_context_required_capability( ['admin_ephd_access_admin_pages_read'] ), 'ephd-help-dialog-chat', array( new EPHD_Chat_Page(), 'display_page' ), 13 );
	}

	add_submenu_page( 'ephd-help-dialog', esc_html__( 'Help Dialog Contact Form', 'help-dialog' ), esc_html__( 'Form Submissions', 'help-dialog' ),
		'manage_options', 'ephd-help-dialog-contact-form', array( new EPHD_Contact_Form_Display(), 'display_page' ), 15 );

	add_submenu_page( 'ephd-help-dialog', esc_html__( 'Help Dialog Analytics', 'help-dialog' ), esc_html__( 'Analytics', 'help-dialog' ),
						'manage_options', 'ephd-plugin-analytics', array( new EPHD_Analytics_Page(), 'display_page' ) );

	add_submenu_page( 'ephd-help-dialog', esc_html__( 'Help Dialog Advanced', 'help-dialog' ), esc_html__( 'Advanced', 'help-dialog' ),
						'manage_options', 'ephd-help-dialog-advanced-config', array( new EPHD_Config_Page(), 'display_page'), 20 );

}
add_action( 'admin_menu', 'ephd_add_plugin_menus', 10 );

/**
 * Change name for admin submenu pages
 */
function ephd_admin_menu_change_name() {
	global $submenu;

	if ( isset( $submenu['ephd-help-dialog'] ) ) {
		$submenu['ephd-help-dialog'][0][0] = esc_html__( 'Get Started', 'help-dialog' );
	}
}
add_action( 'admin_menu', 'ephd_admin_menu_change_name', 200 );
