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
    $sources = NS_Front_Page_Data();
    update_option( 'NASLOVNA_STRANA_SOURCES_FRONT_PAGE', maybe_serialize($sources) );
    $sources = NS_Email_Data();
    update_option( 'NASLOVNA_STRANA_SOURCES', maybe_serialize($sources) );
}
function naslovna_strana_plugin_remove_data() {
    global $wpdb;
    $table = $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME;
    $wpdb->query("DROP TABLE IF EXISTS $table");
}
function NS_Front_Page_Data() {
    // Sources list
    $sources = array(
        'Informer' => array(
            'url' => 'http://www.informer.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//div[@class="previewizdanja"]/a/img',
            'html_object_src_type' => 'src',

            'relative' => true,
        ),
        '24 Sata' => array(
            'url' => 'http://www.24sata.rs/',
            'type' => 'img_src',
            'img_src_address' => 'http://e24.24sata.rs/issues/24sata_'. date('dmy') .'/pages/large/24sata_'. date('dmy') .'-000001.jpg',
            'has_weekends_isue' => 'no',
        ),
        'Blic' => array(
            'url' => 'http://www.blic.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//a[@id="blic_naslovna_print"]/img',
            'html_object_src_type' => 'src',

            'relative' => true,
            'change_src' => true,
            'change_src_offset' => -5,
            'change_src_string' => 'big.jpg',
        ),
        'Politika' => array(
            'url' => 'http://www.politika.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//a[@class="home_print_img"]',
            'html_object_src_type' => 'href',

            'relative' => false,
            'change_src' => false,
        ),
        'Sportski Zurnal' => array(
            'url' => 'http://www.zurnal.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//aside/a',
            'html_object_src_type' => 'href',

            'relative' => true,
        ),
        'Narodne Novine Nis' => array(
            'url' => 'http://www.narodne.com/',
            'type' => 'img_src',
            'img_src_address' => 'http://www.narodne.com/fliper/pages/large/01NN.jpg',
        ),
        'Danas' => array(
            'url' => 'http://www.danas.rs/',
            'type' => 'pdf_src',
            'pdf_src_address' => 'http://www.danas.rs/upload/documents/Dodaci/'.date('Y').'/Dnevni/danas_srb_01.pdf',
            'extra_param' => '-width=794&-height=1106',
        ),
        'Vecernje Novosti' => array(
            'url' => 'http://www.novosti.rs/',
            'type' => 'img_src',
            'img_src_address' => 'http://www.novosti.rs/upload/images/banners/naslovna/naslovna-velika.jpg',
        ),
        'Kurir' => array(
            'url' => 'http://www.kurir-info.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//li[@id="print_page_01"]/div/img',
            'html_object_src_type' => 'src',

            'change_src_find' => 'slika-300x380',
            'change_src_replace' => 'slika-620x910',
        ),
    );
    return $sources;
}
function NS_Email_Data() {
    // Sources list
    $sources = array(
        'Blic' => array(
            'url' => 'http://www.blic.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//a[@id="blic_naslovna_print"]/img',
            'html_object_src_type' => 'src',

            'relative' => true,
            'change_src' => true,
            'change_src_offset' => -5,
            'change_src_string' => 'big.jpg',
        ),
        'Vecernje Novosti' => array(
            'url' => 'http://www.novosti.rs/',
            'type' => 'img_src',
            'img_src_address' => 'http://www.novosti.rs/upload/images/banners/naslovna/naslovna-velika.jpg',
        ),
        'Politika' => array(
            'url' => 'http://www.politika.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//a[@class="home_print_img"]',
            'html_object_src_type' => 'href',

            'relative' => false,
            'change_src' => false,
        ),
        'Danas' => array(
            'url' => 'http://www.danas.rs/',
            'type' => 'pdf_src',
            'pdf_src_address' => 'http://www.danas.rs/upload/documents/Dodaci/'.date('Y').'/Dnevni/danas_srb_01.pdf',
            'extra_param' => '-width=794&-height=1106',
        ),
        'Sportski Zurnal' => array(
            'url' => 'http://www.zurnal.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//div[@id="danasuszimage"]/a',
            'html_object_src_type' => 'href',
        ),
        'Narodne Novine Nis' => array(
            'url' => 'http://www.narodne.com/',
            'type' => 'img_src',
            'img_src_address' => 'http://www.narodne.com/fliper/pages/large/01NN.jpg',
        ),
        'Kurir' => array(
            'url' => 'http://www.kurir-info.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//li[@id="print_page_01"]/div/img',
            'html_object_src_type' => 'src',

            'change_src_find' => 'slika-300x380',
            'change_src_replace' => 'slika-620x910',
        ),
        'Informer' => array(
            'url' => 'http://www.informer.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//div[@class="previewizdanja"]/a/img',
            'html_object_src_type' => 'src',

            'relative' => true,
        ),
        '24 Sata' => array(
            'url' => 'http://www.24sata.rs/',
            'type' => 'img_src',
            'img_src_address' => 'http://e24.24sata.rs/issues/24sata_'. date('dmy') .'/pages/large/24sata_'. date('dmy') .'-000001.jpg',
            'has_weekends_isue' => 'no',
        ),
        'Magazin Tabloid' => array(
            'url' => 'http://www.magazin-tabloid.com/casopis/',
            'type' => 'dom_query',
            'dom_query_string' => '//a[@class="fancybox"]/img',
            'html_object_src_type' => 'src',

            'relative' => true,
        ),
        'Vijesti' => array(
            'url' => 'http://www.vijesti.me/p/naslovnice/'. date('Y').'-'.date('m').'-'.date('d'),
            'type' => 'dom_query',
            'dom_query_string' => '//article/figure/img',
            'html_object_src_type' => 'src',

            '_parse_url_extract_host' => true,
        ),
        'Corax' => array(
            'url' => 'http://www.danas.rs/',
            'type' => 'img_src',
            'img_src_address' => 'http://www.danas.rs/upload/images/corax/'.date('Y').'/'.date('n').'/'.date('j').'/coraxv-'.date('d').'-'.date('m').'-'.date('Y').'_ocp_w500_h516.jpg',
        ),
        'Novosti Strip' => array(
            'url' => 'http://novosti.rs/dodatni_sadrzaj/foto.110.html?galleryId=4',
            'type' => 'dom_query',
            'dom_query_string' => '//div[@class="visualBg"]/div/a[1]',
            'html_object_src_type' => 'href',
            '_parse_url_extract_host' => true,
        ),
        'Politika Strip' => array(
            'url' => 'http://www.politika.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//div[@id="main_holder"]/div[@class="right_col"]/div/div/a/img',
            'html_object_src_type' => 'src',
        ),
        'Blic Strip' => array(
            'url' => 'http://www.blic.rs/',
            'type' => 'dom_query',
            'dom_query_string' => '//div[@id="strip_right"]/a/img',
            'html_object_src_type' => 'src',

            'change_src' => true,
            'change_src_offset' => -9,
            'change_src_string' => '.jpg',

            'override_email_width' => true,
            'override_email_width_value' => '780px',
        ),
    );
    return $sources;
}
?>