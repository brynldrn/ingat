<?php
$mysql_hostname = "localhost";
$mysql_user = "root";
$mysql_password = "";
$mysql_database = "ingat_db";
$conn = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database) or die("Could not connect database");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>