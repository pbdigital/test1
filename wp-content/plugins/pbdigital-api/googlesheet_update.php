<?php

// Get the current day of the week (1 for Monday through 7 for Sunday)
$currentDay = date('N');

// Get the current time in 24-hour format
$currentTime = date('H');

// Get the current minute
$currentMinute = date('i');

$run = false;


// Check if it's Saturday or Sunday, if the current time is between 10:00 AM and 01:59 PM, and if the current minute is divisible by 15
if ((($currentDay == 6 || $currentDay == 7) && ($currentTime >= 10 && $currentTime <= 14) && ($currentMinute % 15 == 0))) {
    $run = true;
}

// Check if it's Saturday or Sunday, if the current time is between 07:00 AM and 10:59 AM, and if the current minute is divisible by 5
if ((($currentDay == 6 || $currentDay == 7) && ($currentTime >= 7 && $currentTime <= 10) && ($currentMinute % 5 == 0))) {
    $run = true;
}

if($run){
    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, 'https://my.journey2jannah.com/wp-admin/admin-ajax.php?action=generate_attendance_google_sheet'); // Replace with your URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return the transfer as a string

    // Execute cURL session and get the HTML content
    $htmlContent = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    // Output the HTML content
    echo $htmlContent;
}
?>