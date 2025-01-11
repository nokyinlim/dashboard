<?php 
$db = new SQLite3('database.db');

$db->exec('CREATE TABLE calendar_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    title TEXT,
    location TEXT,
    professor_name TEXT,
    period TEXT UNIQUE,  -- Ensure each period can only have one item
    date TEXT,
    FOREIGN KEY (user_id) REFERENCES users (id)
);');

?>