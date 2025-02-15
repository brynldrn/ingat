<?php
session_start();
error_reporting(0);
include("include/config.php");

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $query = "SELECT * FROM admin WHERE username='$username' and password='$password'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_array($result);

    if ($user) {
        $_SESSION['alogin'] = $username;
        $_SESSION['id'] = $user['id'];

        if (!empty($_POST['remember-me'])) {
            setcookie("username", $username, time() + (10 * 365 * 24 * 60 * 60)); // 10 years
            setcookie("password", $_POST['password'], time() + (10 * 365 * 24 * 60 * 60)); // 10 years
        } else {
            if (isset($_COOKIE["username"])) {
                setcookie("username", "");
            }
            if (isset($_COOKIE["password"])) {
                setcookie("password", "");
            }
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['errmsg'] = "Invalid username or password";
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="assets/images/ingat.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&amp;display=swap" rel="stylesheet">
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
    <style>
        body.authentication-bg {
            background: black;
        }
        .card-body {
            background: white;
        }
        .text-dark {
            color: #2f3039 !important;
        }
        .text-muted {
            --bs-text-opacity: 1;
            color: #444b53 !important;
        }
        .form-label {
            margin-bottom: .4rem;
            font-size: .875rem;
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
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .form-check-input {
            --bs-form-check-bg: #fff;
        }
        .form-check-label {
            color: black;
        }
        .text-muted {
            --bs-text-opacity: 1;
            color: #07f !important;
        }
        .btn-dark {
            --bs-btn-color: #fff;
            --bs-btn-bg: #000;
        }
    </style>
</head>

<body class="authentication-bg">
    <div class="account-pages py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <div class="text-center">
                                <div class="mx-auto mb-4 text-center auth-logo">
                                    <a href="dashboard.php" class="logo-light">
                                        <img src="assets/images/logodark.png" class="logo-lg" alt="logo dark" style="width: 150px; height: auto;">
                                    </a>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Welcome Back!</h4>
                                <p class="text-muted">Sign in to your account to continue</p>
                            </div>
                            <form method="post" class="mt-4">
                                <span style="color:red;"><?php echo htmlentities($_SESSION['errmsg']); ?><?php echo htmlentities($_SESSION['errmsg'] = ""); ?></span>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" value="<?php if(isset($_COOKIE["username"])) { echo $_COOKIE["username"]; } ?>">
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="password" class="form-label">Password</label>
                                        <a href="#" class="text-decoration-none small text-muted">Forgot password?</a>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" value="<?php if(isset($_COOKIE["password"])) { echo $_COOKIE["password"]; } ?>">
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="remember-me" name="remember-me" <?php if(isset($_COOKIE["username"])) { ?> checked <?php } ?>>
                                    <label class="form-check-label" for="remember-me">Remember me</label>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-dark btn-lg fw-medium" type="submit" name="submit">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>