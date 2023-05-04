<?php
namespace Bookmify;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class PHP Mailer Custom
 */
class PHPMailerCustom{
 
	
	
	/**
     * Construct
	 * @since 1.0.0
     */
	public function __construct() {
		
		require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
		require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
		require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

	}
	

	public function mailer($to, $from, $subject, $body, $headers){
		
		$mail = new PHPMailer(true);
		
		try {
			//Server settings
			//$mail->SMTPDebug = 1;    

			$smtpHost 			= get_option('bookmify_be_not_smtp_host', '');
			$smtpPort 			= get_option('bookmify_be_not_smtp_port', '');
			$smtpUsername 		= get_option('bookmify_be_not_smtp_username', '');
			$smtpPassword 		= get_option('bookmify_be_not_smtp_pass', '');
			$smtpSecure 		= get_option('bookmify_be_not_smtp_secure', '');
			
			if(BOOKMIFY_MODE == 'demo' || BOOKMIFY_MODE == 'dev'){
				
				$smtpHost 		= 'smtp.hostinger.com';
				$smtpPort 		= 587;
				$smtpUsername 	= 'bookmify@frenify.net';
				$smtpPassword 	= 'A:1m&op3';
				$smtpSecure 	= 'SSL/TLS';
				
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
		} catch (Exception2 $e) {
//			return $mail->ErrorInfo;
			wp_mail( $to, $subject, $body, $headers );
		}
		
		
	}
	
		
}


