<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);


//Server settings        
$mail->isSMTP();                                            //Send using SMTP
$mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
$mail->SMTPAuth = true;                                   //Enable SMTP authentication
$mail->Username = 'cgs@cgstechlab.com';                     //SMTP username
$mail->Password = 'pwupgeqllrsfgxpz';                               //SMTP password
$mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
$mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//Recipients
$mail->setFrom('pranav@cgstechlab.com', 'Mailer');
$mail->addAddress('cgs@cgstechlab.com', 'Joe User');     //Add a recipient

//Content
$mail->Body = !empty($_POST['fname']) ? ' First Name: ' . $_POST['fname'] . '<br>' : '';
$mail->Body = !empty($_POST['lname']) ? ' Last Name: ' . $_POST['lname'] . '<br>' : '';
$mail->Body .= !empty($_POST['cname']) ? 'Company: ' . $_POST['cname'] . '<br>' : '';
$mail->Body .= !empty($_POST['bemail']) ? 'Email: ' . $_POST['bemail'] . '<br>' : '';
$mail->Body .= !empty($_POST['pnum']) ? 'Phone: ' . $_POST['pnum'] . '<br>' : '';
$mail->Body .= 'message:<br>';
$mail->Body .= $_POST['msg'];

if (!$mail->Send()) {
    echo "Error sending: " . $mail->ErrorInfo;
    ;
} else {
    echo 'Message Sent Successfully';
}