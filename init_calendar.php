<?php 
$db = new SQLite3('database.db');

// This file creates a new table in the database called "periods"
// It stores the following information:
    // day_number: the day of the week the period is on (one of [1, 2, 3, 4, 5], INTEGER)
    // week_number: the week the period is on (one of [1, 2], INTEGER)
    // period_number: the period number of the period (1-9, INTEGER)
    // title: the title of the period (user-submitted, TEXT)
    // location: the location of the period (user-submitted, TEXT)
    // professor: the professor of the period (user-submitted, TEXT)
    // class: the class of the period (user-submitted, TEXT)
    // user: the username of the user who created the period (TEXT)

$db->exec('
    CREATE TABLE IF NOT EXISTS periods (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        day_number INTEGER,
        week_number INTEGER,
        period_number INTEGER,
        title TEXT,
        location TEXT,
        professor TEXT,
        class TEXT,
        user TEXT
    )
');

error_log('[Server] Table "periods" created successfully');

?>