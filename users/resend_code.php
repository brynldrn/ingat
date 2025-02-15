<?php
include('includes/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Error reporting for debugging
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Generate a new random verification code
        $verification_code = rand(100000, 999999); // 6-digit random number

        // Update the verification code in the database
        try {
            $update_sql = "UPDATE users SET verification_code = ? WHERE user_email = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("is", $verification_code, $email);
            $stmt->execute();

            // Send the verification code to the user's email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ingat.system@gmail.com'; // Your email
                $mail->Password = 'frqtctoqyfnuzivt'; // Your email app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Sender and recipient details
                $mail->setFrom('ingat.system@gmail.com', 'Ingat System');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your Email Verification Code';
                
                // HTML body with blue color theme and logo
                $mail->Body = "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Email Verification</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f4f4f9;
                            margin: 0;
                            padding: 0;
                        }
                        .container {
                            max-width: 600px;
                            margin: 20px auto;
                            background-color: #ffffff;
                            border-radius: 8px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        }
                        .header {
                            padding: 20px;
                            text-align: center;
                            border-top-left-radius: 8px;
                            border-top-right-radius: 8px;
                        }
                        .header h1 {
                            margin: 0;
                            font-size: 24px;
                        }
                        .content {
                            padding: 20px;
                            font-size: 16px;
                            line-height: 1.6;
                        }
                        .verification-code {
                            font-size: 20px;
                            font-weight: bold;
                            color: #0056b3;
                        }
                        .footer {
                            background-color: #f1f1f1;
                            padding: 10px;
                            text-align: center;
                            font-size: 14px;
                            color: #666666;
                            border-bottom-left-radius: 8px;
                            border-bottom-right-radius: 8px;
                        }
                        .footer a {
                            color: #0056b3;
                            text-decoration: none;
                        }
                        .logo {
                            max-width: 200px;
                            margin: 0 auto;
                            display: block;
                        }
                    </style>
                </head>
                <body>

                    <div class='container'>
                        <!-- Header -->
                        <div class='header'>
                            <img src='cid:system_logo' alt='Ingat System Logo' class='logo'>
                            <h1>Welcome to INGAT!</h1>
                        </div>

                        <!-- Content -->
                        <div class='content'>
                            <p>Dear $fullName,</p>
                            <p>Your verification code has been reset. Please use the following code to verify your email address:</p>
                            <p class='verification-code'>$verification_code</p>
                            <p>Simply enter the code on the verification page to activate your account.</p>
                            <p>If you did not request a code, please ignore this email.</p>
                        </div>

                        <!-- Footer -->
                        <div class='footer'>
                            <p>If you have any questions or need assistance, feel free to <a href='mailto:ingat.system@gmail.com'>contact us</a>.</p>
                            <p>Best regards, <br> The INGAT Team</p>
                        </div>
                    </div>

                </body>
                </html>
                ";

                // Attach your system logo (path should be relative to the location of your PHP script)
                $mail->addEmbeddedImage('../img/logo1.png', 'system_logo'); // Make sure to replace with actual path to the logo

                // Send the email
                $mail->send();
                $_SESSION['success_message'] = "A new verification code has been sent to your email!";
                header("Location: verify.php?email=" . urlencode($email));
                exit();
            } catch (Exception $e) {
                $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } catch (mysqli_sql_exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Email address is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification Code</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container">
        <div class="verification-box">
            <h2 class="text-center mb-4">Resend Verification Code</h2>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Enter Your Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <button type="submit" class="btn btn-custom" id="submitBtn" disabled>Request New Code</button>
            </form>

            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <p class="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // JavaScript to enable the button when the email field is filled
        const emailInput = document.getElementById('email');
        const submitBtn = document.getElementById('submitBtn');

        emailInput.addEventListener('input', function () {
            // Enable the button only if the email field is not empty
            submitBtn.disabled = !emailInput.value.trim();
        });
    </script>

</body>
</html>