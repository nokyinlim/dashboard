<?php
session_start();
include "date_formatter.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the Content-Type header to application/json
header('Content-Type: application/json');

// Read input JSON
$data = json_decode(file_get_contents("php://input"), true);
$date = isset($data['date']) ? trim($data['date']) : '';

// Check if the date is valid
if ($date) {
    // Format the date or do something with it
    $formattedDate = formatFriendlyDate($date);
    $_SESSION["datestuff"] = "$formattedDate";
    echo htmlspecialchars($formattedDate);

} else {
    echo ("No date selected.");
}
?>