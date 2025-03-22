<?php
require_once 'includes/config.php';

session_start();

if (isset($_GET['code'])) {
    // Exchange code for token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        die("Error fetching token: " . $token['error']);
    }
    $client->setAccessToken($token['access_token']);

    // Get user info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    // Map Google data to your users table
    $userinfo = [
        'user_email' => mysqli_real_escape_string($conn, $google_account_info->email),
        'firstname' => mysqli_real_escape_string($conn, $google_account_info->givenName),
        'lastname' => mysqli_real_escape_string($conn, $google_account_info->familyName),
        'username' => mysqli_real_escape_string($conn, $google_account_info->email), // Use email as username
        'user_image' => mysqli_real_escape_string($conn, $google_account_info->picture),
        'status' => 1,
        'is_verified' => $google_account_info->verifiedEmail ? 1 : 0,
        'reg_date' => date('Y-m-d H:i:s'),
        'updation_date' => date('Y-m-d H:i:s'),
    ];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE user_email = '{$userinfo['user_email']}'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['login'] = $user['user_email'];
        $_SESSION['userId'] = $user['id'];
        $_SESSION['displayName'] = trim($user['firstname'] . ' ' . $user['lastname']);
    } else {
        // Insert new user
        $sql = "INSERT INTO users (user_email, firstname, lastname, username, user_image, status, is_verified, reg_date, updation_date) 
                VALUES ('{$userinfo['user_email']}', '{$userinfo['firstname']}', '{$userinfo['lastname']}', '{$userinfo['username']}', '{$userinfo['user_image']}', '{$userinfo['status']}', '{$userinfo['is_verified']}', '{$userinfo['reg_date']}', '{$userinfo['updation_date']}')";
        if (mysqli_query($conn, $sql)) {
            $userId = mysqli_insert_id($conn);
            $_SESSION['login'] = $userinfo['user_email'];
            $_SESSION['userId'] = $userId;
            $_SESSION['displayName'] = trim($userinfo['firstname'] . ' ' . $userinfo['lastname']);
        } else {
            die("Error: " . mysqli_error($conn));
        }
    }

    header("Location: dashboard.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>