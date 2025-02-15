<?php
session_start();
error_reporting(0);
include("includes/config.php");

if (isset($_POST['submit'])) {
    $email = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to fetch user details
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

            // Combine names into a single display name
            $_SESSION['displayName'] = $user['firstname'] 
                . (empty($user['middlename']) ? '' : ' ' . $user['middlename']) 
                . ' ' . $user['lastname'];

            // Log user login
            $uip = $_SERVER['REMOTE_ADDR'];
            $status = 1;  // Successful login
            $log_stmt = $conn->prepare("INSERT INTO userlog (uid, username, userip, status) VALUES (?, ?, INET_ATON(?), ?)");
            $log_stmt->bind_param("issi", $_SESSION['id'], $_SESSION['login'], $uip, $status);
            $log_stmt->execute();

            session_regenerate_id(true); // Regenerate session ID for security
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
  <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&amp;display=swap" rel="stylesheet">
  <!-- Vendor CSS -->
  <link href="asset/css/vendor.min.css" rel="stylesheet" type="text/css" />
  <link href="asset/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- Custom Style -->
  <link href="asset/css/style.min.css" rel="stylesheet" type="text/css" />
  <script src="asset/js/config.js"></script>
  <style>
    body.authentication-bg {
      background: royalblue;
    }
    .account-pages {
      padding-top: 3rem;
      padding-bottom: 3rem;
    }
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .card-body {
      background: white;
      border-radius: 10px;
      padding: 2rem;
    }
    .auth-logo img {
      max-width: 150px;
      margin-bottom: 1rem;
    }
    .fw-bold {
      font-weight: 700 !important;
    }
    .text-dark {
      color: #2f3039 !important;
    }
    .text-muted {
      color: #444b53 !important;
    }
    .form-label {
      margin-bottom: 0.4rem;
      font-size: 0.875rem;
      font-weight: 500;
      color: #000;
    }
    .form-control {
      background-color: white;
      color: black;
    }
    .form-control:focus {
      background-color: white;
      color: black;
      border-color: #ced4da;
      outline: 0;
      box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    .btn-dark {
      background: #000;
      color: #fff;
      padding: 12px;
      font-size: 18px;
      border: none;
      border-radius: 8px;
      width: 100%;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.3s ease;
    }
    .btn-dark:hover {
      background: #333;
      transform: translateY(-2px);
    }
    .text-center a {
      color: #fff;
      text-decoration: none;
      font-weight: 700;
    }
  </style>
</head>
<body class="authentication-bg">
  <div class="account-pages py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="card shadow-lg">
            <div class="card-body p-5">
              <div class="text-center">
                <div class="mx-auto mb-4 auth-logo">
                  <a href="dashboard.php" class="logo-light">
                    <img src="asset/images/logodark.png" class="logo-lg" alt="logo dark">
                  </a>
                </div>
                <h4 class="fw-bold text-dark mb-2">Sign In</h4>
                <p class="text-muted">Enter your email and password to sign in.</p>
              </div>
              <form method="post" class="mt-4">
                <?php if($errormsg): ?>
                  <div class="alert alert-danger">
                    <?= htmlentities($errormsg); ?>
                  </div>
                <?php endif; ?>
                <div class="mb-3">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" name="username" id="email" placeholder="Enter your email" required autofocus>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn-dark" name="submit"><i class="fa fa-lock"></i> Sign In</button>
                </div>
                <div class="text-center mt-3">
                  <a href="forgot_password.php" id="forgotPasswordLink">Forgot Password?</a>
                </div>
              </form>
            </div>
          </div>
          <p class="text-center mt-4 text-white text-opacity-50">
            Don't have an account? <a href="registration.php" class="fw-bold">Create one</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <script src="asset/js/vendor.min.js"></script>
  <script src="asset/js/app.js"></script>
</body>
</html>
