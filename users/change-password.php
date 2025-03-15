<?php
session_start();
include('includes/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if(strlen($_SESSION['login'])==0) {   
    header('location:index.php');
} else {
    if(isset($_POST['submit'])) {
        $email = $_POST['email'];
        
        $query = mysqli_query($conn, "SELECT CONCAT(firstname, ' ', middlename, ' ', lastname) AS fullName FROM users WHERE user_email='$email'");
        if(mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            $fullName = $row['fullName'];
            $firstName = explode(' ', trim($fullName))[0]; // Extract the first name

            // Generate a unique token
            $token = bin2hex(random_bytes(50));
            $updateQuery = mysqli_query($conn, "UPDATE users SET reset_token='$token' WHERE user_email='$email'");
            
            if($updateQuery) {
                // Send reset password email
                $resetLink = "https://ingat-web-php-7q7ei.ondigitalocean.app/users/reset-password.php?token=$token";
                $subject = "Password Reset Request";

                $message = "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Password Reset Request</title>
                  <style>
                    /* Base Styles */
                    body {
                      margin: 0;
                      padding: 0;
                      background-color: #f7f7f7;
                      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                      color: #333333;
                      line-height: 1.6;
                    }
                    .container {
                      max-width: 600px;
                      margin: 40px auto;
                      background: #ffffff;
                      border-radius: 8px;
                      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                      overflow: hidden;
                    }
                    /* Header Section */
                    .header {
                      background-color: #0056b3;
                      color: #ffffff;
                      text-align: center;
                      padding: 30px;
                    }
                    .header img {
                      max-width: 100px;
                      margin-bottom: 15px;
                    }
                    .header h1 {
                      margin: 0;
                      font-size: 24px;
                      font-weight: 400;
                    }
                    /* Content Section */
                    .content {
                      padding: 30px;
                    }
                    .content p {
                      margin-bottom: 20px;
                      font-size: 16px;
                    }
                    /* Reset Link Button */
                    .reset-link {
                      display: inline-block;
                      background-color: #0056b3;
                      color: #ffffff !important;
                      text-decoration: none;
                      padding: 12px 25px;
                      border-radius: 5px;
                      font-size: 16px;
                      transition: background-color 0.3s ease;
                    }
                    .reset-link:hover {
                      background-color: #004494;
                    }
                    /* Footer Section */
                    .footer {
                      background-color: #f0f0f0;
                      text-align: center;
                      padding: 20px;
                      font-size: 14px;
                      color: #777777;
                    }
                    .footer a {
                      color: #0056b3;
                      text-decoration: none;
                    }
                  </style>
                </head>
                <body>
                  <div class='container'>
                    <div class='header'>
                      <img src='cid:logo' alt='Ingat Logo'>
                      <h1>Password Reset Request</h1>
                    </div>
                    <div class='content'>
                      <p>Dear $firstName,</p>
                      <p>We received a request to reset your password. Click the button below to reset your password:</p>
                      <p><a href='$resetLink' class='reset-link'>Reset Password</a></p>
                      <p>If you did not request a password reset, please ignore this email.</p>
                      <p>Thank you,<br>The Ingat System Team</p>
                    </div>
                    <div class='footer'>
                      <p>If you have any questions, feel free to <a href='mailto:ingat.system@gmail.com'>contact us</a>.</p>
                    </div>
                  </div>
                </body>
                </html>
                ";
                

                // Send the reset password email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'ingat.system@gmail.com';
                    $mail->Password = 'frqtctoqyfnuzivt'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    //Recipients
                    $mail->setFrom('ingat.system@gmail.com', 'Ingat System');
                    $mail->addAddress($email); // Add the user's email

                    // Attach logo
                    $mail->addEmbeddedImage('../img/logo.png', 'logo');

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;

                    $mail->send();
                    $_SESSION['msg'] = "An email with instructions to reset your password has been sent to $email.";
                } catch (Exception $e) {
                    $_SESSION['msg'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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
                        <a href="dashboard.php" class="text-decoration-none text-white fw-bold">Back to Home</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="asset/js/vendor.min.js"></script>
    <script src="asset/js/app.js"></script>
</body>
</html>