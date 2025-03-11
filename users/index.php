<?php
session_start();
error_reporting(0);
include("includes/config.php");

if (isset($_POST['submit'])) {
    $email = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, firstname, middlename, lastname, password FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['userId'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['middlename'] = $user['middlename'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['login'] = $email;
            $_SESSION['id'] = $user['id'];

            $_SESSION['displayName'] = $user['firstname'] 
                . (empty($user['middlename']) ? '' : ' ' . $user['middlename']) 
                . ' ' . $user['lastname'];

            $uip = $_SERVER['REMOTE_ADDR'];
            $status = 1; 
            $log_stmt = $conn->prepare("INSERT INTO userlog (uid, username, userip, status) VALUES (?, ?, INET_ATON(?), ?)");
            $log_stmt->bind_param("issi", $_SESSION['id'], $_SESSION['login'], $uip, $status);
            $log_stmt->execute();

            session_regenerate_id(true);
            header("Location: dashboard.php");
            exit();
        } else {
            $errormsg = "Invalid username or password.";
        }
    } else {
        $errormsg = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Sign In - INGAT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="robots" content="index, follow" />
  <meta name="theme-color" content="#ffffff">
  <link rel="shortcut icon" href="asset/images/ingat.ico">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
  <!-- Vendor CSS -->
  <link href="asset/css/vendor.min.css" rel="stylesheet" type="text/css" />
  <link href="asset/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- Custom Style -->
  <link href="asset/css/style.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <script src="asset/js/config.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Play', sans-serif;
      background: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      display: flex;
      width: 100%;
      max-width: 1200px;
      height: 100vh;
    }
    .left-section {
      flex: 1;
      background: linear-gradient(135deg, #4a90e2, #63b8ff);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: #fff;
      padding: 2rem;
      margin-left:-10%;
    }
    .left-section img {
      max-width: 150px;
      margin-bottom: 1rem;
    }
    .left-section p {
      text-align: center;
      font-size: 1.1rem;
      margin: 0;
    }
    .right-section {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }
    .card {
      width: 100%;
      max-width: 400px;
      border: none;
      border-radius: 10px;
      padding: 2rem;
      background-color: #fff;
    }
    .card-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #2f3039;
      margin-bottom: 0.5rem;
    }
    .card-subtitle {
      color: #444b53;
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
    }
    .form-label {
      font-size: 0.875rem;
      font-weight: 500;
      color: #000;
      margin-bottom: 0.4rem;
    }
    .form-control {
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
      padding: 0.5rem 1rem;
      font-size: 1rem;
      width: 100%;
      background-color: #fff;
      color: #000;
    }
    .form-control:focus {
      border-color: #4a90e2;
      box-shadow: 0 0 0 0.2rem rgba(74,144,226,0.25);
      outline: none;
    }
    .password-container {
      position: relative;
    }
    .password-container .fa {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }
    .btn-primary {
      background-color: #4a90e2;
      border: none;
      border-radius: 0.25rem;
      padding: 0.75rem;
      font-size: 1rem;
      color: #fff;
      width: 100%;
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #357abd;
    }
    .forgot-password {
      text-align: right;
      margin-top: 0.5rem;
    }
    .forgot-password a {
      color: #4a90e2;
      text-decoration: none;
      font-size: 0.875rem;
    }
    .signup-link {
      text-align: center;
      margin-top: 1rem;
      color: #6c757d;
    }
    .signup-link a {
      color: #4a90e2;
      text-decoration: none;
      font-weight: 700;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border-radius: 0.25rem;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="left-section">
      <img src="asset/images/logodark.png" alt="INGAT Logo">
      <p>INGAT<br>Inform - Navigate - Guard - Act - Together</p>
    </div>
    <div class="right-section">
      <div class="card">
        <h4 class="card-title">Sign In</h4>
        <p class="card-subtitle">Welcome back! Please sign in to continue.</p>
        <form method="post" class="mt-4">
          <?php if($errormsg): ?>
            <div class="alert alert-danger">
              <?= htmlentities($errormsg); ?>
            </div>
          <?php endif; ?>
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" style="background-color:white; color:rgb(0, 0, 0);" class="form-control" name="username" id="email" placeholder="Enter your email" required autofocus>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="password-container">
              <input type="password" style="background-color:white; color:rgb(0, 0, 0);" class="form-control" name="password" id="password" placeholder="Enter your password" required>
              <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </div>
          </div>
          <div class="forgot-password">
            <a href="forgot_password.php">Forgot password?</a>
          </div>
          <div class="d-grid mt-3">
            <button type="submit" class="btn btn-primary" name="submit">Sign In</button>
          </div>
          <div class="signup-link">
            Don't have an account? <a href="registration.php">Sign up</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="asset/js/vendor.min.js"></script>
  <script src="asset/js/app.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const passwordField = document.getElementById("password");
      const togglePassword = document.getElementById("togglePassword");

      togglePassword.addEventListener("click", function () {
        if (passwordField.type === "password") {
          passwordField.type = "text";
          togglePassword.classList.remove("fa-eye");
          togglePassword.classList.add("fa-eye-slash");
        } else {
          passwordField.type = "password";
          togglePassword.classList.remove("fa-eye-slash");
          togglePassword.classList.add("fa-eye");
        }
      });
    });
  </script>
</body>
</html>