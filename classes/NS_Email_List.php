<?php
/**
 * Created by Vladica Bibeskovic.
 * Date: 24.10.14., 22.02 
 */

class NS_Email_List {

    public static function init() {
        add_action('wp_ajax_ns_remove_email_address_ajax', array( get_class(), 'ns_remove_email_address_ajax' ) );
        add_action('wp_ajax_nopriv_ns_remove_email_address_ajax', array( get_class(), 'ns_remove_email_address_ajax' ) );
    }

    public static function get_users_emails() {
        global $wpdb;
        $table_name = $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME;
        // get Users
        // TODO: Use wordpress SQL objects instead text query
        $results = $wpdb->get_results("SELECT id, email_address, mobile_number FROM $table_name");

        $sending_emails = array();
        if (count($results) > 0) {
            foreach ($results as $item) {
                $sending_emails['email'] = $item->email_address;
                $sending_emails['id'] = $item->id;
            }
        }
        return $sending_emails;
    }
    public function ns_remove_email_address_ajax() {
        $email_id = $_POST['email_id'];
        if( $email_id > 0 ) {
            global $wpdb;
            $wpdb->delete( $wpdb->prefix . CGCS_TABLE_NAME , array( 'ID' => $email_id ) );
        }
        exit();
    }
    public static function add_new_email_to_the_list( $email_address, $mobile_number, $location_slug)
    {
        if( strlen($email_address) > 0 ) {
            global $wpdb;
            $data_to_insert = array(
                'email_address' => $email_address,
                'status' 		=> 'subscribed',
                'mobile_number' => $mobile_number,
                'location'      => $location_slug,
                'ip_address'	=> $_SERVER['REMOTE_ADDR'],
                'creation_date' => current_time( 'mysql' )
            );
            $wpdb->insert( $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME, $data_to_insert );
        }
    }
}