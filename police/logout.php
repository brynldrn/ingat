<?php
session_start();
include("includes/config.php");

// Record logout time
date_default_timezone_set('Asia/Manila');
$ldate = date('F j, Y h:i:s A'); 
$log_stmt = $conn->prepare("UPDATE userlog SET logout = ?, status = 0 WHERE username = ? ORDER BY id DESC LIMIT 1");
$log_stmt->bind_param("ss", $ldate, $_SESSION['login']);
$log_stmt->execute();


session_unset(); 
session_destroy(); 

header("Location: index.php");
exit();
?>