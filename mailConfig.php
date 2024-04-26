<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';




function getMailer()
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.ethereal.email';
        $mail->SMTPAuth = true;
        $mail->Username = 'lenora60@ethereal.email';
        $mail->Password = 'xtx7ry9gD1Rfk5S3uR';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Avsändare
        $mail->setFrom('no-reply@yourdomain.com', 'Your Application Name');

        return $mail;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return null;
    }
}