<?php 
$db = new SQLite3('database.db');

$db->exec('CREATE TABLE homework_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    title TEXT,
    due_date TIMESTAMPTZ,
    notes_and_details TEXT,
    links TEXT,
    homework_subject TEXT,
    FOREIGN KEY (user_id) REFERENCES users (id)
);');

?>