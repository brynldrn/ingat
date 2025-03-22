<?php
require '/vendor/autoload.php';

// // Looing for .env at the root directory
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();

// // Retrive env variable
// $clientId = $_ENV['GOOGLE_CLIENT_ID'];
// $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];

// Google OAuth Configuration
$client = new Google_Client();
$client->setClientId('166138622522-tjv9l6rltv978ktsqc9tpm7e23k7se3h.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://ingat-web-php-7q7ei.ondigitalocean.app/users/callback.php'); 
$client->addScope('email');
$client->addScope('profile');

// AWS RDS MySQL Configuration
$servername = "ingat-db.c1qrsgyhssje.ap-southeast-1.rds.amazonaws.com";
$username = "admin";
$password = "password"; 
$dbname = "ingat_db";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>