<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /');
    exit();
}
require 'db_init.php';

$user_id = $_SESSION['username'];
$stmt = $db->prepare('SELECT * FROM assignments WHERE user_id = ?');
$stmt->bindValue(1, $user_id);
$result = $stmt->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homework_styles.css">
    <title>Your Assignments</title>
</head>
<body>
    <h1>Your Assignments</h1>
    <table>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Due Date</th>
            <th>Links</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td>
                <?php 
                if ($row['due_date']) {
                    echo "<script>document.write(formatFriendlyDate('" . $row['due_date'] . "'));</script>";
                } else {
                    echo "No due date";
                }
                ?>
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
        <?php endwhile; ?>
    </table>
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
        }
    </script>
</body>
</html>