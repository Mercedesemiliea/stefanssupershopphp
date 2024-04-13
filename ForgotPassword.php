<?php
require_once('lib/PageTemplate.php');
include 'db.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])){
    $userEmail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$userEmail) {
        echo "Please enter a valid email address.";
    } else {
        $token = bin2hex(random_bytes(32)); // Säker token
        $expires = new DateTime('NOW');
        $expires->add(new DateInterval('PT24H')); // Token är giltig i 24 timmar

        // Sparaa token i databasen
        $stmt = $pdo->prepare("INSERT INTO password_reset_requests (user_id, token, created_at) VALUES ((SELECT id FROM users WHERE email = ?), ?, ?)");
        if ($stmt->execute([$userEmail, $token, $expires->format('Y-m-d H:i:s')])) {
            
            $link = "http://yourdomain.com/reset_password.php?token=$token";
            mail($userEmail, "Password Reset", "Please click on the following link to reset your password: $link");

            $message = "A password reset link has been sent to your email.";
        } else {
            $message = "An error occurred. Please try again.";
        }
    }
}

if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "My Title";
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
    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php else: ?>
    <h2>Reset Your Password</h2>
    <p>Please enter your email address to receive a link to create a new password via email.</p>
    <form action="PasswordResetRequests.php" method="post">
    <label for="email">Your Email Address:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit" class="button-style">Send Reset Link</button>
</form>

    <?php endif; ?>
</div>
</html>

