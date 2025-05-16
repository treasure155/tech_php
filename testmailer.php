<?php
require 'mailer.php';

if (sendWelcomeEmail('recipient@example.com', 'Test User')) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>