<?php
/*
Plugin Name: Naslovna Strana
Plugin URI: http://v85dev.com
Description: Naslovna Strana plugin for sending emails of all newspapers in Serbia
Version: 1.1
Author: Vladica Bibeskovic
Author URI: http://v85dev.com/
*/

/* options page */
define( 'NASLOVNA_STRANA_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' );
define( 'NASLOVNA_STRANA_RESOURCE_URL', plugins_url( 'resources/', __FILE__ ) );
define( 'NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME', 'email_list');

include NASLOVNA_STRANA_PATH . 'classes/NaslovnaStrana.php';
include NASLOVNA_STRANA_PATH . 'classes/EmailSendingCron.php';
include NASLOVNA_STRANA_PATH . 'classes/NS_Email_List.php';
include NASLOVNA_STRANA_PATH . 'inc/template_tags.php';

/**
 * Activation and uninstall scripts for installind and importing database
 */
register_activation_hook(__FILE__, 'naslovna_strana_plugin_activate_plugin' );
register_uninstall_hook(__FILE__, 'naslovna_strana_plugin_remove_plugin' );
function naslovna_strana_plugin_activate_plugin() {
    include NASLOVNA_STRANA_PATH . 'classes/import/default_options.php';
    naslovna_strana_plugin_activate();
}
function naslovna_strana_plugin_remove_plugin() {
    include NASLOVNA_STRANA_PATH . 'classes/import/default_options.php';
    naslovna_strana_plugin_remove_data();
}
/**
 * Plugin class call on invoke
 */
class Naslovna_Strana_Plugin {

    public static function init() {
        // Email sending corections
        add_filter("wp_mail_from_name", array( get_class(), "nss_filter_wp_mail_from_name" ) );
        add_filter("wp_mail_from", array( get_class(), "nss_filter_wp_mail_from" ) );
        // Load Scripts
        add_action('init', array( get_class(), 'nss_plugin_load' ) );
        // Add setting link on plugin page
        add_filter( 'plugin_action_links', array( get_class(), 'naslovna_add_action_link' ), 10, 2 );
        // Add Link of the subscribers
        add_action('admin_menu', array( get_class(), 'nss_options_page' ) );
        // Init functions for deleting in the Email list table
        NS_Email_List::init();
    }
    public static function nss_filter_wp_mail_from_name() {
        return "Naslovna Strana";
    }
    public static function nss_filter_wp_mail_from() {
        return "info@naslovnastrana.com";
    }

    public static function nss_plugin_load() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );
    }

    public static function naslovna_add_action_link( $links, $file ) {
        $this_plugin = 'naslovnastrana-addon/naslovnastrana-addon.php';
        if ( $file == $this_plugin ) {
            $settings_link = '<a href="options-general.php?page=naslovnastrana-addon/options.php">' . __( 'Settings' ) . '</a>';
            array_unshift( $links, $settings_link );
        }
        return $links;
    }
    public static function nss_options_page() {
        add_users_page( "Naslovna Strana Subscribers", "Subscribers",'edit_themes', 'nss_options_page' , array( get_class(), 'nss_options_page_render' ),  'icon.png');
    }
    public static function nss_options_page_render() {
        include( NASLOVNA_STRANA_PATH . 'views/Email_Table_View.php' );
    }
}
add_action( 'init', array( 'Naslovna_Strana_Plugin', 'init' )  );
add_action( 'init', array( 'NS_Email_List', 'capture_number_and_email' )  );
add_action( 'init', array( 'NS_Email_List', 'naslovna_monitor_go' ) );


?>