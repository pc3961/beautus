<?php
session_start();
use PHPMailer\PHPMailer\Exception;
require './mail/Exception.php';
require './mail/PHPMailer.php';
require './mail/SMTP.php';
// Function to generate a random OTP
function generateOtp($length = 6) {
    return rand(100000, 999999); // Generate a 6-digit OTP
}

// Check if email is sent through POST request
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['email'])) {
    $email = $data['email'];
    $otp = generateOtp();

    // Store the OTP in session or database
    $_SESSION['otp'] = $otp;

    // Setup PHPMailer to send the OTP
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'test@omesacreative.ca';  // SMTP username
        $mail->Password = 'ojgavpiqkxixbqpz';  // SMTP password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('marketing@wickedwipes.ca', 'Test Omesacreative');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = 'Your OTP code is: <b>' . $otp . '</b>';
        $mail->AltBody = 'Your OTP code is: ' . $otp;

        $mail->send();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
