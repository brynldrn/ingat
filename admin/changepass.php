<?php
session_start();
include('include/config.php');

if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
    if(isset($_POST['submit'])) {
        $email = $_POST['email'];
        
        $query = mysqli_query($conn, "SELECT * FROM admin WHERE email='$email'");
        if(mysqli_num_rows($query) > 0) {
            // Generate a unique token
            $token = bin2hex(random_bytes(50));
            $updateQuery = mysqli_query($conn, "UPDATE admin SET reset_token='$token' WHERE email='$email'");
            
            if($updateQuery) {
                // Send reset password email
                $resetLink = "http://yourdomain.com/reset-password.php?token=$token";
                $subject = "Password Reset Request";
                $message = "Click the link below to reset your password:\n\n$resetLink";
                $headers = "From: no-reply@ingatsystem.com";
                
                if(mail($email, $subject, $message, $headers)) {
                    $_SESSION['msg'] = "An email with instructions to reset your password has been sent to $email.";
                } else {
                    $_SESSION['msg'] = "Failed to send reset email. Please try again.";
                }
            } else {
                $_SESSION['msg'] = "Failed to generate reset token. Please try again.";
            }
        } else {
            $_SESSION['msg'] = "No account found with that email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Reset Password</title>
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
                                <h4 class="fw-bold text-dark mb-2">Reset Password</h4>
                                <p class="text-muted">Enter your email address and we'll send you an email with instructions to reset your password.</p>
                            </div>
                            <form method="post" class="mt-4">
                                <span style="color:red;">
                                    <?php 
                                    if(isset($_SESSION['msg'])) {
                                        echo htmlentities($_SESSION['msg']); 
                                        unset($_SESSION['msg']);
                                    }
                                    ?>
                                </span>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-dark btn-lg fw-medium" type="submit" name="submit">Send Reset Instructions</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <p class="text-center mt-4 text-white text-opacity-50">
                        <a href="dashboard.php" class="text-decoration-none text-white fw-bold">Back to Dashboard</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>