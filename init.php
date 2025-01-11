<?php

$db = new SQLite3('database.db');

$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
)");

$db->exec("INSERT OR IGNORE INTO users (username, password) VALUES ('user', 'password')");


// others:

// user: 'louistest', password: 'password'
// user: 'nlim', password: 'P*********##'
?>

