<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /'); // Redirect to the root page
    exit();
}
require 'db_init.php';

$user_id = $_SESSION['username'];
$stmt = $db->prepare('SELECT * FROM assignments WHERE user_id = ?');
$stmt->bindValue(1, $user_id);
$result = $stmt->execute();

?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homework_styles.css">
    <title>Homework Tracker</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
    <a href="/homework/add_assignment.php">Add Assignment</a>
    <a href="/homework/view_assignments.php">View Assignments</a>
</body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homework Tracker</title>
    <link rel="stylesheet" href="mainpage_styles.css">
</head>
<body>

    <h1>Homework Tracker</h1>
    <table class="task-table">
        <thead>
            <tr>
                <th></th>
                <th>Title</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Links</th>
                <th>Subject</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 0; ?>
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
            <tr class="task-row" data-complete="false">
                <td><input type="checkbox"></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                    <?php if ($row['due_date']): ?>
                        <p id="due-date-text" name="due-date-text"><?php echo $row['due_date']; ?></p>
                    <?php else: ?>
                        No Due Date
                    <?php endif; ?>
                    
                </td>
                <td>
                    <?php if ($row['subject']): ?>
                        <?php echo $row['subject'];?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['links']): ?>
                        <?php foreach (explode(',', $row['links']) as $link): ?>
                            <a href="<?php echo htmlspecialchars(trim($link)); ?>" target="_blank"><?php echo htmlspecialchars(trim($link)); ?></a><br>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
                <td><a href="edit_assignment.php?id=<?php echo $row['id']; ?>">Edit</a></td>
            </tr>
            <?php $count++; ?>
            <?php endwhile; ?>
            <!-- <tr class="task-row" data-complete="false">
                <td><input type="checkbox"></td>
                <td>Math Homework</td>
                <td>Complete exercises 1 to 10</td>
                <td>Due Tomorrow</td>
                <td><a href="#">Link 1</a><br><a href="#">Link 2</a></td>
                <td>Mathematics</td>
                <td class="task-actions">
                    <button onclick="toggleDescription(this)">View Info</button>
                    <button>Edit</button>
                    <button>Delete</button>
                </td>
            </tr>
            <tr class="full-description hidden">
                <td colspan="7">Complete the exercises from the textbook, focusing on problem-solving strategies.</td>
            </tr>
            <tr class="task-row" data-complete="true">
                <td><input type="checkbox" checked></td>
                <td>English Essay</td>
                <td>Write an essay on Shakespeare</td>
                <td>Due Next Week</td>
                <td><a href="#">Link 1</a></td>
                <td>English</td>
                <td class="task-actions">
                    <button onclick="toggleDescription(this)">Expand</button>
                    <button>Edit</button>
                    <button>Delete</button>
                </td>
            </tr>
            <tr class="full-description hidden">
                <td colspan="7">The essay should analyze themes and character development in Shakespeare's works.</td>
            </tr> -->
        </tbody>
    </table>

    <script>
        function toggleDescription(button) {
            const row = button.closest('.task-row');
            const descriptionRow = row.nextElementSibling;

            

            row.classList.toggle('expanded');
            descriptionRow.classList.toggle('hidden');

            if (row.classList.contains('expanded')) {
                descriptionRow.style.display = 'table-row';
            } else {
                descriptionRow.style.display = 'none';
            }
        }

        // Example input: "2024-12-30"
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
                return "Due In " + (weeks === 1 ? "one week" : `${weeks} weeks`);
            } else if (daysDiff >= 30 && daysDiff <= 364) {
                const months = Math.floor(daysDiff / 30);
                return "Due In " + (months === 1 ? "one month" : `${months} months`);
            } else if (daysDiff >= 365 && daysDiff <= 3650) {
                return `Due In ${Math.floor(daysDiff / 365)}`;
            } else if (daysDiff >= 3651) {
                return "Due in a very long time!";
            } else if (daysDiff === -3) {
                return "Due the day before Yesterday";
            } else if (daysDiff >= -7) {
                return "Due last week";
            } else if (daysDiff >= -30) {
                return "Due around last month";
            } else if (daysDiff >= -365) {
                return "Due last year";
            } else {
                return "Due a very long time ago!";
            }


            return targetDate.toISOString().split('T')[0]; // Fallback to the date string
        }

        var elements = document.getElementsByName("due-date-text");

        Array.from(elements).map((e) => {
            const text = e.innerText;
            e.innerHTML = text + ":<br>" + formatFriendlyDate(text);
        });

        function getDateFromDaysDiff(daysDiff) {
            const today = new Date(); // Current date
            const targetDate = new Date(today); // Create a new date object based on today

            // Add the days difference to the current date
            targetDate.setDate(today.getDate() + daysDiff);

            // Get the year, month, and day
            const year = targetDate.getFullYear();
            const month = String(targetDate.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            const day = String(targetDate.getDate()).padStart(2, '0');

            // Return the formatted date string
            return `${year}-${month}-${day}`;
        }
    </script>
</body>
</html>
