<?php
require_once('lib/PageTemplate.php');
include 'db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}


$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $error = 'Invalid email address.';
        error_log('Invalid email format.');
        exit;
    }

    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Spara användarens ID och IP-adress i sessionen
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['loginSuccess'] = true; 


       
        // Lägg till post i login_sessions-tabellen
        $stmt = $pdo->prepare("INSERT INTO login_sessions (user_id, ip_address, login_time) VALUES (?, ?, NOW())");
        $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);

        header('Location: index.php');
        exit();
    } else {
        $error = 'Incorrect username or password';
        error_log('Login failed for user . $email');
    }
}




# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Login";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}
?>
<p>
<div class="row">

<div class="row">
                <div class="col-md-12">
                    <div class="newsletter">
 

                        <p>User<strong>&nbsp;LOGIN</strong></p>
                        <form action="AccountLogin.php" method="post">
                            <input class="input" type="email" name="email" placeholder="Enter Your Email">
                            <br/>
                            <br/>
                            <input class="input" type="password" name="password" placeholder="Enter Your Password">
                            <br/>
                            <br/>
                            <button class="newsletter-btn"><i class="fa fa-envelope"></i> Login</button>
                        </form>
                        <a href="ForgotPassword.php">Lost password?</a>
                        <?php if (isset($error)): ?>
                            <div class="error-message"><?php echo $error; ?></div>
                                <?php endif; ?>
                                
                    </div>
                </div>
            </div>


</div>
    

</p>