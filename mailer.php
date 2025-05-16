<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 
// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

try {
    loadEnv(__DIR__ . '/.env');
} catch (Exception $e) {
    error_log('Failed to load .env file: ' . $e->getMessage());
}

function sendWelcomeEmail($toEmail, $username) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.titan.email';
        $mail->SMTPAuth   = true;
        $mail->Username  = $_ENV['SMTP_USER'] ?? 'info@techalphahub.com';
        $mail->Password  = $_ENV['SMTP_PASS'] ?? 'Uyioobong155@';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $_ENV['SMTP_PORT'] ?? 465;

        // Recipients
        $mail->setFrom(
            $_ENV['FROM_EMAIL'] ?? 'info@techalphahub.com',
            $_ENV['FROM_NAME'] ?? 'AuthSystem'
        );
        $mail->addAddress($toEmail, $username);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Our Service!';
        
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .footer { margin-top: 20px; padding: 10px; text-align: center; font-size: 0.8em; color: #6c757d; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Welcome to AuthSystem!</h1>
                    </div>
                    <div class='content'>
                        <p>Hello $username,</p>
                        <p>Thank you for registering with us. We're excited to have you on board!</p>
                        <p>Your account has been successfully created and you can now log in using the credentials you provided during registration.</p>
                        <p>If you have any questions, feel free to contact our support team.</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " AuthSystem. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        $mail->AltBody = "Hello $username,\n\nThank you for registering with us. We're excited to have you on board!\n\nYour account has been successfully created and you can now log in using the credentials you provided during registration.\n\nIf you have any questions, feel free to contact our support team.\n\nBest regards,\nAuthSystem Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>