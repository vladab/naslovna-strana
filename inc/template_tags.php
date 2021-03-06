<?php
/**
 * Created by Vladica Bibeskovic.
 * Date: 24.10.14., 23.13 
 */

function ns_print_input_form() {
    if ( isset( $_POST['email_address'] ) ) {
        $res = NS_Email_List::check_if_email_in_database( $_POST['email_address'] );
        if( $res == 'success') {
            $report_message = '<p class="success">Uspesna prijava! Uskoro cete dobiti svoje prve naslovne strane.</p>
                           <p class="success">Hvala na prijavi!</p>';
        } else {
            $report_message = '<p class="error">Doslo je do greske</p>';
        }
    } else
        $report_message = ''
    ?>
<div class="ns_headline">
    <form method="post" action="" class="subscrbr-sign-up-form">
    <?php echo $report_message; ?>
    <p class="ns_input_div">
        <input type="text" name="email_address" value="" class="ns_input" placeholder="Unesite Vašu email adresu..."
               onblur="if (this.value == '')  {this.value = 'Unesite Vašu email adresu...';}"
               onfocus="if (this.value == 'Unesite Vašu email adresu...') {this.value = '';}"/>
    </p>
        <input type="hidden" name="nss_subscription">
        <input type="submit" value="Prijava" name="submit_email" class="submit ns_submit" />
    </form>
</div>
<?php
}

function ns_print_preview_images() {
    ?>
    <div class="ns_images" >
        <?php if( class_exists('NaslovnaStrana' ) ) { echo NaslovnaStrana::ReturnImages(); }?>
        <?php if( class_exists('EmailSendingCron' ) ) { EmailSendingCron::DefaultSendingCron(); }?>
    </div>
    <?php
}

/**
 * Function for usage in templates
 * Usage: <?php if( function_exists('maybe_print_all' ) ) { meybe_print_all(); }?>
 */
function maybe_print_all() {
    echo '<div class="ns_headline"><p>Svako jutro naslovne strane dnevnih novina u Vašem sandučetu!</p></div>';
    ns_print_input_form();
    ns_print_preview_images();
}