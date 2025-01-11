<?php
session_start();

$db = new SQLite3('database.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    $password_verify = htmlspecialchars($_POST['password_verify'] ?? '');

    // Check if the passwords match
    if ($password !== $password_verify) {
        $_SESSION['error_message'] = 'Passwords do not match!';
        header('Location: sign-up.php');
        exit;
    }

    // Check if the username already exists
    $stmt = $db->prepare('SELECT password FROM users WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user) {
        $_SESSION['error_message'] = 'This username is already taken. Please enter another username and try again.';
        header('Location: sign-up.php');
        exit;
    } else {
        // Insert the new user into the database
        $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->execute();

        $_SESSION['success_message'] = 'Your account has been successfully created! To get started, enter your login credentials and sign in.';
        header('Location: auth.php');
        exit;
    }
}
?>