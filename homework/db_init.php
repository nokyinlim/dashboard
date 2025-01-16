<?php
// db_init.php
$db = new SQLite3('../database.db');

// Create assignments table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS assignments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id TEXT NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT,
    links TEXT
)");
?>