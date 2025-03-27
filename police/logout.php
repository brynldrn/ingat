<?php
session_start();
include("includes/config.php");

// Record logout time
date_default_timezone_set('Asia/Kolkata');
$ldate = date('F j, Y h:i:s A'); // Use a timestamp for better consistency

// Update the logout and status to 0 in the userlog table for the current session
$log_stmt = $conn->prepare("UPDATE userlog SET logout = ?, status = 0 WHERE username = ? ORDER BY id DESC LIMIT 1");
$log_stmt->bind_param("ss", $ldate, $_SESSION['login']);
$log_stmt->execute();

// Clear session data
session_unset(); // Unset session variables
session_destroy(); // Destroy session

// Redirect to the login page
header("Location: index.php");
exit();
?>