<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRF($_POST['csrf_token'])) {
    $response['message'] = 'Invalid security token';
    echo json_encode($response);
    exit;
}

// Validate and sanitize input
$firstName = sanitizeInput($_POST['first_name'] ?? '');
$lastName = sanitizeInput($_POST['last_name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$subject = sanitizeInput($_POST['subject'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($firstName)) {
    $errors[] = 'First name is required';
}

if (empty($lastName)) {
    $errors[] = 'Last name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($subject)) {
    $errors[] = 'Subject is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (!empty($errors)) {
    $response['message'] = implode(', ', $errors);
    echo json_encode($response);
    exit;
}

// Get client information
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Save to database
$pdo = getDBConnection();
if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO contact_inquiries 
            (first_name, last_name, email, subject, message, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $firstName,
            $lastName,
            $email,
            $subject,
            $message,
            $ipAddress,
            $userAgent
        ]);
        
        $inquiryId = $pdo->lastInsertId();
        
        // Send email notification
        $emailSubject = "New Contact Inquiry - " . $subject;
        $emailMessage = "
        <html>
        <head>
            <title>New Contact Inquiry</title>
        </head>
        <body>
            <h2>New Contact Inquiry from KGF Pharmaceuticals Website</h2>
            <p><strong>Inquiry ID:</strong> #$inquiryId</p>
            <p><strong>Name:</strong> $firstName $lastName</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Message:</strong></p>
            <div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;'>
                " . nl2br($message) . "
            </div>
            <hr>
            <p><strong>Additional Information:</strong></p>
            <p><strong>IP Address:</strong> $ipAddress</p>
            <p><strong>User Agent:</strong> $userAgent</p>
            <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
        </body>
        </html>
        ";
        
        // Send email to company
        $emailSent = sendEmail(TO_EMAIL, $emailSubject, $emailMessage);
        
        // Send confirmation email to user
        $confirmationSubject = "Thank you for contacting KGF Pharmaceuticals";
        $confirmationMessage = "
        <html>
        <head>
            <title>Thank you for your inquiry</title>
        </head>
        <body>
            <h2>Thank you for contacting KGF Pharmaceuticals</h2>
            <p>Dear $firstName $lastName,</p>
            <p>Thank you for your inquiry. We have received your message and will get back to you as soon as possible.</p>
            
            <div style='background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #4285f4;'>
                <h3>Your Inquiry Details:</h3>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong></p>
                <div style='background-color: white; padding: 10px; border-radius: 3px; margin: 10px 0;'>
                    " . nl2br($message) . "
                </div>
                <p><strong>Inquiry ID:</strong> #$inquiryId</p>
            </div>
            
            <p>Our team typically responds within 24-48 hours during business days.</p>
            
            <hr>
            <p>Best regards,<br>
            <strong>KGF Pharmaceuticals Team</strong><br>
            Email: kgfpharmaceuticals@gmail.com<br>
            Phone: +91-9216226227, +91-9906253881</p>
        </body>
        </html>
        ";
        
        sendEmail($email, $confirmationSubject, $confirmationMessage);
        
        $response['success'] = true;
        $response['message'] = 'Thank you for your message! We will get back to you soon.';
        $response['inquiry_id'] = $inquiryId;
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $response['message'] = 'There was an error processing your request. Please try again later.';
    }
} else {
    $response['message'] = 'Database connection failed. Please try again later.';
}

echo json_encode($response);
?>