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
    $terms        = isset($_POST['terms']);
    
    // Check if terms are accepted
    if (!$terms) {
        $msg = "You must accept the Terms of Use to register.";
    }
    // Check if passwords match
    elseif ($password !== $repassword) {
        $msg = "Passwords do not match. Please try again.";
    } else {
        $passwordHashed   = password_hash($password, PASSWORD_DEFAULT);
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
  <meta charset="utf-8" />
  <title>Sign Up - INGAT</title>
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
      flex-direction: row; /* Default for desktop */
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
      margin-left: -10%;
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
      max-width: 500px;
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
    .form-row {
      display: flex;
      gap: 15px;
      margin-bottom: 1rem;
    }
    .form-group {
      flex: 1;
    }
    .form-label {
      font-size: 0.875rem;
      font-weight: 500;
      color: #000;
      margin-bottom: 0.4rem;
      display: block;
    }
    .form-label.required::after {
      content: '*';
      color: red;
      margin-left: 2px;
    }
    .form-control {
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
      padding: 0.5rem 1rem;
      font-size: 1rem;
      width: 100%;
      background-color: #fff;
      color: #000;
      box-sizing: border-box;
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
    .terms-container {
      margin-top: 1rem;
      margin-bottom: 1.5rem;
    }
    .terms-container label {
      font-size: 0.875rem;
      color: #000;
    }
    .terms-container a {
      color: #4a90e2;
      text-decoration: none;
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
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border-radius: 0.25rem;
    }
    .error-message {
      color: red;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      display: none;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column; /* Stack vertically on mobile */
        height: auto; /* Allow height to adjust */
        padding: 1rem;
      }
      .left-section {
        flex: none;
        width: 100%;
        padding: 1rem;
        margin-left: 0; /* Remove negative margin */
        min-height: 200px;
      }
      .left-section img {
        max-width: 120px; /* Smaller logo */
      }
      .left-section p {
        font-size: 0.9rem; /* Smaller text */
      }
      .right-section {
        flex: none;
        width: 100%;
        padding: 1rem;
      }
      .card {
        max-width: 100%; /* Full width on mobile */
        padding: 1.5rem;
      }
      .card-title {
        font-size: 1.25rem; /* Smaller title */
      }
      .card-subtitle {
        font-size: 0.85rem; /* Smaller subtitle */
      }
      .form-row {
        flex-direction: column; /* Stack form fields vertically */
        gap: 0;
      }
      .form-group {
        margin-bottom: 1rem;
      }
      .form-control {
        font-size: 0.9rem; /* Slightly smaller input text */
      }
      .btn-primary {
        padding: 0.6rem; /* Smaller button */
        font-size: 0.9rem;
      }
      .terms-container {
        margin-bottom: 1rem;
      }
      /* Modal adjustments */
      #termsModal > div {
        width: 95%; /* Wider on small screens */
        margin: 10% auto;
        padding: 15px;
      }
      .terms-content {
        max-height: 300px; /* Smaller modal height */
        font-size: 0.9rem;
      }
    }

    /* Tablets (optional intermediate step) */
    @media (min-width: 769px) and (max-width: 991px) {
      .login-container {
        max-width: 800px;
      }
      .left-section, .right-section {
        padding: 1.5rem;
      }
      .card {
        max-width: 450px;
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
        <h4 class="card-title">Sign Up</h4>
        <p class="card-subtitle">Create your account to get started.</p>
        <form method="post" class="mt-4" onsubmit="return finalValidateForm()">
          <?php if(isset($msg)): ?>
            <div class="<?php echo strpos($msg, 'successful') !== false ? 'alert-success' : 'alert-danger'; ?>">
              <?= htmlentities($msg); ?>
            </div>
          <?php endif; ?>
          <!-- First Row: First Name and Middle Name -->
          <div class="form-row">
            <div class="form-group">
              <label for="firstname" class="form-label required">First Name</label>
              <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First name" style="background-color:white; color:rgb(0, 0, 0);" required>
            </div>
            <div class="form-group">
              <label for="middlename" class="form-label">Middle Name (Optional)</label>
              <input type="text" style="background-color:white; color:rgb(0, 0, 0);" class="form-control" name="middlename" id="middlename" placeholder="Middle name">
            </div>
          </div>
          <!-- Second Row: Last Name and Email -->
          <div class="form-row">
            <div class="form-group">
              <label for="lastname" class="form-label required">Last Name</label>
              <input type="text" style="background-color:white; color:rgb(0, 0, 0);" class="form-control" name="lastname" id="lastname" placeholder="Last name" required>
            </div>
            <div class="form-group">
              <label for="email" class="form-label required">Email Address</label>
              <input type="email" style="background-color:white; color:rgb(0, 0, 0);" class="form-control" name="email" id="email" placeholder="Email address" required>
            </div>
          </div>
          <!-- Third Row: Password and Confirm Password -->
          <div class="form-row">
            <div class="form-group">
              <label for="password" class="form-label required">Password</label>
              <div class="password-container">
                <input type="password" style="background-color:white; color:rgb(0, 0, 0);" class="form-control" name="password" id="password" placeholder="Password" required oninput="validatePassword()">
                <i class="fa fa-eye" id="togglePassword1" style="cursor: pointer;"></i>
              </div>
            </div>
            <div class="form-group">
              <label for="repassword" class="form-label required">Confirm Password</label>
              <div class="password-container">
                <input type="password" style="background-color:white; color:rgb(0, 0, 0);" class="form-control" name="repassword" id="repassword" placeholder="Confirm password" required oninput="validatePassword()">
                <i class="fa fa-eye" id="togglePassword2" style="cursor: pointer;"></i>
              </div>
            </div>
          </div>
          <span id="passwordError" class="error-message">Passwords do not match or do not meet complexity requirements.</span>
          <div class="terms-container">
            <label>
              <input type="checkbox" id="terms" name="terms" required>
              I accept the <a href="#" onclick="openTermsModal(); return false;">Terms of Use</a>.
            </label>
            <span id="termsError" class="error-message">You must accept the Terms of Use.</span>
          </div>
          <div class="d-grid mt-3">
            <button type="submit" class="btn btn-primary">Sign Up</button>
          </div>
          <div class="signup-link">
            Already have an account? <a href="index.php">Sign in</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal for Terms of Use -->
  <div id="termsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
  <div style="background: #fff; width: 90%; max-width: 600px; margin: 5% auto; padding: 20px; border-radius: 8px; position: relative; color: black;">
    <span onclick="closeTermsModal()" style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer;">×</span>
    <div class="terms-content" style="max-height: 400px; overflow-y: auto; padding: 1rem;">
      <h1 style="font-size: 22px; margin-bottom: 10px; color: #0092bf;">Terms of Use</h1>
      <p>Welcome to INGAT (Inform, Navigate, Guard, Act, Together)! By using our platform, you agree to comply with the following Terms of Use. Please read them carefully before accessing or using our system.</p>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">1. Acceptance of Terms</h2>
      <p>By accessing and using the INGAT platform, you agree to be bound by these Terms of Use, our Privacy Policy, and any additional guidelines or rules provided within the system. If you do not agree to these terms, you must not use the platform.</p>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">2. Purpose of the System</h2>
      <p>INGAT is a platform designed for crime reporting, safety updates, and community engagement. The primary goals are to:</p>
      <ul style="margin-left:20px;">
        <li>Allow users to report incidents responsibly.</li>
        <li>Provide real-time safety information.</li>
        <li>Foster collaboration among users to promote safety.</li>
      </ul>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">3. User Responsibilities</h2>
      <p>When using the INGAT platform, you agree to:</p>
      <ul style="margin-left:20px;">
        <li>Provide accurate and truthful information during registration and reporting.</li>
        <li>Use the system responsibly and not for malicious, fraudulent, or defamatory purposes.</li>
        <li>Maintain the confidentiality of your login credentials.</li>
      </ul>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">4. Prohibited Activities</h2>
      <p>You agree not to:</p>
      <ul style="margin-left:20px;">
        <li>Submit false or misleading reports.</li>
        <li>Use the platform for illegal activities, harassment, or threats.</li>
        <li>Access or attempt to access another user's account without authorization.</li>
        <li>Distribute malware, spam, or any content that disrupts the platform's functionality.</li>
        <li>Reverse-engineer, decompile, or disassemble the platform.</li>
      </ul>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">5. Reporting Obligations</h2>
      <p>Reports made on the INGAT system should be genuine and based on real incidents. Users found submitting false reports may have their accounts suspended or terminated and may face legal consequences.</p>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">6. Privacy and Data Protection</h2>
      <p>Your use of INGAT is subject to our Privacy Policy, which outlines how your data is collected, used, and protected. By using the system, you consent to the collection and use of your data as described in the Privacy Policy.</p>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">7. Account Suspension or Termination</h2>
      <p>INGAT reserves the right to suspend or terminate accounts that violate these terms, engage in prohibited activities, or misuse the platform.</p>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">8. Limitation of Liability</h2>
      <p>While INGAT strives to provide accurate and reliable services, we do not guarantee the complete accuracy or timeliness of the information on the platform and are not liable for damages arising from its misuse.</p>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">9. Updates to Terms</h2>
      <p>We may update these Terms of Use periodically. Continued use of the system after updates indicates your acceptance of the revised terms.</p>
      <h2 style="font-size: 18px; margin-top: 15px;color: #0092bf;">10. Contact Us</h2>
      <p>If you have questions or concerns about these Terms of Use, please contact us at <strong>ingat.system@gmail.com</strong>.</p>
    </div>
  </div>
</div>

  <script src="asset/js/vendor.min.js"></script>
  <script src="asset/js/app.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const passwordField = document.getElementById("password");
      const togglePassword1 = document.getElementById("togglePassword1");
      togglePassword1.addEventListener("click", function () {
        if (passwordField.type === "password") {
          passwordField.type = "text";
          togglePassword1.classList.remove("fa-eye");
          togglePassword1.classList.add("fa-eye-slash");
        } else {
          passwordField.type = "password";
          togglePassword1.classList.remove("fa-eye-slash");
          togglePassword1.classList.add("fa-eye");
        }
      });

      const repasswordField = document.getElementById("repassword");
      const togglePassword2 = document.getElementById("togglePassword2");
      togglePassword2.addEventListener("click", function () {
        if (repasswordField.type === "password") {
          repasswordField.type = "text";
          togglePassword2.classList.remove("fa-eye");
          togglePassword2.classList.add("fa-eye-slash");
        } else {
          repasswordField.type = "password";
          togglePassword2.classList.remove("fa-eye-slash");
          togglePassword2.classList.add("fa-eye");
        }
      });

      const nameFields = ["firstname", "middlename", "lastname"];
      nameFields.forEach(function (fieldId) {
        document.getElementById(fieldId).addEventListener("input", function () {
          this.value = this.value.replace(/[^a-zA-Z\s]/g, "");
        });
      });

      document.getElementById("passwordError").style.display = "none";
      document.getElementById("termsError").style.display = "none";
    });

    function validatePassword() {
      const password = document.getElementById('password').value;
      const repassword = document.getElementById('repassword').value;
      const errorElement = document.getElementById('passwordError');
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
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

    function validateEmail() {
      const emailField = document.getElementById('email');
      const emailValue = emailField.value;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailValue)) {
        return false;
      }
      const allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
      const domain = emailValue.split('@')[1].toLowerCase();
      if (allowedDomains.indexOf(domain) === -1) {
        return false;
      }
      return true;
    }

    function finalValidateForm() {
      // Validate password
      if (!validatePassword()) {
        return false;
      }
      // Validate email
      if (!validateEmail()) {
        alert("Please enter a valid email address from an allowed provider (e.g., gmail.com, yahoo.com, outlook.com, hotmail.com, icloud.com).");
        return false;
      }
      // Validate terms
      const terms = document.getElementById('terms');
      const termsError = document.getElementById('termsError');
      if (!terms.checked) {
        termsError.style.display = "block";
        return false;
      }
      termsError.style.display = "none";
      return true;
    }

    // Functions to open and close the Terms of Use modal
    function openTermsModal() {
      document.getElementById('termsModal').style.display = 'block';
    }

    function closeTermsModal() {
      document.getElementById('termsModal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      const modal = document.getElementById('termsModal');
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>
</html>