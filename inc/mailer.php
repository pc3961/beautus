<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './mail/Exception.php';
require './mail/PHPMailer.php';
require './mail/SMTP.php';
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Set your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'cgs@cgstechlab.com'; // SMTP username
    $mail->Password = 'pwupgeqllrsfgxpz'; // SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('pranav@cgstechlab.com', 'Shailesh Merai');
    $mail->addAddress('cpranavss65@gmail.com', 'Shailesh Merai');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission';
    $mail->Body = '<b>First Name:</b> ' . $_POST['fname'] . '<br><b>Last Name:</b> ' . $_POST['lname'] . '<br><b>Email:</b> ' . $_POST['bemail'] . '<br><b>Phone Number:</b> ' . $_POST['pnum'] . '<br><b>Company Name:</b> ' . $_POST['cname'] . '<br><b>Message:</b><br>' . nl2br($_POST['msg']);
    $mail->AltBody = 'First Name: ' . $_POST['fname'] . "\nLast Name: " . $_POST['lname'] . "\nEmail: " . $_POST['bemail'] . "\nPhone Number: " . $_POST['pnum'] . "\nCompany Name: " . $_POST['cname'] . "\nMessage:\n" . $_POST['msg'];

    $mail->send();
    echo 'Message has been sent';

    //Thank you Email
    $mail->clearAddresses(); // Clear all addresses for the next email
    $mail->addAddress($_POST['bemail']); // Send thank you email to the user's email
    $mail->Subject = 'Thank you for showing interest!';
    $mail->Body = 'Dear ' . $_POST['fname'] . ' ' . $_POST['lname'] . ',<br><br>' .
                  'Thank you for showing interest in our services. We have received your message and will get back to you shortly.<br><br>' .
                  'Best regards,<br>' .
                  'Wick Wipes'; // Customize with your company name
    $mail->AltBody = 'Dear ' . $_POST['fname'] . ' ' . $_POST['lname'] . ",\n\n" .
                     "Thank you for showing interest in our services. We have received your message and will get back to you shortly.\n\n" .
                     "Best regards,\n" .
                     "Wicked Wipes"; // Customize with your company name

    $mail->send();
    echo 'Thank you message has been sent';

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recaptcha_secret = '6LeK3DAqAAAAABPUmnvuJCf7ZUQwRq27KDavibFx'; // Replace with your actual secret key
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Make a POST request to the reCAPTCHA API
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        echo 'Please complete the CAPTCHA.';
    } else {
        echo 'CAPTCHA completed successfully.';
        // Continue with form processing
    }
}
?>