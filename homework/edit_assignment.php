<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /');
    exit();
}
require 'db_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? null;
    $links = $_POST['links'] ?? '';

    $stmt = $db->prepare('UPDATE assignments SET title = ?, description = ?, due_date = ?, links = ? WHERE id = ?');
    $stmt->bindValue(1, $title);
    $stmt->bindValue(2, $description);
    $stmt->bindValue(3, $due_date);
    $stmt->bindValue(4, $links);
    $stmt->bindValue(5, $id);
    $stmt->execute();

    header('Location: view_assignments.php');
    exit();
} else {
    $id = $_GET['id'];
    $stmt = $db->prepare('SELECT * FROM assignments WHERE id = ?');
    $stmt->bindValue(1, $id);
    $result = $stmt->execute();
    $assignment = $result->fetchArray(SQLITE3_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homework_styles.css">
    <title>Edit Assignment</title>
</head>
<body>
    <h1>Edit Assignment</h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $assignment['id']; ?>">
        <label for="title">Assignment Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($assignment['description']); ?></textarea>
        
        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" value="<?php echo $assignment['due_date']; ?>">
        
        <label for="links">Links (comma-separated):</label>
        <input type="text" id="links" name="links" value="<?php echo htmlspecialchars($assignment['links']); ?>">
        
        <button type="submit">Update Assignment</button>
    </form>
</body>
</html>