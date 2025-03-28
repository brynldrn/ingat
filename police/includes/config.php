<?php
$servername = "localhost"; 
$username = "root"; // Default username for localhost
$password = ""; // Default password for localhost (usually empty)
$dbname = "ingat_db";
$port = 3306; // Default MySQL port

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>