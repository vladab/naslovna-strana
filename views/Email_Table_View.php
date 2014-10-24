<style type="text/css">img.flag { background:url('<?php echo NASLOVNA_STRANA_RESOURCE_URL ?>flags.png') no-repeat }</style>
<link rel='stylesheet'  href='<?php echo NASLOVNA_STRANA_RESOURCE_URL ?>flags.css' type='text/css' media='screen' />
<h1>All Subscribed users captured</h1>
<?php
include( NASLOVNA_STRANA_PATH . 'classes/tables/Naslovana_Email_Captured_Table_List.php' );
new Naslovana_Email_Captured_Table_List();
?>
IP Country Search provided by <a href="http://ipinfo.io" target="_blank">ipinfo.io</a>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        jQuery(document).on('click', '.remove_email_address' , function() {
            var email_id_click = jQuery(this).attr('rel');
            var y=confirm('Are you sure you want to delete this Email?');
            if(y==true) {
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    data: {
                        action: 'cg_remove_email_address_ajax',
                        email_id: email_id_click
                    },
                    success: function(data) {
                        location.reload();
                    }
                });
                return false;
            }
        });
    });
    jQuery(document).ready(function($) {
        jQuery(document).on('click', '.block_email_address' , function() {
            var email_id_click = jQuery(this).attr('rel');
            var y=confirm('Are you sure you want to block this Email?');
            if(y==true) {
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    data: {
                        action: 'gbsac_block_email_address_item_ajax',
                        email_id: email_id_click
                    },
                    success: function(data) {
                        location.reload();
                    }
                });
                return false;
            }
        });
    });
</script>