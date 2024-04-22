<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

$registrationSuccess = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (!$email) {
        $error = 'Invalid email address.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        if ($password !== $confirmPassword) {
            $error = "Passwords do not match!";
        } else {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            if (!$hashed_password) {
                $error = 'Could not hash the password';

            } else {
                // Kontrollera om användaren redan finns i databasen
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = "An account with that email already exists.";

            } else { // Om den inte finns, skapa en ny användare
                $sql = "INSERT INTO users (email, password, address, postcode, city, name) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$email, $hashed_password, $address, $postcode, $city, $name])) {
                    $_SESSION['user_email'] = $email;
                    $_SESSION['registrationSuccess'] = true;
                    header('Location: RegistrationConfirmation.php');
                    exit;
                } else {
                    $error = "An error occurred. Please try again.";
                }
            }
        }
    }
}
}

require_once ('lib/PageTemplate.php');
# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = " Register";
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
                <p>User<strong>&nbsp;REGISTER</strong></p>
                <form action="accountRegister.php" method="post">
                    <input class="input" type="email" name="email" placeholder="Enter Your Email" required>
                    <br /><br />
                    <input class="input" type="password" name="password" placeholder="Enter Your Password" required>
                    <br /><br />
                    <input class="input" type="password" name="confirmPassword" placeholder="Repeat Password" required>
                    <br /><br />
                    <input class="input" type="text" name="name" placeholder="Name" required>
                    <br /><br />
                    <input class="input" type="text" name="address" placeholder="Street address" required>
                    <br /><br />
                    <input class="input" type="text" name="postcode" placeholder="Postal code" required>
                    <br /><br />
                    <input class="input" type="text" name="city" placeholder="City" required>
                    <br /><br />
                    <button class="newsletter-btn"><i class="fa fa-envelope"></i> Register</button>
                    <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
</p>