<?php

require_once('lib/PageTemplate.php');
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Spara användarens ID och IP-adress i sessionen
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];

        // Lägg till post i login_sessions-tabellen
        $stmt = $pdo->prepare("INSERT INTO login_sessions (user_id, ip_address, login_time) VALUES (?, ?, NOW())");
        $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);

        header('Location: index.php');
        exit();
    } else {
        $error = 'Fel användarnamn eller lösenord';
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
                        <a href="">Lost password?</a>
                        <?php if (isset($error)): ?>
                            <div class="error-message-lost-password"><?php echo $error; ?></div>
                                <?php endif; ?>
                    </div>
                </div>
            </div>


</div>
    

</p>