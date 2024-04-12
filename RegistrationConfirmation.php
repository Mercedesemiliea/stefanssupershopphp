<?php
require_once('lib/PageTemplate.php');
# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "My Title";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

if(isset($_SESSION['registered_user_name'])) {
    $registeredUserName = $_SESSION['registered_user_name'];
} else {
   
    $registeredUserName = "Unknown User";
}

?>
<head>

<title>Registration Confirmation</title>
</head>
<body>
    <div class="registration-container">
    <h1>Registration Confirmation</h1>
    <p>Thank you for registering, <?php echo $registeredUserName; ?>!</p>
    <p>A confirmation has been sent to your email address. Please check your inbox for further instructions.</p>
     <a href="AccountLogin.php">log in here</a>
    <div class="stefans-supershop-image"></div>
    </div>
</body>