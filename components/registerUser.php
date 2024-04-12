<?php

function registerUser($pdo, $username, $email, $password, $confirmPassword) {
    // Kontrollera om lösenorden matchar
    if ($password != $confirmPassword) {
        echo "Lösenorden matchar inte.";
        return false;
    }

    // Förbered och exekvera INSERT-frågan
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        return true;
    } catch (PDOException $e) {
        // Hantera eventuella undantag, till exempel om e-post eller användarnamn redan finns
        echo "Registrering misslyckades: " . $e->getMessage();
        return false;
    }
}