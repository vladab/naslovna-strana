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
        $results = $wpdb->get_results("SELECT id, email_address, mobile_number, status FROM $table_name");

        $sending_emails = array();
        if (count($results) > 0) {
            foreach ($results as $item) {
                if( $item->status == 'subscribed' ) {
                    $sending_emails[]['email'] = $item->email_address;
                }
            }
        }
        return $sending_emails;
    }
    public static function check_if_email_in_database( $email_address ) {

        if ( strlen( $email_address ) > 0 ) {

            global $wpdb;
            $table_name = $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME;
            $query      = "SELECT id FROM $table_name WHERE email_address=%s;";
            $sql        = $wpdb->prepare( $query, $email_address );
            $results = $wpdb->get_results( $sql );
            if( count( $results ) > 0 ) {
                return 'success';
            } else {
                return 'error';
            }
        } else {
            return 'error';
        }
    }
    public function ns_remove_email_address_ajax() {
        $email_id = $_POST['email_id'];
        if( $email_id > 0 ) {
            global $wpdb;
            $wpdb->delete( $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME , array( 'ID' => $email_id ) );
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

            // Send Welcome email
            EmailSendingCron::SendWelcomeEmail($email_address);
        }
    }
    public static function naslovna_monitor_go()
    {
        if ( isset( $_GET['_unsubscribe'] ) && $_GET['_unsubscribe'] == 'true' ) {

            $open_info = base64_decode( $_GET['code'] );
            if ( strlen( $open_info ) > 0 ) {

                global $wpdb;
                $table_name = $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME;
                $query      = "UPDATE $table_name SET status=%s, creation_date=%s WHERE email_address=%s;";
                $sql        = $wpdb->prepare( $query, 'unsubscribed', current_time( 'mysql' ), $open_info );
                $wpdb->query( $sql );
                self::unsubscribed_page( $open_info );
                exit;
            }
        }
    }
    public static function capture_number_and_email()
    {
        if ( isset( $_POST['nss_subscription'] ) ) {
            if ( isset( $_POST['email_address'] ) ) {
                $email_address = $_POST['email_address'];
                $mobile_number = '';
                if( isset( $_POST['mobile_number'] ) ) {
                    $mobile_number = $_POST['mobile_number'];
                }
                $location_slug = '';
                if( isset( $_POST['location'] ) ) {
                    $location_slug = $_POST['location'];
                }
                NS_Email_List::add_new_email_to_the_list( $email_address, $mobile_number, $location_slug );
            }
        }
    }
    public static function unsubscribed_page( $email_address ) {
        echo '
			<body style="background-color: #4991C5;">
			    <div style="width:800px;
			                margin:70px auto;
			                text-align: center;
			                font-family: Arial;
			                background-color: #fff;
			                border-radius: 10px;
			                box-shadow: 0px 2px 4px #5E5E5E;
			                padding: 20px;">

			        <h2>Uspesno ste se odjavili sa nase email liste!</h2>
			        <p>Vasa email adresa: <b>' . $email_address . '</b></p>
			        <p>Vise necete dobijati nove naslovne strane od nas.</p>
			        <p>Hvala sto se bili deo nase zajednice.</p>
			    </div>
			</body>';
    }
}