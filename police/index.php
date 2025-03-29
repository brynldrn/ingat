<?php
session_start();
error_reporting(0);
include("includes/config.php");

if (isset($_POST['submit'])) {
    $badge_number = $_POST['badge_number'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, badge_number, firstname, middlename, lastname, password FROM police WHERE badge_number = ?");
    $stmt->bind_param("s", $badge_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $police = $result->fetch_assoc();

        if (password_verify($password, $police['password'])) {
            $_SESSION['police_id'] = $police['id'];
            $_SESSION['badge_number'] = $police['badge_number'];
            $_SESSION['firstname'] = $police['firstname'];
            $_SESSION['middlename'] = $police['middlename'];
            $_SESSION['lastname'] = $police['lastname'];
            $_SESSION['login'] = $badge_number;
            $_SESSION['id'] = $police['id'];

            $_SESSION['displayName'] = $police['firstname'] 
                . (empty($police['middlename']) ? '' : ' ' . $police['middlename']) 
                . ' ' . $police['lastname'];

            $uip = $_SERVER['REMOTE_ADDR'];
            $status = 1;
            $log_stmt = $conn->prepare("INSERT INTO userlog (uid, username, userip, status) VALUES (?, ?, INET_ATON(?), ?)");
            $log_stmt->bind_param("issi", $_SESSION['id'], $_SESSION['login'], $uip, $status);
            $log_stmt->execute();

            session_regenerate_id(true);
            header("Location: complaint-management.php");
            
            exit();
        } else {
            $errormsg = "Invalid badge number or password.";
        }
    } else {
        $errormsg = "Invalid badge number or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Police Sign In - INGAT</title>
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
      background: url('../img/pnp.jpeg') center/cover no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: #fff;
      padding: 2rem;
      margin-left: -10%;
      position: relative;
    }
    .left-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5); /* Blackish overlay */
      z-index: 1;
    }
    .left-section img {
      max-width: 150px;
      margin-bottom: 1rem;
      position: relative;
      z-index: 2; /* Above overlay */
    }
    .left-section p {
      text-align: center;
      font-size: 1.1rem;
      margin: 0;
      position: relative;
      z-index: 2; /* Above overlay */
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
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border-radius: 0.25rem;
    }
    .d-grid {
      display: grid;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
        height: auto;
        padding: 1rem;
      }
      .left-section {
        flex: none;
        width: 100%;
        padding: 1rem;
        margin-left: 0;
        min-height: 200px;
      }
      .left-section img {
        max-width: 120px;
      }
      .left-section p {
        font-size: 0.9rem;
      }
      .right-section {
        flex: none;
        width: 100%;
        padding: 1rem;
      }
      .card {
        max-width: 100%;
        padding: 1.5rem;
      }
      .card-title {
        font-size: 1.25rem;
      }
      .card-subtitle {
        font-size: 0.85rem;
      }
      .form-label {
        font-size: 0.85rem;
      }
      .form-control {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
      }
      .btn-primary {
        padding: 0.6rem;
        font-size: 0.9rem;
      }
      .alert-danger {
        font-size: 0.9rem;
        padding: 0.6rem;
      }
      .forgot-password a {
        font-size: 0.8rem;
      }
    }

    @media (min-width: 769px) and (max-width: 991px) {
      .login-container {
        max-width: 800px;
      }
      .left-section, .right-section {
        padding: 1.5rem;
      }
      .card {
        max-width: 350px;
      }
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
        <h4 class="card-title">Police Sign In</h4>
        <p class="card-subtitle">Welcome back, officer! Please sign in to continue.</p>
        <form method="post" class="mt-4">
          <?php if($errormsg): ?>
            <div class="alert alert-danger">
              <?= htmlentities($errormsg); ?>
            </div>
          <?php endif; ?>
          <div class="mb-3">
            <label for="badge_number" class="form-label">Badge Number</label>
            <input type="text" class="form-control" name="badge_number" id="badge_number" placeholder="Enter your badge number" required autofocus>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="password-container">
              <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
              <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </div>
          </div>
          <div class="d-grid mt-3">
            <button type="submit" class="btn btn-primary" name="submit">Sign In</button>
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