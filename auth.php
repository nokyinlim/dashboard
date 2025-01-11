<?php
session_start();

// Database connection
$db = new SQLite3('database.db');

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');

    // Fetch user from database
    $stmt = $db->prepare('SELECT password FROM users WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    // Verify password
    if ($user && $user['password'] === $password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Invalid username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Your Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <div class="nav-left" id="nav-left">
            <?php if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]): ?>
                <a href="/">About Us</a>
                <a href="#" onclick="fetchLinks('features'); return false;">Features <img class="navbar-link-from-left" src="images/chevron-right-2.png" height="10" width="10"></a>
                <a href="/">Contact Us</a>
                <a href="/">Help / Support</a>
            <?php else: ?>
                <a href="/todo">To Do List</a>
                <a href="/reminders">Reminders</a>
                <a href="#">Homework Tracking</a>
            <?php endif; ?>
            
        </div>
        <div class="nav-right" id="nav-right">
            

            <?php if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]): ?>
                <a href="/sign-up.php">Don't have an account? Sign Up</a>
            <?php else: ?>
                <a href="#settings">Settings</a>
                <a href="/logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="content-body">
        <form action="auth.php" method="POST" class="task-form">
            <h2>Log In To Your Account</h2>
            <?php if (isset($_SESSION["error_message"])): ?>
                <p style="color: red;"><?php echo htmlspecialchars($_SESSION["error_message"]); ?></p>
                <?php unset($_SESSION["error_message"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["success_message"])): ?>
                <p style="color: green;"><?php echo htmlspecialchars($_SESSION["success_message"]); ?></p>
                <?php unset($_SESSION["success_message"]); ?>
            <?php endif; ?>

            <input type="text" name="username" placeholder="Username" class="login-input" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <input type="submit" name="submit" value="Log In">
        </form>
    </div>


    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>