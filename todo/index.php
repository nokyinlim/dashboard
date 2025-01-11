<?php
require '../src/Todo.php';


session_start();


if (!isset($_SESSION['todo'])) {
    $_SESSION['todo'] = new Todo();
}

if (!isset($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = false;
}

if (!$_SESSION['logged_in']) {
    header('Location: localhost:8000');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task'])) {
        $_SESSION['todo']->addTask($_POST['task']);
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit(); // Ensure no further code is executed after the redirect
    } else {
        $_SESSION['error_message'] = "Your To-Do item must have a title.";
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit();
    }
    if (isset($_POST['remove'])) {
        $_SESSION['todo']->removeTask($_POST['remove']);
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit();
    }
}

$tasks = $_SESSION['todo']->getTasks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css?v=1.0">
    <title>To-Do List</title>
    <script src="../navbar.js"></script>
</head>
<body>
    <div class="navbar" onmouseover="resetTimer()" onmouseleave="startTimer()">
        <div class="nav-left" id="nav-left">
            <?php if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]): ?>
                <a href="/">About Us</a>
                <a href="#" onclick="fetchLinks('features'); return false;">Features <img class="navbar-link-from-left" src="images/chevron-right-2.png" height="10" width="10"></a>
                <a href="/">Contact Us</a>
                <a href="/">Help / Support</a>
            <?php else: ?>
                <a href="/todo">To Do List</a>
                <a href="/reminders">Reminders</a>
                <a href="/homework">Homework Tracking</a>
            <?php endif; ?>
            
        </div>
        <div class="nav-right" id="nav-right">
            

            <?php if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]): ?>
                <a href="/">Don't have an account? Sign Up</a>
            <?php else: ?>
                <a href="#settings">Settings</a>
                <a href="/logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="horizontal-container">
        <div class="left-side">
            <div class="content-body">
                <form method="POST" class="task-form">
                    <h2>To-Do List</h2>
                    <?php if (isset($_SESSION["error_message"])):
                        echo '<p style="color: red;">' . htmlspecialchars($_SESSION["error_message"]) . '</p>';
                        unset($_SESSION["error_message"]); // Clear the message after displaying it

                    endif; ?>

                    <input type="text" name="task" placeholder="Task Title" class="login-input" required>
                    <input type="submit" name="submit" value="Add Item">
                </form>
            </div>
        </div>

        <div class="separator"></div>

        <div class="right-side">
            <div class="content-body">
                <div class="container-body">
                    <div class="container">
                        <?php if (empty($tasks)): ?>
                            <div class="box">
                                <div class="box-content">
                                    <h1>This is your To-Do List!</h1>
                                </div>
                            </div>
                            <div class="box">
                                <div class="box-content">
                                    <h1>Try adding an item here!</h1>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($tasks as $index => $task): ?>
                                <div class="box" onclick="removeTask(<?php echo $index; ?>)">
                                    <div class="box-content">
                                        <?php echo htmlspecialchars($task); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Hidden Form for Removal -->
    <form id="remove-form" method="POST" style="display: none;">
        <input type="hidden" name="remove" id="remove-input">
    </form>

    <script>
    function removeTask(index) {
        document.getElementById('remove-input').value = index;
        document.getElementById('remove-form').submit();
    }
    document.body.onmousemove = e => {
        for(const box of document.getElementsByClassName("box")) {
            const rect = box.getBoundingClientRect(),
                x = e.clientX - rect.left,
                y = e.clientY - rect.top;

            box.style.setProperty("--mouse-x", `${x}px`);
            box.style.setProperty("--mouse-y", `${y}px`);
        }
    };

    </script>
    
</body>
</html>