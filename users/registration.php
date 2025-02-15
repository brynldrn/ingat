<?php
include('includes/config.php'); 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

error_reporting(0); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $firstname    = $_POST['firstname'];
    $middlename   = $_POST['middlename'];
    $lastname     = $_POST['lastname'];
    $email        = $_POST['email'];
    $password     = $_POST['password'];
    $repassword   = $_POST['repassword'];
    
    // Check if passwords match
    if ($password !== $repassword) {
        $msg = "Passwords do not match. Please try again.";
    } else {
        $passwordHashed   = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
        $verification_code = rand(100000, 999999); 
        $status            = 1; 

        try {
            // Check if email already exists
            $check_sql = "SELECT * FROM users WHERE user_email = '$email'";
            $check_query = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_query) > 0) {
                $msg = "Email already exists. Please try again.";
            } else {
                // Insert the new user into the database
                $insert_sql = "INSERT INTO users (firstname, middlename, lastname, user_email, password, verification_code, status, is_verified) 
                               VALUES ('$firstname', '$middlename', '$lastname', '$email', '$passwordHashed', '$verification_code', '$status', 0)";
                mysqli_query($conn, $insert_sql);

                // Send verification email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'ingat.system@gmail.com';
                    $mail->Password   = 'frqtctoqyfnuzivt'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('ingat.system@gmail.com', 'Ingat System');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification';

                    // HTML email body (blue theme with embedded logo)
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
                          background-color: #007bff;
                          color: #ffffff;
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
                        <div class='header'>
                          <img src='cid:system_logo' alt='Ingat System Logo' class='logo'>
                          <h1>Welcome to INGAT!</h1>
                        </div>
                        <div class='content'>
                          <p>Dear $firstname $lastname,</p>
                          <p>Thank you for registering with INGAT! To complete your registration and verify your email address, please use the following verification code:</p>
                          <p class='verification-code'>$verification_code</p>
                          <p>Enter this code on the verification page to activate your account.</p>
                          <p>If you did not sign up for an INGAT account, please ignore this email.</p>
                        </div>
                        <div class='footer'>
                          <p>If you have any questions or need assistance, feel free to <a href='mailto:ingat.system@gmail.com'>contact us</a>.</p>
                          <p>Best regards,<br> The INGAT Team</p>
                        </div>
                      </div>
                    </body>
                    </html>
                    ";

                    // Attach the logo image (adjust the path as necessary)
                    $mail->addEmbeddedImage('../img/logo.png', 'system_logo');
                    $mail->send();
                    $msg = "Registration successful. Please verify your email. Check your inbox for the verification code.";
                    header("Location: verify.php?email=" . urlencode($email)); 
                    exit();
                } catch (Exception $e) {
                    $msg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        } catch (mysqli_sql_exception $e) {
            $msg = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - INGAT</title>
  <link rel="shortcut icon" href="asset/images/ingat.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&amp;display=swap" rel="stylesheet">
  <link href="asset/css/vendor.min.css" rel="stylesheet" type="text/css" />
  <link href="asset/css/icons.min.css" rel="stylesheet" type="text/css" />
  <link href="asset/css/style.min.css" rel="stylesheet" type="text/css" />
  <script src="asset/js/config.js"></script>
  <style>
    /* Similar styling to the Reset Password page */
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
      padding: 2rem;
      background: white;
      border-radius: 10px;
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
      box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    .btn-dark {
      background: #000;
      color: #fff;
    }
    /* Multi-Step Form Specific Styles */
    .step {
      display: none;
    }
    .step.active {
      display: block;
    }
    .navigation-buttons {
      margin-top: 1.5rem;
      display: flex;
      gap: 1rem;
    }
    .navigation-buttons button {
      flex: 1;
      font-size: 1rem;
    }
    /* Scrollable Terms of Use */
    .terms-content {
      height: 300px;
      overflow-y: auto;
      border: 1px solid #ddd;
      padding: 1rem;
      background-color: #f9f9f9;
      border-radius: 8px;
      font-size: 1rem;
      margin-bottom: 1rem;
      color: #0a79ca;
    }
    @media (max-width: 600px) {
      .card-body {
        padding: 1.5rem;
      }
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    #passwordError {
  color: red;
}
h1 {
  color: #0092bf;
}
label {
  color: black;
}
  </style>
</head>
<body class="authentication-bg">
  <div class="account-pages py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="card shadow-lg">
            <div class="card-body p-5" style="animation: fadeIn 0.5s ease-in-out;">
              <div class="text-center">
                <div class="mx-auto mb-4 auth-logo">
                  <a href="dashboard.php" class="logo-light">
                    <img src="asset/images/logodark.png" alt="logo dark">
                  </a>
                </div>
                <h4 class="fw-bold text-dark mb-2">Sign Up</h4>
                <p class="text-muted">Fill in your details to create your account.</p>
              </div>
              <form method="post" id="registrationForm" onsubmit="return finalValidateForm()">
                <!-- Display PHP message if any -->
                <p style="text-align:center; color: green;"><?php if(isset($msg)) echo htmlentities($msg); ?></p>
                <!-- Step 1: Personal Information -->
                <div id="step1" class="step active">
                  <div class="mb-3">
                    <label for="firstname" class="form-label">First Name</label>
                    <input type="text" class="form-control" placeholder="First Name" name="firstname" id="firstname" required>
                  </div>
                  <div class="mb-3">
                    <label for="middlename" class="form-label">Middle Name (Optional)</label>
                    <input type="text" class="form-control" placeholder="Middle Name" name="middlename" id="middlename">
                  </div>
                  <div class="mb-3">
                    <label for="lastname" class="form-label">Last Name</label>
                    <input type="text" class="form-control" placeholder="Last Name" name="lastname" id="lastname" required>
                  </div>
                  <div class="navigation-buttons">
                    <button type="button" class="btn btn-dark" onclick="nextStep(2)">Next</button>
                  </div>
                </div>
                <!-- Step 2: Account Details -->
                <div id="step2" class="step">
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <!-- You may also add a pattern attribute if desired -->
                    <input type="email" class="form-control" placeholder="Email" id="email" name="email" required>
                  </div>
                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-container position-relative">
                      <input type="password" class="form-control" placeholder="Password" id="password" name="password" required oninput="validatePassword()">
                      <i class="fa fa-eye position-absolute" id="eye1" style="top:50%; right:15px; transform: translateY(-50%);" onclick="togglePassword('password', 'eye1')"></i>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="repassword" class="form-label">Confirm Password</label>
                    <div class="password-container position-relative">
                      <input type="password" class="form-control" placeholder="Confirm Password" id="repassword" name="repassword" required oninput="validatePassword()">
                      <i class="fa fa-eye position-absolute" id="eye2" style="top:50%; right:15px; transform: translateY(-50%);" onclick="togglePassword('repassword', 'eye2')"></i>
                    </div>
                    <span id="passwordError" class="error-message">Passwords do not match or do not meet complexity requirements.</span>
                  </div>
                  <div class="navigation-buttons">
                    <button type="button" class="btn btn-dark" onclick="prevStep(1)">Back</button>
                    <button type="button" class="btn btn-dark" onclick="nextStep(3)">Next</button>
                  </div>
                </div>
                <!-- Step 3: Terms of Use -->
                <div id="step3" class="step">
                  <div class="terms-content">
                    <h1 style="font-size: 22px; margin-bottom: 10px;">Terms of Use</h1>
                    <p>Welcome to INGAT (Inform, Navigate, Guard, Act, Together)! By using our platform, you agree to comply with the following Terms of Use. Please read them carefully before accessing or using our system.</p>
                    <h2 style="font-size: 18px; margin-top: 15px;">1. Acceptance of Terms</h2>
                    <p>By accessing and using the INGAT platform, you agree to be bound by these Terms of Use, our Privacy Policy, and any additional guidelines or rules provided within the system. If you do not agree to these terms, you must not use the platform.</p>
                    <h2 style="font-size: 18px; margin-top: 15px;">2. Purpose of the System</h2>
                    <p>INGAT is a platform designed for crime reporting, safety updates, and community engagement. The primary goals are to:</p>
                    <ul style="margin-left:20px;">
                      <li>Allow users to report incidents responsibly.</li>
                      <li>Provide real-time safety information.</li>
                      <li>Foster collaboration among users to promote safety.</li>
                    </ul>
                    <h2 style="font-size: 18px; margin-top: 15px;">3. User Responsibilities</h2>
                    <p>When using the INGAT platform, you agree to:</p>
                    <ul style="margin-left:20px;">
                      <li>Provide accurate and truthful information during registration and reporting.</li>
                      <li>Use the system responsibly and not for malicious, fraudulent, or defamatory purposes.</li>
                      <li>Maintain the confidentiality of your login credentials.</li>
                    </ul>
                    <h2 style="font-size: 18px; margin-top: 15px;">4. Prohibited Activities</h2>
                    <p>You agree not to:</p>
                    <ul style="margin-left:20px;">
                      <li>Submit false or misleading reports.</li>
                      <li>Use the platform for illegal activities, harassment, or threats.</li>
                      <li>Access or attempt to access another user's account without authorization.</li>
                      <li>Distribute malware, spam, or any content that disrupts the platform's functionality.</li>
                      <li>Reverse-engineer, decompile, or disassemble the platform.</li>
                    </ul>
                    <h2 style="font-size: 18px; margin-top: 15px;">5. Reporting Obligations</h2>
                    <p>Reports made on the INGAT system should be genuine and based on real incidents. Users found submitting false reports may have their accounts suspended or terminated and may face legal consequences.</p>
                    <h2 style="font-size: 18px; margin-top: 15px;">6. Privacy and Data Protection</h2>
                    <p>Your use of INGAT is subject to our Privacy Policy, which outlines how your data is collected, used, and protected. By using the system, you consent to the collection and use of your data as described in the Privacy Policy.</p>
                    <h2 style="font-size: 18px; margin-top: 15px;">7. Account Suspension or Termination</h2>
                    <p>INGAT reserves the right to suspend or terminate accounts that violate these terms, engage in prohibited activities, or misuse the platform.</p>
                    <h2 style="font-size: 18px; margin-top: 15px;">8. Limitation of Liability</h2>
                    <p>While INGAT strives to provide accurate and reliable services, we do not guarantee the complete accuracy or timeliness of the information on the platform and are not liable for damages arising from its misuse.</p>
                    <h2 style="font-size: 18px; margin-top: 15px;">9. Updates to Terms</h2>
                    <p>We may update these Terms of Use periodically. Continued use of the system after updates indicates your acceptance of the revised terms.</p>
                    <h2 style="font-size: 18px; margin-top: 15px;">10. Contact Us</h2>
                    <p>If you have questions or concerns about these Terms of Use, please contact us at <strong>ingat.system@gmail.com</strong>.</p>
                  </div>
                  <div class="terms-container">
                    <label>
                      <input type="checkbox" id="terms" name="terms" required>
                      I accept the Terms of Use.
                    </label>
                  </div>
                  <span id="termsError" class="error-message">You must accept the Terms of Use.</span>
                  <div class="navigation-buttons">
                    <button type="button" class="btn btn-dark" onclick="prevStep(2)">Back</button>
                    <button type="submit" class="btn btn-dark">Register</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <p class="text-center mt-4 text-white text-opacity-50">
            Already have an account? <a href="index.php" class="fw-bold">Login here</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <script src="asset/js/vendor.min.js"></script>
  <script src="asset/js/app.js"></script>
  <script>
    var currentStep = 1;
    function nextStep(step) {
      // Validate current step before moving forward
      if (currentStep === 1) {
        var firstname = document.getElementById('firstname').value;
        var lastname = document.getElementById('lastname').value;
        if (firstname.trim() === "" || lastname.trim() === "") {
          alert("Please fill in all required fields in Personal Information.");
          return false;
        }
      }
      if (currentStep === 2) {
        // Validate Step 2: Account Details
        if (!validatePassword()) {
          return false;
        }
        // Validate Email Format & Allowed Providers
        if (!validateEmail()) {
          alert("Please enter a valid email address from an allowed provider (e.g., gmail.com, yahoo.com, outlook.com, hotmail.com, icloud.com).");
          return false;
        }
      }
      document.getElementById("step" + currentStep).classList.remove("active");
      document.getElementById("step" + step).classList.add("active");
      currentStep = step;
    }

    function prevStep(step) {
      document.getElementById("step" + currentStep).classList.remove("active");
      document.getElementById("step" + step).classList.add("active");
      currentStep = step;
    }

    function togglePassword(passwordId, iconId) {
      var passwordField = document.getElementById(passwordId);
      var icon = document.getElementById(iconId);
      if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }

    function validatePassword() {
      var password = document.getElementById('password').value;
      var repassword = document.getElementById('repassword').value;
      var errorElement = document.getElementById('passwordError');
      // Regex for password: at least 8 characters, one uppercase, one lowercase, and one number.
      var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
      if (!passwordRegex.test(password)) {
        errorElement.style.display = "block";
        errorElement.textContent = "Password must be at least 8 characters long and include uppercase, lowercase, and numbers.";
        return false;
      } else if (password !== repassword) {
        errorElement.style.display = "block";
        errorElement.textContent = "Passwords do not match.";
        return false;
      } else {
        errorElement.style.display = "none";
        return true;
      }
    }

    // New function to validate the email format and domain
    function validateEmail() {
      var emailField = document.getElementById('email');
      var emailValue = emailField.value;
      // Basic email format regex
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailValue)) {
        return false;
      }
      // Allowed email domains list
      var allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
      var domain = emailValue.split('@')[1].toLowerCase();
      if (allowedDomains.indexOf(domain) === -1) {
        return false;
      }
      return true;
    }

    function finalValidateForm() {
      // Final validation before submission
      if (!validatePassword()) {
        return false;
      }
      if (!validateEmail()) {
        alert("Please enter a valid email address from an allowed provider (e.g., gmail.com, yahoo.com, outlook.com, hotmail.com, icloud.com).");
        return false;
      }
      var terms = document.getElementById('terms');
      var termsError = document.getElementById('termsError');
      if (!terms.checked) {
        termsError.style.display = "block";
        return false;
      }
      termsError.style.display = "none";
      return true;
    }
  </script>
</body>
</html>
