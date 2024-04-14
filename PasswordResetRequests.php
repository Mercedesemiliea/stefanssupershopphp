<?php
date_default_timezone_set('Europe/Stockholm');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('lib/PageTemplate.php');
include 'db.php';


$message = '';
$updateMessage = '';
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
                    $newHashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    if ($updateStmt->execute([$newHashedPassword, $tokenData['user_id']])) {
                        $message = "Your password has been updated successfully.";
                       
                        // Rensa token från databasen
                        $pdo->prepare("DELETE FROM password_reset_requests WHERE token = ?")->execute([$token]);
                       
                        // Redirect eller logga in användaren här
                        
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
    $TPL->PageTitle = "My Title";
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
<?php if (empty($message)): ?>
    <div class="password-reset-requests-container">
   
        <h2>Reset Your Password</h2> 
        <form action="PasswordResetRequests.php?token=<?= htmlspecialchars($token) ?>" method="post">
    <input type="password" name="new_password" placeholder="New password" required>
    <input type="password" name="confirm_password" placeholder="Confirm new password" required>
    <button type="submit">Reset Password</button>
</form>

</form>
</div>
<?php else: ?>
    <div class="success-massage">
    <p><?php echo $message; ?>  <a class="login" href="/AccountLogin.php">Login </a></p> 
    </div>
    
    <?php endif; ?>
    
</body>
</html>