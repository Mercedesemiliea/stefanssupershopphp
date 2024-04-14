<?php
date_default_timezone_set('Europe/Stockholm');
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
        $userStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $userStmt->execute([$userEmail]);
        $userId = $userStmt->fetchColumn();

        if ($userId) {
            $token = bin2hex(random_bytes(32)); // Generate a secure token
            $expires = new DateTime('NOW');
            $expires->add(new DateInterval('PT24H')); // Token expires after 24 hours
            $expiresFormatted = $expires->format('Y-m-d H:i:s');
        

        // Insert token into the database
        $stmt = $pdo->prepare("INSERT INTO password_reset_requests (user_id, token, created_at) VALUES ((SELECT id FROM users WHERE email = ?), ?, ?)");
        if ($stmt->execute([$userEmail, $token, $expiresFormatted])) {
            $link = "http://localhost:8000/PasswordResetRequests.php?token=$token";
            $message = "Click on the following link to reset your password: <a href='$link'>$link</a>";
        } else {
            $message = "An error occurred. Please try again.";
        }
    }}
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
    <div class="forgot-password-link">
    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php else: ?>
    </div>
    <h2>Reset Your Password</h2>
    <p>Please enter your email address to receive a link to create a new password via email.</p>
    <form action="ForgotPassword.php" method="post">
    <label for="email">Your Email Address:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit" class="button-style">Send Reset Link</button>
</form>

    <?php endif; ?>
</div>
</html>

