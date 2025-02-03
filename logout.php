<?php
// Maybe needs updating?

// This file logs the user out by destroying the session and redirecting to the login page

session_start();
session_destroy();
header('Location: auth.php');
exit;


?>