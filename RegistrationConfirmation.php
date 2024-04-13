<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('lib/PageTemplate.php');
# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "My Title";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

if (isset($_SESSION['registrationSuccess']) && $_SESSION['registrationSuccess']) {
    $userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'an unknown email';
    echo "<h1>Registration Confirmation</h1>";
    echo "<p>You have registered as " . htmlspecialchars($userEmail) . ",the registration was successful, and you are now registered at Stefans SuperShop.</p>";
    unset($_SESSION['registrationSuccess']); 
    unset($_SESSION['user_email']); 
} else {
    echo "<p>Registration failed or you have accessed this page directly without registering.</p>";
}

?>
<head>
<link rel="stylesheet" href="/css/RegistrationConfirmation.css">
<title>Registration Confirmation</title>
</head>
<body>
    <div class="registration-container">
    
    <?php if (isset($_SESSION['registered_user_name'])): ?>
        <p>Thank you for registering, <?php echo $_SESSION['registered_user_name']; ?>!</p>
    <p>A confirmation has been sent to your email address. Please check your inbox for further instructions.</p>
    <?php else: ?>
        <p>Thank you for registering!</p>
    <?php endif; ?> 
    <a href="AccountLogin.php">log in here</a>
    
    </div>
</body>