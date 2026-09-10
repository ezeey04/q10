<?php

/*
============================================================
 Q-TEN ENQUIRY EMAIL HANDLER
 Sends website enquiries to:

 Q10qatar@gmail.com
============================================================
*/


/* ----------------------------------------------------------
   JSON RESPONSE
---------------------------------------------------------- */

header('Content-Type: application/json; charset=UTF-8');


/* ----------------------------------------------------------
   ONLY ACCEPT POST
---------------------------------------------------------- */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);

    exit;
}


/* ----------------------------------------------------------
   HONEYPOT SPAM CHECK
---------------------------------------------------------- */

if (!empty($_POST['website'])) {

    echo json_encode([
        'success' => true,
        'message' => 'Thank you. Your enquiry has been received.'
    ]);

    exit;
}


/* ----------------------------------------------------------
   EMAIL SETTINGS
---------------------------------------------------------- */

$to = 'Q10qatar@gmail.com';

$subject = 'New Q-TEN Website Enquiry';


/*
IMPORTANT:

Change this to an email address using YOUR OWN DOMAIN
when your website is hosted.

Example:

$from = 'website@q-ten.com';

Using the visitor's email as "From" can cause delivery
problems and spam rejection.
*/

$from = 'website@yourdomain.com';


/* ----------------------------------------------------------
   GET FORM DATA
---------------------------------------------------------- */

$name = isset($_POST['name'])
    ? trim($_POST['name'])
    : '';

$email = isset($_POST['email'])
    ? trim($_POST['email'])
    : '';

$phone = isset($_POST['phone'])
    ? trim($_POST['phone'])
    : '';

$service = isset($_POST['service'])
    ? trim($_POST['service'])
    : '';

$userMessage = isset($_POST['message'])
    ? trim($_POST['message'])
    : '';


/* ----------------------------------------------------------
   VALIDATION
---------------------------------------------------------- */

if ($name === '') {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Please enter your name.'
    ]);

    exit;
}


if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);

    exit;
}


if ($service === '') {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Please select a service.'
    ]);

    exit;
}


if ($userMessage === '') {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Please enter your requirement.'
    ]);

    exit;
}


/* ----------------------------------------------------------
   SANITIZE HEADER VALUES
---------------------------------------------------------- */

$name = preg_replace(
    "/[\r\n]+/",
    " ",
    $name
);

$email = preg_replace(
    "/[\r\n]+/",
    "",
    $email
);

$phone = preg_replace(
    "/[\r\n]+/",
    " ",
    $phone
);

$service = preg_replace(
    "/[\r\n]+/",
    " ",
    $service
);


/* ----------------------------------------------------------
   EMAIL BODY
---------------------------------------------------------- */

$emailBody = "";

$emailBody .= "Q-TEN WEBSITE ENQUIRY";
$emailBody .= "\n";
$emailBody .= "====================================";
$emailBody .= "\n\n";

$emailBody .= "Name: ";
$emailBody .= $name;
$emailBody .= "\n";

$emailBody .= "Email: ";
$emailBody .= $email;
$emailBody .= "\n";

$emailBody .= "Phone: ";
$emailBody .= ($phone !== '' ? $phone : 'Not provided');
$emailBody .= "\n";

$emailBody .= "Service: ";
$emailBody .= $service;
$emailBody .= "\n\n";

$emailBody .= "Requirement:";
$emailBody .= "\n";
$emailBody .= "------------------------------------";
$emailBody .= "\n";
$emailBody .= $userMessage;
$emailBody .= "\n";
$emailBody .= "------------------------------------";
$emailBody .= "\n\n";

$emailBody .= "Submitted from Q-TEN website.";
$emailBody .= "\n";


/* ----------------------------------------------------------
   EMAIL HEADERS
---------------------------------------------------------- */

$headers = [];

$headers[] =
    'MIME-Version: 1.0';

$headers[] =
    'Content-Type: text/plain; charset=UTF-8';

$headers[] =
    'From: Q-TEN Website <' . $from . '>';

$headers[] =
    'Reply-To: ' . $email;


/* ----------------------------------------------------------
   SEND EMAIL
---------------------------------------------------------- */

$sent = mail(
    $to,
    $subject,
    $emailBody,
    implode("\r\n", $headers)
);


/* ----------------------------------------------------------
   RESPONSE
---------------------------------------------------------- */

if ($sent) {

    echo json_encode([
        'success' => true,
        'message' =>
            'Thank you. Your enquiry has been sent successfully. We will contact you soon.'
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' =>
            'The enquiry could not be sent right now. Please contact Q-TEN directly by phone or email.'
    ]);

}