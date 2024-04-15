<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('lib/PageTemplate.php');


if (empty($_SESSION['success_message'])) {
    header('Location: SuccesfullUpdatePassword.php'); 
    exit;
    
}
$message = $_SESSION['success_message'];
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Successful Password Update";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}



session_unset();
session_destroy();

?>


<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="/css/ForgotPassword.css">

</head>
<body>
    <div class="success-massage">
        <h1>Password Updated Successfully</h1>
        <p><?= htmlspecialchars($message); ?></p>
        <a href="/AccountLogin.php">Click here to Login</a>
    </div>
</body>
</html>