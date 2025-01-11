<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: auth.php');
    exit;
}

// include 'calendar/calendar.php';
// include 'calendar/database.php';
// include 'calendar/config.php';

// $calendar = new Calendar();

// $current_period = $calendar->getCurrentPeriod(8);
// $next_period = $calendar->getNextPeriod(8);
// $today_schedule = $calendar->getDaySchedule();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION["logged_in"] ? "Your Dashboard" : "Log In - Your Dashboard"; ?></title>
    <link rel="stylesheet" href="style.css">
    <script href="script.js"></script>

    <script>
        function fetchLinks(linkType) {
            fetch(`navbar.php?type=${linkType}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('nav-left').innerHTML = data;
                })
                .catch(error => console.error('Error fetching links:', error));
        }

        function slideOut() {
            const elements = document.querySelectorAll('.navbar-link-from-left');
            elements.forEach(element => {
                element.classList.add('slide-out-to-right'); // Add the slide-out class to each element
                console.log("Element Counter")
            });
            setTimeout(fetchLinks, 100, "")
        }

        let timer;
        function startTimer() {
            // Check if the timer is already running
            if (timer) {
                return; // Do not start a new timer if one is running
            }

            // Start a 5-second countdown
            let timeLeft = 5;
            timer = setInterval(() => {
                console.log(`Time left: ${timeLeft} seconds`);
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(timer); 
                    timer = null; 
                    timerComplete(); 
                }
            }, 1000);
        }

        function resetTimer() {
            
            if (timer) {
                clearInterval(timer); 
                timer = null; 
            }
            startTimer(); 
        }

        function timerComplete() {
            slideOut()
        }
    </script>
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
                <a href="/todo/index.php">To Do List</a>
                <a href="/calendar/index.php">Calendar</a>
                <a href="/homework/index.php">Homework Tracking</a>
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
    <div class="content-body">

    <p style="color: white;">Homepage</p>
    <!-- <div class="current-info"> -->
        <?php
        // $current_period = $calendar->getCurrentPeriod(8);
        // if ($current_period) {
        //     echo "<h3>Current Period: P{$current_period['period']}</h3>";
        //     if ($current_period['info']) {
        //         echo "<p>Title: {$current_period['info']['title']}<br>";
        //         echo "Location: {$current_period['info']['location']}<br>";
        //         echo "Professor: {$current_period['info']['professor']}</p>";
        //     }
        // }

        // $next_period = $calendar->getNextPeriod(8);
        // if ($next_period) {
        //     echo "<h3>Next Period: P{$next_period['period']}</h3>";
        //     if ($next_period['info']) {
        //         echo "<p>Title: {$next_period['info']['title']}<br>";
        //         echo "Location: {$next_period['info']['location']}<br>";
        //         echo "Professor: {$next_period['info']['professor']}</p>";
        //     }
        // }
        // ?>
    <!-- </div> -->

    </div>

    <script>
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