<?php

class EmailSendingCron {

	public static function DefaultSendingCron() {

		//add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));

        if( isset($_GET["function_call"]) && $_GET["function_call"] == 'send' ) {

            // Construct Query & get users
            global $wpdb;
            $sql_query = "SELECT * FROM `" . $wpdb->prefix . "sndr_mail_users_info` ";
            $sql_query .= "WHERE `subscribe`=1 AND `delete`=0 AND `black_list`=0";
            $subscribed_users = $wpdb->get_results( $sql_query, ARRAY_A );

            foreach ($subscribed_users as $user) {


                $email_to = $user['user_email'];
                // Subject
                setlocale(LC_ALL, 'sr_RS.UTF-8@latin', 'sr_RS.UTF-8@Latn', 'sr_CS.UTF-8@Latn');
                $date =  strftime("%e %B %Y");
                $email_subject = 'Naslovne strane za ' . $date;
                $sources = NaslovnaStrana::PrepareDataEmail();
                $images = NaslovnaStrana::ReturnImages( '50%', $sources, 'max-width: 445px;min-width: 290px;' );

                // User Name
                $parts = explode( "@", $user['user_email'] );
                $username = $parts[0];
                $email_body = "<html><body><h2>Zdravo $username,</h2><p>Pregled stampe za danas:</p>$images</body></html>";

                // Unsubscribe link
                $unsubscribe_link = home_url( '/?sbscrbr_unsubscribe=true&code=' . $user['unsubscribe_code'] . '&id=' . $user['id_user'] );
                $email_body .= '<p style="text-align: center;font-size: 11px;display:block;">Ako zelite da se odjavite sa liste to mozete ucinit na ovom linku: <a href="' . $unsubscribe_link . '" >odjava!</a></p>';

                EmailSendingCron::SendEmail( $email_to, $email_subject, $email_body );
            }
        } else {
            return;
        }
	}
	private function SendEmail( $email_to, $email_subject, $email_body ) {

		$sender_name = "Naslovna Strana";
		$sender_email = "info@naslovnastrana.com";

		$headers[] = "From: $sender_name <$sender_email>";
		$headers[] = 'Content-type: text/html';

		
		wp_mail( $email_to, $email_subject, $email_body, $headers );
	}
}

?>