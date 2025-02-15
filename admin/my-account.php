<?php
session_start();
include('include/config.php');

// Redirect to login if the user is not logged in
if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit;
}

// Fetch user data
$username = $_SESSION['alogin'];
$query = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$firstname = $user['firstname'];
$middlename = $user['middlename'];
$lastname = $user['lastname'];
$email = $user['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];

    // Server-side validation
    if (!preg_match("/^[a-zA-Z]*$/", $firstname) || !preg_match("/^[a-zA-Z]*$/", $middlename) || !preg_match("/^[a-zA-Z]*$/", $lastname)) {
        $error = "Only letters are allowed in the name fields.";
    } else {
        // Update the database
        $updateQuery = $conn->prepare("UPDATE admin SET firstname = ?, middlename = ?, lastname = ?, email = ? WHERE username = ?");
        $updateQuery->bind_param("sssss", $firstname, $middlename, $lastname, $email, $username);
        if ($updateQuery->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="assets/images/ingat.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="assets/css/vendor.min.css" rel="stylesheet">
    <link href="assets/css/icons.min.css" rel="stylesheet">
    <link href="assets/css/style.min.css" rel="stylesheet">
    <script src="assets/js/config.js"></script>
    <script>
        function validateForm() {
            var firstname = document.getElementById("firstname").value;
            var middlename = document.getElementById("middlename").value;
            var lastname = document.getElementById("lastname").value;
            var namePattern = /^[a-zA-Z]*$/;

            if (!namePattern.test(firstname) || !namePattern.test(middlename) || !namePattern.test(lastname)) {
                alert("Only letters are allowed in the name fields.");
                return false;
            }
            return true;
        }
    </script>
</head>
<style> 
 .content-page {
            margin-left: 250px; /* Adjust to match the width of your sidebar */
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
       </style>
<body>
    <?php include('include/header.php'); ?>
    <?php include('include/sidebar.php'); ?>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="page-title">My Account</h4>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">My Account</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4">View and manage your account details</h4>
                                <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                                <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
                                <form method="post" onsubmit="return validateForm()">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">First Name</label>
                                                <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo htmlentities($firstname); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="middlename" class="form-label">Middle Name (Optional)</label>
                                                <input type="text" id="middlename" name="middlename" class="form-control" value="<?php echo htmlentities($middlename); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">Last Name</label>
                                                <input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo htmlentities($lastname); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" id="username" class="form-control" value="<?php echo htmlentities($user['username']); ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlentities($email); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-dark btn-lg fw-medium">Save Info</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php include('include/footer.php'); ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>