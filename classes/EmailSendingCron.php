<?php

class EmailSendingCron {

    public static function DefaultSendingCron() {

        if( isset($_GET["function_call"]) && $_GET["function_call"] == 'send' ) {

            $subscribed_users = NS_Email_List::get_users_emails();
            foreach ($subscribed_users as $user) {
                $user_email = $user['email'];
                $email_to = $user_email;
                // Subject
                setlocale(LC_ALL, 'sr_RS.UTF-8@latin', 'sr_RS.UTF-8@Latn', 'sr_CS.UTF-8@Latn');
                $date =  strftime("%e %B %Y");
                $email_subject = 'Naslovne strane za ' . $date;
                $sources = NaslovnaStrana::PrepareDataEmail();
                $images = NaslovnaStrana::ReturnImages( '50%', $sources, 'max-width: 445px;min-width: 290px;' );

                // User Name
                $parts = explode( "@", $user_email );
                $username = ucfirst($parts[0]);
                $email_body = "<html><body><h2>Zdravo $username,</h2><p>Pregled stampe za danas:</p>$images</body></html>";

                // Unsubscribe link
                $code = base64_encode($user_email);
                $unsubscribe_link = home_url( '/?_unsubscribe=true&code=' . $code );
                $email_body .= '<p style="text-align: center;font-size: 11px;display:block;">Ako zelite da se odjavite sa liste to mozete ucinit na ovom linku: <a href="' . $unsubscribe_link . '" >odjava!</a></p>';

                EmailSendingCron::SendEmail( $email_to, $email_subject, $email_body );
            }
        } else {
            return;
        }
    }
    public static function SendWelcomeEmail( $email_to ) {

        // Subject
        $email_subject = 'Uspesno prijava na NaslovnaStrana.com';
        $sources = NaslovnaStrana::PrepareDataEmail();
        $images = NaslovnaStrana::ReturnImages( '50%', $sources, 'max-width: 445px;min-width: 290px;' );

        // User Name
        $parts = explode( "@", $email_to );
        $username = ucfirst($parts[0]);
        $email_body = "<html><body><h2>Zdravo $username,</h2>
                <p>Hvala Vam na registraciji na nasoj listi.</p>
                <p>Ovo je pregled stampe za danas:</p>$images</body></html>";
        // Unsubscribe link
        $code = base64_encode($email_to);
        $unsubscribe_link = home_url( '/?_unsubscribe=true&code=' . $code );
        $email_body .= '<p style="text-align: center;font-size: 11px;display:block;">Ako zelite da se odjavite sa liste to mozete ucinit na ovom linku: <a href="' . $unsubscribe_link . '" >odjava!</a></p>';

        EmailSendingCron::SendEmail( $email_to, $email_subject, $email_body );
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