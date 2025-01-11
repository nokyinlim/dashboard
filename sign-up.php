<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Your Dashboard</title>
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
                <a href="/auth.php">Already have an account? Log In</a>
            <?php else: ?>
                <a href="#settings">Settings</a>
                <a href="/logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="content-body">
        <form action="create-account.php" method="POST" class="task-form">
            <h2>Create Your Account</h2>
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
            <input type="password" name="password_verify" placeholder="Verify Password" required>
            <input type="submit" name="submit" value="Sign Up">
        </form>
    </div>


    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>