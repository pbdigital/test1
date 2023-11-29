<?php
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
?>