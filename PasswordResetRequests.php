<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('lib/PageTemplate.php');
include 'db.php';

$passwordUpdated = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        $message = "Passwords do not match.";
    } else {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $newHashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users u JOIN password_reset_requests prr ON u.id = prr.user_id SET u.password = ?, prr.token = NULL WHERE prr.token = ? AND prr.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        if ($stmt->execute([$newHashedPassword, $token])) {
            $message = "Password updated successfully,";
            $passwordUpdated = true;
        } else {
            $message =  "Password update failed.";
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

<html>
<head>
    <link rel="stylesheet" href="/css/ForgotPassword.css">
    <title>Reset Password</title>
</head>
<body> 
    <?php if (!$passwordUpdated): ?>
    <div class="password-reset-requests-container">
   
        <h2>Reset Your Password</h2> 
<form action="PasswordResetRequests.php" method="post">
<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <input type="password" name="new_password" placeholder="Enter your new password" required>
    <input type="password" name="confirm_password" placeholder="Confirm your new password" required>
    <button type="submit">Reset Password</button>

</form>
</div>
<?php else: ?>
    <div class="success-massage">
    <p><?php echo $message; ?>  <a class="login" href="/AccountLogin.php">Login </a></p> 
    </div>
    
    <?php endif; ?>
    
</body>
</html>