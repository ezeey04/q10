<?php

header('Content-Type: application/json; charset=UTF-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendError($message, $code = 500)
{
    http_response_code($code);

    echo json_encode([
        'success' => false,
        'message' => $message
    ]);

    exit;
}

try {

    require __DIR__ . '/PHPMailer/src/Exception.php';
    require __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/PHPMailer/src/SMTP.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Invalid request method.', 405);
    }

    if (!empty($_POST['website'])) {
        echo json_encode([
            'success' => true,
            'message' => 'OK'
        ]);
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $service === '' || $message === '') {
        sendError('Please fill in all required fields.', 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Please enter a valid email address.', 400);
    }

    $to = 'Q10qatar@gmail.com';
    $fromEmail = 'info@qtenservice.com';
    $fromName = 'Q-TEN Website';
    $subject = 'New Enquiry - Q-TEN Website';

    $emailBody =
        "NEW ENQUIRY - Q-TEN WEBSITE\n" .
        "====================================\n\n" .
        "Name: " . $name . "\n" .
        "Email: " . $email . "\n" .
        "Phone: " . $phone . "\n" .
        "Service: " . $service . "\n\n" .
        "Message:\n" .
        "------------------------------------\n" .
        $message . "\n" .
        "------------------------------------\n\n" .
        "Submitted from: Q-TEN Website\n" .
        "Website: https://qtenservice.com\n";

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@qtenservice.com';

    // ENTER YOUR ACTUAL INFO@QTENSERVICE.COM PASSWORD HERE
    $mail->Password = 'Qten@2022';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($to);
    $mail->addReplyTo($email, $name);

    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body = $emailBody;

    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Your enquiry has been sent successfully.'
    ]);

} catch (\Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'SERVER ERROR: ' . $e->getMessage()
    ]);
}

exit;
?>