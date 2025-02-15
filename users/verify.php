<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'], $_POST['verification_code'])) {
        $email = $_POST['email'];
        $verification_code = $_POST['verification_code'];

        try {
            $sql = "SELECT * FROM users WHERE user_email = ? AND verification_code = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $verification_code); 
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $update_sql = "UPDATE users SET verification_code = NULL, is_verified = 1 WHERE user_email = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("s", $email);
                $update_stmt->execute();

                $_SESSION['success_message'] = "Your email has been verified! You can now log in.";
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Invalid verification code. Please try again.";
            }
        } catch (mysqli_sql_exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Missing email or verification code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            display: flex;
            justify-content: center;
        }
        .verification-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .otp-field {
            flex-direction: row;
            column-gap: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .otp-field input {
            height: 45px;
            width: 42px;
            border-radius: 6px;
            outline: none;
            font-size: 1.125rem;
            text-align: center;
            border: 1px solid #ddd;
        }
        .otp-field input:focus {
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
        }
        .otp-field input::-webkit-inner-spin-button,
        .otp-field input::-webkit-outer-spin-button {
            display: none;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
        }
        .resend {
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="verification-box">
            <h2 class="text-center mb-4">Email Verification</h2>
            <p class="text-center mb-4">Your verification code has been sent to your email.</p>

            <form method="POST" action="">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>" />
                <div class="otp-field mb-4">
                    <input type="text" id="verification_code_1" name="verification_code_1" required maxlength="1" />
                    <input type="text" id="verification_code_2" name="verification_code_2" required maxlength="1" disabled />
                    <input type="text" id="verification_code_3" name="verification_code_3" required maxlength="1" disabled />
                    <input type="text" id="verification_code_4" name="verification_code_4" required maxlength="1" disabled />
                    <input type="text" id="verification_code_5" name="verification_code_5" required maxlength="1" disabled />
                    <input type="text" id="verification_code_6" name="verification_code_6" required maxlength="1" disabled />
                </div>

                <!-- Hidden field for the full verification code -->
                <input type="hidden" name="verification_code" id="verification_code" value="" />
                <button class="btn btn-primary mb-3" id="verify-btn" disabled type="submit">
                    Verify
                </button>
            </form>

            <p class="resend text-muted mb-0">
                Didn't receive the code? <a href="resend_code.php">Request again</a>
            </p>

            <?php if (isset($error_message)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const otpFields = document.querySelectorAll(".otp-field > input");
        const hiddenCodeInput = document.querySelector("#verification_code");
        const button = document.querySelector("#verify-btn");

        // Combine OTP input values
        function combineOtp() {
            let otp = '';
            otpFields.forEach(input => {
                otp += input.value;  // Append each input value to the OTP string
            });
            hiddenCodeInput.value = otp;  // Set the hidden input to the full OTP
        }

        document.querySelector("form").addEventListener("submit", function(e) {
            combineOtp();  // Combine OTP before submitting the form
        });

        window.addEventListener("load", () => otpFields[0].focus());

        otpFields.forEach((input, index1) => {
            input.addEventListener("keyup", (e) => {
                const currentInput = input;
                const nextInput = input.nextElementSibling;
                const prevInput = input.previousElementSibling;

                if (currentInput.value.length > 1) {
                    currentInput.value = ""; // Prevent more than 1 digit
                    return;
                }

                // Move to next input when a digit is typed
                if (nextInput && nextInput.hasAttribute("disabled") && currentInput.value !== "") {
                    nextInput.removeAttribute("disabled");
                    nextInput.focus();
                }

                // Handle Backspace to go back to the previous input
                if (e.key === "Backspace") {
                    if (prevInput) {
                        prevInput.removeAttribute("disabled");
                        prevInput.focus();
                    }
                }

                // Enable button if all OTP fields are filled
                button.setAttribute("disabled", true);
                if (Array.from(otpFields).every(input => input.value !== "")) {
                    button.removeAttribute("disabled");
                }
            });
        });
    </script>
</body>
</html>