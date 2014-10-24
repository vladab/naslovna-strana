<?php
/**
 * Created by Vladica Bibeskovic.
 * Date: 24.10.14., 22.02 
 */

class NS_Email_List {

    public static function get_users_emails() {
        global $wpdb;
        $table_name = $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME;
        // get Users
        // TODO: Use wordpress SQL objects instead text query
        $results = $wpdb->get_results("SELECT email_address, mobile_number FROM $table_name");

        $sending_emails = array();
        if (count($results) > 0) {
            foreach ($results as $item) {
                $sending_emails[] = $item->email_address;
            }
        }
        return $sending_emails;
    }
}