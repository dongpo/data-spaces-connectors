<?php
	ini_set('upload_max_filesize', '10M');
	ini_set('post_max_size', '10M');
	//以下是內容
	session_start();
   	if (isset($_REQUEST['_SESSION'])) die("Get lost Muppet!");
		$frommail='';
		$frompass='';
		$mailhost = getenv('TTC_SMTP_HOST') ?: '';
		$mailport=25;
		$mailssl=0;

    // Automatic test email disabled in public source export.
/*
require 'func/PHPMailerAutoload.php';
//Create a new PHPMailer instance
$mail = new PHPMailer;
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = $mailhost;
		$mail->Port = $mailport;
		$mail->CharSet = "utf-8";
		$mail->Encoding = "base64";
		$mail->Username = $frommail;
		$mail->Password = $frompass;
//Set who the message is to be sent from
$mail->setFrom($frommail, 'First Last');
//Set an alternative reply-to address
//$mail->addReplyTo('recipient@example.invalid', 'First Last');
//Set who the message is to be sent to
$mail->addAddress(getenv('TTC_SMTP_TEST_RECIPIENT') ?: 'recipient@example.invalid', 'Demo recipient');
//Set the subject line
$mail->Subject = 'Demo email example';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
//Replace the plain text body with one created manually
$mail->Body = 'Demo email example';
//$mail->AltBody = 'Demo email example';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
*/

   ?>