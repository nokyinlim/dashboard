<?php

session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: auth.php');
    exit;
}

include 'config.php';
include 'database.php';
include 'calendar.php';
include '../src/utils.php';

$calendar = new Calendar();

$current_period = $calendar->getCurrentPeriod(8);
$next_period = $calendar->getNextPeriod(8);
$today_schedule = $calendar->getDaySchedule();

if (isset($_COOKIE['theme'])) { // default
    $_COOKIE['theme'] = 'dark';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $db->savePeriod(
        $_POST['day'],
        $_POST['week'],
        $_POST['period'],
        $_POST['title'],
        $_POST['location'],
        $_POST['professor'],
        $_POST['class']
    );
    header('Location: calendar/index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
    transition: background-color 0.3s, color 0.3s;
    background-color: #ffffff; /* Light mode background */
    color: #333333; /* Light mode text color */
}

body.dark-mode {
    background-color: #121212; /* Dark mode background */
    color: #e0e0e0; /* Dark mode text color */
}

table {
    width: 100%;
    margin: 20px 0;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
    transition: background-color 0.3s;
}

th {
    background-color: #f0f0f0;
}

body.dark-mode th {
    background-color: #1e1e1e; /* Dark mode header */
}

tr:nth-child(even) {
    background-color: #fafafa;
}

body.dark-mode tr:nth-child(even) {
    background-color: #292929; /* Dark mode even rows */
}

.period-form {
    margin: 20px 0;
    padding: 20px;
    background-color: #f5f5f5; /* Light mode form background */
    border-radius: 8px;
    transition: background-color 0.3s;
}

body.dark-mode .period-form {
    background-color: #1f1f1f; /* Dark mode form background */
}

.period-form input,
.week-selector select {
    margin: 5px;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: calc(100% - 26px); /* Full width minus padding */
    transition: border-color 0.3s, background-color 0.3s;
}

body.dark-mode .period-form input,
body.dark-mode .week-selector select {
    border: 1px solid #444; /* Dark mode input border */
    background: #333; /* Dark mode input background */
    color: #e0e0e0; /* Dark mode input text */
}

.toggle-button {
    margin: 20px 0;
    padding: 10px 15px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #007bff; /* Light mode button */
    color: white;
    transition: background-color 0.3s;
}

.toggle-button:hover {
    background-color: #0056b3; /* Light mode button hover */
}

body.dark-mode .toggle-button {
    background-color: #6200ea; /* Dark mode button */
}

body.dark-mode .toggle-button:hover {
    background-color: #3700b3; /* Dark mode button hover */
}

.week-selector {
    margin: 20px 0;
}

.current-info {
    margin: 20px 0;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f5f5f5; /* Light mode current info */
}

body.dark-mode .current-info {
    border-color: #444; /* Dark mode border */
    background-color: #1f1f1f; /* Dark mode current info background */
}

/* styles.css */
.schedule-table {
    border-collapse: collapse;
    width: 100%;
    background-color: #ffffff;
    border: 1px solid #ddd;
}

.schedule-table th,
.schedule-table td {
    padding: 8px;
    border: 1px solid #ddd;
}

.schedule-table th {
    background-color: #f5f5f5;
    color: #000000;
}

.schedule-table td {
    background-color: #ffffff;
    color: #111;
}

.schedule-table .period-info {
    color: #333;
}

.schedule-table .no-class {
    color: #666;
}

.schedule-table .current-period {
    background-color: #b4ff3b;
}

.schedule-table .current-day {
    background-color: #d6ecf3;

}

.schedule-table .next-period {
    background-color: #ffeb3b;

}

.schedule-table .current-day:hover, .current-period:hover, .next-period:hover {
    scale: 1;
}

/* Dark mode styles */
.dark-mode .schedule-table {
    background-color: #333;
    border-color: #555;
}

.dark-mode .schedule-table th,
.dark-mode .schedule-table td {
    border-color: #555;
    color: #ffffff;
}

.dark-mode .schedule-table th {
    background-color: #444;
}

.dark-mode .schedule-table td {
    background-color: #333;
}

.dark-mode .schedule-table .period-info {
    color: #ffffff;
}

.dark-mode .schedule-table .no-class {
    color: #999;
}

.dark-mode .schedule-table .current-period {
    background-color: #0c4d05;
}

.dark-mode .schedule-table .current-day {
    background-color: #00001c;
}

.dark-mode .schedule-table .next-period {
    background-color: #4a4a00;
}



    </style>
    <script href="../script.js"></script>
</head>
<body>
    <button class="toggle-button" id="toggle-button">Switch to Dark Mode</button>

    <div class="week-selector">
        <h2>Week <?php echo ((date('W') % 2) + 1); ?></h2>
        <form method="GET">
            <select name="week" onchange="this.form.submit()">
                <option value="1" <?php echo ($_GET['week'] ?? '') == '1' ? 'selected' : ''; ?>>Week 1</option>
                <option value="2" <?php echo ($_GET['week'] ?? '') == '2' ? 'selected' : ''; ?>>Week 2</option>
            </select>
        </form>
    </div>

    <?php
    // Display table

    echo $calendar->generateTableView($_GET['week'] ?? null);

    ?>

    <div class="period-form">
        <h3>Customize Period</h3>
        <form method="POST">
            <input type="number" name="day" min="1" max="7" placeholder="Day (1-7)" required>
            <input type="number" name="week" min="1" max="2" placeholder="Week (1-2)" required>
            <input type="number" name="period" min="1" max="9" placeholder="Period (1-9)" required>
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="class" placeholder="Class">
            <input type="text" name="location" placeholder="Location" required>
            <input type="text" name="professor" placeholder="Professor" required>
            
            <button type="submit">Save Period</button>
        </form>
    </div>

    <div class="period-form">
        <h3>Bulk-Add Periods</h3>
        <h2>Paste from ISAMS: Go to 'Print My Timetable', and press ⌘+A, then ⌘+C and paste text in the below text area.</h2>
        <p>Note: this is horribly done so expect like a 30% success rate</p>
        <form method="POST" action="/calendar/harrow-isams-parser.php">
            <textarea name="text" cols="150" rows="5" placeholder="Paste your full ISAMs calendar here" required></textarea>
            <br><button type="submit">Confirm Save Periods</button>
        </form>
    </div>

    <div class="current-info">
        <?php
        $current_period = $calendar->getCurrentPeriod();
        if ($current_period) {
            echo "<h3>Current Period: P{$current_period['period']}</h3>";
            if ($current_period['info']) {
                echo "<p>Title: {$current_period['info']['title']}<br>";
                echo "Location: {$current_period['info']['location']}<br>";
                echo "Professor: {$current_period['info']['professor']}</p>";
            }
        }

        $next_period = $calendar->getNextPeriod();
        if ($next_period) {
            echo "<h3>Next Period: P{$next_period['period']}</h3>";
            if ($next_period['info']) {
                echo "<p>Title: {$next_period['info']['title']}<br>";
                echo "Location: {$next_period['info']['location']}<br>";
                echo "Professor: {$next_period['info']['professor']}</p>";
            }
        }
        ?>
    </div>

    <script>
        const toggleButton = document.getElementById('toggle-button');
        toggleButton.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            toggleButton.textContent = document.body.classList.contains('dark-mode') ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        });

        // Set initial mode based on cookie
        if (getCookie('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            toggleButton.textContent = 'Switch to Light Mode';
        }

        
        toggleButton.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            setCookie('theme', isDarkMode ? 'dark' : 'light', 7); // Save preference for 7 days
            toggleButton.textContent = isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        });


    </script>
</body>
</html>