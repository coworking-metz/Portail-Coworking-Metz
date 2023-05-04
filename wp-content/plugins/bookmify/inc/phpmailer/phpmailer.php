<?php
namespace Bookmify;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class PHP Mailer Custom
 */
class PHPMailerCustom{
 
	
	
	/**
     * Construct
	 * @since 1.0.0
     */
	public function __construct() {
		
		require( BOOKMIFY_PATH . 'inc/phpmailerAPI/autoload.php' );

	}
	

	public function mailer($to, $from, $subject, $body, $headers){
		
		$mail = new PHPMailer(true);
		
		try {
			//Server settings
			//$mail->SMTPDebug = 2;    

			$smtpHost 			= get_option('bookmify_be_not_smtp_host', '');
			$smtpPort 			= get_option('bookmify_be_not_smtp_port', '');
			$smtpUsername 		= get_option('bookmify_be_not_smtp_username', '');
			$smtpPassword 		= get_option('bookmify_be_not_smtp_pass', '');
			$smtpSecure 		= get_option('bookmify_be_not_smtp_secure', '');
			
			if(BOOKMIFY_MODE == 'demo' || BOOKMIFY_MODE == 'dev'){
				$smtpHost 		= 'mail.frenify.com';
				$smtpPort 		= 25;
				$smtpUsername 	= 'bookmify@frenify.com';
				$smtpPassword 	= 'Bookmify2019!';
				$smtpSecure 	= 'ssl';
			}
			
			// Enable verbose debug output
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host       	= $smtpHost;							// Specify main and backup SMTP servers
			$mail->SMTPAuth   	= true;									// Enable SMTP authentication
			$mail->Username   	= $smtpUsername;						// SMTP username
			$mail->Password   	= $smtpPassword;						// SMTP password
			$mail->SMTPSecure 	= $smtpSecure;							// Enable TLS encryption, `ssl` also accepted
			$mail->Port       	= $smtpPort;							// TCP port to connect to
			//Recipients
			$mail->setFrom($from[1], $from[0]); 						// [0] name, [1] email
			$mail->addAddress($to);										// Add a recipient
			$mail->addReplyTo($from[1]);
			
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');

			// Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments

			// Content
			$mail->CharSet = 'UTF-8';
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $body;
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			//return 'Message has been sent';
		} catch (Exception $e) {
//			return $mail->ErrorInfo;
			wp_mail( $to, $subject, $body, $headers );
		}
		
		
	}
	
		
}


