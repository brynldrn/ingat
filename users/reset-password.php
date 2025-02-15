<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token'");
    $num = mysqli_fetch_array($sql);

    if ($num) {
        if (isset($_POST['submit'])) {
            // Server-side validation (optional additional check)
            $newPass = $_POST['newpassword'];
            $confirmPass = $_POST['confirmpassword'];

            // Regular expression: Minimum 8 characters, at least one uppercase, one lowercase, and one number.
            $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';

            if (!preg_match($pattern, $newPass)) {
                $errormsg = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.";
            } elseif ($newPass !== $confirmPass) {
                $errormsg = "Passwords do not match.";
            } else {
                $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
                $con = mysqli_query($conn, "UPDATE users SET password='$hashedPassword', reset_token=NULL, token_expiry=NULL WHERE reset_token='$token'");
                if ($con) {
                    $successmsg = "Password Reset Successfully !!";
                } else {
                    $errormsg = "Failed to reset password. Please try again.";
                }
            }
        }
    } else {
        $errormsg = "Invalid token.";
    }
} else {
    header('location:index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="asset/images/ingat.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&amp;display=swap" rel="stylesheet">
    <link href="asset/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="asset/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="asset/css/style.min.css" rel="stylesheet" type="text/css" />
    <script src="asset/js/config.js"></script>
    <style>
        body.authentication-bg {
            background: royalblue;
        }
        .card-body {
            background: white;
            border-radius: 10px;
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
                                        <img src="asset/images/logodark.png" class="logo-lg" alt="logo dark" style="width: 150px; height: auto;">
                                    </a>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Reset Password</h4>
                                <p class="text-muted">Enter your new password below to reset your password.</p>
                            </div>
                            <form method="post" class="mt-4" id="resetForm">
                                <?php if(isset($successmsg)) { ?>
                                <div class="alert alert-success alert-dismissable">
                                    <b>Well done!</b> <?php echo htmlentities($successmsg); ?>
                                </div>
                                <?php } ?>

                                <?php if(isset($errormsg)) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <b>Oh snap!</b> <?php echo htmlentities($errormsg); ?>
                                </div>
                                <?php } ?>

                                <div class="mb-3">
                                    <label for="newpassword" class="form-label">New Password</label>
                                    <!-- Added pattern attribute for HTML5 validation -->
                                    <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="Enter new password" 
                                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                                           title="Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number." 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="confirmpassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Confirm new password" required>
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-dark btn-lg fw-medium" type="submit" name="submit">Reset Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <p class="text-center mt-4 text-white text-opacity-50">
                        <a href="dashboard.php" class="text-decoration-none text-white fw-bold">Back to Home</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side Validation Script -->
    <script src="asset/js/vendor.min.js"></script>
    <script src="asset/js/app.js"></script>
    <script>
        document.getElementById('resetForm').addEventListener('submit', function (event) {
            var newPassword = document.getElementById('newpassword').value;
            var confirmPassword = document.getElementById('confirmpassword').value;
            // Regex: Minimum 8 characters, at least one uppercase, one lowercase, and one number.
            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

            if (!passwordPattern.test(newPassword)) {
                alert("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.");
                event.preventDefault();
                return false;
            }
            if (newPassword !== confirmPassword) {
                alert("Passwords do not match.");
                event.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
