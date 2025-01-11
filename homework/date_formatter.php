<?php


if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["formatted_homework_date_message"])) {
    $_SESSION["formatted_homework_date_message"] = '';
}

// Example Input: '2024-12-20'
function formatFriendlyDate($inputDate) {
    $today = new DateTime(); // Current date
    $targetDate = new DateTime($inputDate); // Date from input
    $interval = $today->diff($targetDate); // Difference between today and target date

    $daysDiff = $interval->days;
    $isTomorrow = $interval->invert === 0 && $daysDiff === 1;
    $isYesterday = $interval->invert === 1 && $daysDiff === 1;

    if ($isTomorrow) {
        return "Tomorrow";
    } elseif ($isYesterday) {
        return "Yesterday";
    } elseif ($daysDiff === 0) {
        return "Today";
    } elseif ($daysDiff === 2) {
        return "The day after tomorrow";
    } elseif ($daysDiff > 2 && $daysDiff < 8) {
        return "This " . $targetDate->format('l'); // Day of the week
    } elseif ($daysDiff >= 8 && $daysDiff < 14) {
        return "Next " . $targetDate->format('l'); // Next day of the week
    } elseif ($daysDiff >= 14 && $daysDiff < 30) {
        $weeks = floor($daysDiff / 7);
        return "In " . ($weeks === 1 ? "one week" : "$weeks weeks");
    } elseif ($daysDiff >= 30) {
        $months = floor($daysDiff / 30);
        return "In " . ($months === 1 ? "one month" : "$months months");
    }

    return $targetDate->format('Y-m-d'); // Fallback to the date string if no conditions match
}
?>