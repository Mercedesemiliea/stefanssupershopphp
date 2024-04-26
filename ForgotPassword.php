<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Europe/Stockholm');
require_once ('lib/PageTemplate.php');
include 'db.php';
require_once 'mailConfig.php';



$error = "";
$message = "";
$linkSent = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $userEmail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($userEmail) {
        $userStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $userStmt->execute([$userEmail]);
        $userId = $userStmt->fetchColumn();

        if ($userId) {
            $token = bin2hex(random_bytes(32));
            $expires = new DateTime('NOW');
            $expires->add(new DateInterval('PT24H'));
            $expiresFormatted = $expires->format('Y-m-d H:i:s');

            $stmt = $pdo->prepare("INSERT INTO password_reset_requests (user_id, token, created_at) VALUES (?, ?, ?)");
            if ($stmt->execute([$userId, $token, $expiresFormatted])) {
                $link = "http://localhost:8000/PasswordResetRequests.php?token=$token";
                $linkSent = true;
                
                
                

                $mail = getMailer();
                if ($mail) {
                    $mail->From = "stefans@superdupershop.com"; 
                    $mail->setFrom('stefans@superdupershop.com', 'Stefans SuperShop');
                    $mail->FromName = "Hello";
                    $mail->addAddress($userEmail);
                    $mail->addReplyTo("noreply@superdupershop.com", "No-Reply");
                    $mail->isHTML(true);

                    $mail->Subject = 'Password Reset Request';
                    $mail->Body = '
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #D10024;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .content h1 {
            color: #D10024;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            color: #333;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #D10024;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Password Reset Request</h1>
        <p>Please click on the following link to reset your password:</p>
        <p><a href="' . $link . '">' . $link . '</a></p>
    </div>
    <div class="footer">
        Regards,<br>
        Stefans SuperShop Team
    </div>
</body>
</html>
';



                    if (!$mail->send()) {
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                        
                    } else {
                        $message = 'Password reset link has been sent to your email address.';
                    }
                }
            } else {
                echo "An error occurred. Please try again.";
            }
        } else {
            echo "No account found with that email address.";
        }
    } else {
        echo "Please enter a valid email address.";
    }
}



if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Forgot Password";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}
?>


<head>

    <link rel="stylesheet" href="/css/ForgotPassword.css">
    <title>Password Reset</title>
</head>
<div class="forgot-password-container">
    <div class="forgot-password-link">
        <?php if ($linkSent): ?>
            <p><?php echo $message; ?></p>
        <?php else: ?>
        </div>
        <h2>Reset Your Password</h2>
        <p>Please enter your email address to receive a link to create a new password.</p>
        <form action="ForgotPassword.php" method="post">
            <label for="email">Your Email Address:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit" class="button-style">Send Reset Link</button>
            <?php if ($error): ?>
                <div class="error-message">Please enter a valid email address. </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>

</html>