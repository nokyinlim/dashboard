<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /');
    exit();
}
require 'db_init.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start session if not already started
    session_start();

    $user_id = $_SESSION['username'] ?? null; // Ensure user is authenticated
    if (!$user_id) {
        // Handle unauthenticated access
        header('Location: login.php');
        exit();
    }

    // Sanitize inputs using htmlspecialchars
    $title = htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8');
    $due_date = $_POST['due_date'] ?? null;
    $links = filter_input(INPUT_POST, 'links', FILTER_SANITIZE_URL) ?? '';
    $subject = htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8');

    // Validate due_date if it's provided
    if ($due_date && !DateTime::createFromFormat('Y-m-d', $due_date)) {
        // Handle invalid date format
        exit('Invalid due date format.');
    }

    // Prepare statement
    $stmt = $db->prepare('INSERT INTO assignments (user_id, title, description, due_date, links, subject) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bindValue(1, $user_id);
    $stmt->bindValue(2, $title);
    $stmt->bindValue(3, $description);
    $stmt->bindValue(4, $due_date);
    $stmt->bindValue(5, $links);
    $stmt->bindValue(6, $subject);

    // Execute and check for errors
    if ($stmt->execute()) {
        header('Location: calendar/index.php');
        exit();
    } else {
        // Handle execution error
        exit('Error saving assignment.');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homework_styles.css">
    <title>Add Assignment</title>
</head>
<body>
    <div class="homework-main">
        <form method="POST" action="/homework/index.php" class="task-form">
            <h2>Create An Assignment</h2>
            <?php if (isset($_SESSION["error_message"])): ?>
                <p style="color: red;"><?php echo htmlspecialchars($_SESSION["error_message"]); ?></p>
                <?php unset($_SESSION["error_message"]); ?>
            <?php endif; ?>

            <input type="text" placeholder="Assignment Title" name="title" class="login-input" required>
            
            <label for="homework-date-input" id="homework-date-input-label">Due Date:</label>
            <input id="homework-date-input" type="date" placeholder="" name="due_date" onchange="setDateInputLabel()">
            <div class="suggestion-body">
                <button class="suggestion" onclick="setDateInputToCustom(1, this)">Tomorrow</button>
                <button class="suggestion" onclick="setDateInputToCustom(2, this)">In 2 Days</button>
                <button class="suggestion" onclick="setDateInputToCustom(-1, this, 'Monday')">Next Monday</button>
            </div><div class="suggestion-body">
                <button class="suggestion" onclick="setDateInputToCustom(-1, this, 'Tuesday')">Next Tuesday</button>
                <button class="suggestion" onclick="setDateInputToCustom(-1, this, 'Wednesday')">Next Wednesday</button>
            </div><div class="suggestion-body">
                <button class="suggestion" onclick="setDateInputToCustom(-1, this, 'Thursday')">Next Thursday</button>
                <button class="suggestion" onclick="setDateInputToCustom(-1, this, 'Friday')">Next Friday</button>
            </div><div class="suggestion-body">
                <button class="suggestion" onclick="setDateInputToCustom(-1, this, 'Saturday')">Next Saturday</button>
                <button class="suggestion" onclick="setDateInputToCustom(-1, this, 'Sunday')">Next Sunday</button>
            </div>

            <input type="text" placeholder="Additional Notes and Details" name="description">
            <input type="url" placeholder="Links (separate with commas)" name="links">
            <input type="text" placeholder="Assignment Subject" name="subject">
            <input type="submit" name="submit" value="Create Item">
        </form>
    </div>

    <script>
        function formatFriendlyDate(inputDate) {
            const today = new Date(); // Current date
            const targetDate = new Date(inputDate); // Date from input
            const timeDiff = targetDate - today;
            const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

            const isTomorrow = daysDiff === 0;
            const isYesterday = daysDiff === -2;

            if (isTomorrow) {
                return "Due Tomorrow";
            } else if (isYesterday) {
                return "Due Yesterday";
            } else if (daysDiff === -1) {
                return "Due Today";
            } else if (daysDiff === 1) {
                return "Due the day after tomorrow";
            } else if (daysDiff > 1 && daysDiff < 7) {
                return "Due This " + targetDate.toLocaleString('default', { weekday: 'long' });
            } else if (daysDiff >= 7 && daysDiff < 13) {
                return "Due Next " + targetDate.toLocaleString('default', { weekday: 'long' });
            } else if (daysDiff >= 14 && daysDiff < 30) {
                const weeks = Math.floor(daysDiff / 7);
                return "Due In " + (weeks === 1 ? "One Week" : `${weeks} Weeks`);
            } else if (daysDiff >= 30 && daysDiff <= 364) {
                const months = Math.floor(daysDiff / 30);
                return "Due In " + (months === 1 ? "One Month" : `${months} Months`);
            } else if (daysDiff >= 365 && daysDiff <= 3650) {
                return `Due In ${Math.floor(daysDiff / 365)}`;
            } else if (daysDiff >= 3651) {
                return "Due in a very long time!";
            } else if (daysDiff === -3) {
                return "Due the day before Yesterday";
            } else if (daysDiff <= -7) {
                return "Due more than 1 week ago";
            } else if (daysDiff <= -30) {
                return "Due over a month ago";
            } else if (daysDiff <= -365) {
                return "Due over a year ago";
            } else {
                return "Due a very long time ago!";
            }
        }

        function getDateFromDaysDiff(daysDiff) {
            const today = new Date(); // Current date
            const targetDate = new Date(today); // Create a new date object based on today

            // Add the days difference to the current date
            targetDate.setDate(today.getDate() + daysDiff);

            // Get the year, month, and day
            const year = targetDate.getFullYear();
            const month = String(targetDate.getMonth() + 1).padStart(2, '0'); // Months are zero-based for some reason???
            const day = String(targetDate.getDate()).padStart(2, '0');

            // Return the formatted date string
            return `${year}-${month}-${day}`;
        }

        function daysUntilNextWeekdays() {
            const today = new Date(); // Current date
            const currentDay = today.getDay(); // 0 (Sunday) - 6 (Saturday)

            const daysUntil = {
                Sunday: (0 - currentDay + 7) % 7 || 7,
                Monday: (1 - currentDay + 7) % 7 || 7,
                Tuesday: (2 - currentDay + 7) % 7 || 7,
                Wednesday: (3 - currentDay + 7) % 7 || 7,
                Thursday: (4 - currentDay + 7) % 7 || 7,
                Friday: (5 - currentDay + 7) % 7 || 7,
                Saturday: (6 - currentDay + 7) % 7 || 7,
            };

            return daysUntil;
        }

        function daysUntilThisWeekdays() {
            const today = new Date(); // Current date
            const currentDay = today.getDay(); // 0 (Sunday) - 6 (Saturday)

            // If today is Sunday, return an empty object
            if (currentDay === 0) {
                return {};
            }

            const daysUntil = {};
            count = 1;
            for (let day = currentDay + 1; day <= 6; day++) {
                const dayName = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'][day];
                daysUntil[dayName] = count; // Each day remaining is just one day away
                count++;
            }

            return daysUntil;
        }

        function setDateInputLabel() {
            const label = document.getElementById("homework-date-input-label");
            const input = document.getElementById("homework-date-input");
            // console.log(input.value);
            const formattedDate = formatFriendlyDate(input.value);
            label.innerText = formattedDate;
        }

        function setDateInputToCustom(days, button, weekday = "") {
            
            if (weekday) {
                const daysUntil = daysUntilNextWeekdays();
                
                const newDate = getDateFromDaysDiff(daysUntil[weekday])
            
                console.log("Entered");
                if (newDate === undefined) {
                    console.warn(`Key "${keyToAccess}" does not exist! You may want to refresh this webpage.`);
                    return;
                }

                const label = document.getElementById("homework-date-input-label");
                const input = document.getElementById("homework-date-input");
                // console.log(input.value);
                const formattedDate = formatFriendlyDate(newDate);
                input.value = newDate;
                label.innerText = formattedDate;
            } else {
                newDate = getDateFromDaysDiff(days)
                const label = document.getElementById("homework-date-input-label");
                const input = document.getElementById("homework-date-input");
                // console.log(input.value);
                const formattedDate = formatFriendlyDate(newDate);
                input.value = newDate;
                label.innerText = formattedDate;
            }
        }
    </script>
</body>
</html>