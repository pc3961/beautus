<?php
session_start();  // Start the session

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './mail/Exception.php';
require './mail/PHPMailer.php';
require './mail/SMTP.php';

header('Content-Type: application/json');  // Set response to JSON format
$response = ['status' => 'error', 'message' => 'An error occurred.'];  // Default response

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];

    // Debugging: Check if OTP is set in the session
    if (!isset($_SESSION['otp'])) {
        $response['message'] = 'Session OTP not set.';
        echo json_encode($response);
        exit;
    }

    // Debugging: Check the OTP value
    if ($otp != $_SESSION['otp']) {
        $response['message'] = 'OTP does not match.';
        echo json_encode($response);
        exit;
    }

    unset($_SESSION['otp']);  // OTP matched, remove it from session

    // reCAPTCHA secret key
    $recaptcha_secret = '6LeK3DAqAAAAAG8yuTlWzVS_g-P1QFy1w4PC-pHd';  // Replace with your actual secret key
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify the reCAPTCHA response with Google
    $recaptcha_verify_url = "https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response";
    $recaptcha_verify_response = file_get_contents($recaptcha_verify_url);
    $response_keys = json_decode($recaptcha_verify_response, true);

    // Debugging: Print reCAPTCHA verification response
    if (!$response_keys) {
        $response['message'] = 'Failed to decode reCAPTCHA response.';
        echo json_encode($response);
        exit;
    }

    if (intval($response_keys["success"]) !== 1) {
        // reCAPTCHA failed
        $response['message'] = 'Please complete the CAPTCHA correctly.';
        echo json_encode($response);
        exit;
    }

    // reCAPTCHA successful, proceed with mailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'test@omesacreative.ca';  // SMTP username
        $mail->Password = 'ojgavpiqkxixbqpz';  // SMTP password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('test@omesacreative.ca', 'Test Omesacreative');
        $mail->addAddress('smerai@omesacreative.ca', 'Shailesh Merai');  // Add recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body = '<b>First Name:</b> ' . $_POST['fname'] . '<br><b>Last Name:</b> ' . $_POST['lname'] . '<br><b>Email:</b> ' . $_POST['bemail'] . '<br><b>Phone Number:</b> ' . $_POST['pnum'] . '<br><b>Company Name:</b> ' . $_POST['cname'] . '<br><b>Message:</b><br>' . nl2br($_POST['msg']);
        $mail->AltBody = 'First Name: ' . $_POST['fname'] . "\nLast Name: " . $_POST['lname'] . "\nEmail: " . $_POST['bemail'] . "\nPhone Number: " . $_POST['pnum'] . "\nCompany Name: " . $_POST['cname'] . "\nMessage:\n" . $_POST['msg'];

        // Send the email
        $mail->send();

        // If mail is sent successfully, return success response
        $response['status'] = 'success';
        $response['message'] = 'THANK YOU We are excited to hear from you! We will connect to discuss more about your interest.';

    } catch (Exception $e) {
        // If there was an error sending the email
        $response['message'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
    echo json_encode($response);
}
?>
