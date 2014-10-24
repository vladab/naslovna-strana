<?php


// Plugin activated
function naslovna_strana_plugin_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME;
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
              id int(10) NOT NULL AUTO_INCREMENT,
              email_address varchar(30) DEFAULT NULL,
              status varchar(20) DEFAULT NULL,
              mobile_number varchar(30) DEFAULT NULL,
              location varchar(30) DEFAULT NULL,
              ip_address varchar(40) NULL,
              creation_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              UNIQUE KEY email_address (email_address)
        );";
        //reference to upgrade.php file
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
function naslovna_strana_plugin_remove_data() {
    global $wpdb;
    $table = $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME;
    $wpdb->query("DROP TABLE IF EXISTS $table");
}
?>