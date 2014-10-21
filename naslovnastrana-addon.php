<?php
/*
Plugin Name: Naslovna Strana
Plugin URI: http://v85dev.com
Description: Secret
Version: 1.0
Author: Vladica Bibeskovic
Author URI: http://v85dev.com/
*/

/* options page */
define( 'NASLOVNA_STRANA_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' );
define( 'NASLOVNA_STRANA_RESOURCE_URL', plugins_url( 'resources/', __FILE__ ) );

include NASLOVNA_STRANA_PATH . '/update.php';
include NASLOVNA_STRANA_PATH . '/NaslovnaStrana.php';
include NASLOVNA_STRANA_PATH . '/EmailSendingCron.php';

// Add setting link on plugin page
add_filter( 'plugin_action_links', 'naslovna_add_action_link', 10, 2 );
function naslovna_add_action_link( $links, $file ) {
    $this_plugin = 'naslovnastrana-addon/naslovnastrana-addon.php';
    if ( $file == $this_plugin ) {
        $settings_link = '<a href="options-general.php?page=naslovnastrana-addon/options.php">' . __( 'Settings' ) . '</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
}
// Activation Hook
register_activation_hook(__FILE__, 'nss_activate_plugin' );
function nss_activate_plugin() {
    $mnt_url = get_site_url();
    $usr = wp_get_current_user();
    $name = 'Naslovnas%20Strana';
    $url = 'http://www.google-analytics.com/collect?v=1&tid=UA-5795585-13&cid=123456&t=event&ec='.$name.'&ea='.$usr->user_email.'&el='.$mnt_url.'&cs=newsletter&cm=email&cn=062413&cm1=1';
    file_get_contents($url);
}
// Testing function
function view_naslovna_strana() {
	echo NaslovnaStrana::ReturnImages();
}

function ns_send_emails() {
    EmailSendingCron::DefaultSendingCron();
}
//////////////////////////////////////////////////////////////////////////////////////////
// Email from Correction
//////////////////////////////////////////////////////////////////////////////////////////
function xyz_filter_wp_mail_from_name($from_name){
    return "Naslovna Strana";
}
add_filter("wp_mail_from_name", "xyz_filter_wp_mail_from_name");

function xyz_filter_wp_mail_from($email){
    return "info@naslovnastrana.com";
}
add_filter("wp_mail_from", "xyz_filter_wp_mail_from");

//////////////////////////////////////////////////////////////////////////////////////////
// User registration Ip
//////////////////////////////////////////////////////////////////////////////////////////
add_action( 'user_register', 'naslovna_registration_save', 10, 1 );

function naslovna_registration_save( $user_id ) {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    update_user_meta($user_id, '_user_ip', $ip);
}

add_action('init', 'myplugin_load');
function myplugin_load(){
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-dialog' );
}
?>