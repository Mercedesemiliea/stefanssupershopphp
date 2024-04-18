<?php
date_default_timezone_set('Europe/Stockholm');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once ('lib/PageTemplate.php');
include 'db.php';

$passwordMismatch = false;
$message = '';
$passwordUpdated = false;
$token = $_GET['token'] ?? ''; // Hämta token från URL



if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT user_id, created_at FROM password_reset_requests WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stmt->execute([$token]);
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tokenData) {
        $createdAt = new DateTime($tokenData['created_at']);
        $now = new DateTime();
        if ($createdAt > $now->sub(new DateInterval('PT24H'))) {
            // Token är giltig och inte utgången
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {
                if ($_POST['new_password'] === $_POST['confirm_password']) {
                    $passwordMismatch = true;
                    $message = "Passwords do not match.";
                    $newHashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    if ($updateStmt->execute([$newHashedPassword, $tokenData['user_id']])) {
                        $message = "Password updated successfully,";
                        $passwordUpdated = true;

                        // Rensa token från databasen
                        $pdo->prepare("DELETE FROM password_reset_requests WHERE token = ?")->execute([$token]);
                        // Logga in användaren här
                        $_SESSION['success_message'] = "Your password has been updated successfully. You can now login with your new password.";
                        header('Location: SuccessfulUpdatePassword.php');
                        exit;


                    } else {
                        $message = "Failed to update your password.";
                    }
                } else {
                    $message = "Passwords do not match.";
                }
            }
        } else {
            $message = "Invalid or expired token.";
        }
    } else {
        $message = "Invalid or expired token.";
    }
} else {
    $message = "No token provided.";
}


if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Password Reset Request";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}
?>

<html>

<head>
    <link rel="stylesheet" href="/css/ForgotPassword.css">
    <title>Reset Password</title>
</head>

<body>
    <div class="password-reset-requests-container">
        <h2>Reset Your Password</h2>
        <form action="PasswordResetRequests.php?token=<?= htmlspecialchars($token) ?>" method="post">
            <input type="password" name="new_password" placeholder="New password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit">Reset Password</button>
            <?php if ($message): ?>
                <p class="error-message">Passwords do not match. Please try again.</p>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>