<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit; // Stop execution after redirection
}

date_default_timezone_set('Asia/Manila'); // Set timezone to Philippine time
$currentTime = date('d-m-Y h:i:s A', time());

if (isset($_POST['submit'])) {
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $middleName = filter_input(INPUT_POST, 'middleName', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

    // Handle profile photo upload
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create directory if it doesn't exist
    }

    $user_image = '';
    if (!empty($_FILES['user_image']['name'])) {
        $user_image = $_FILES['user_image']['name'];
        $user_imageTemp = $_FILES['user_image']['tmp_name'];
        $uploadFile = $uploadDir . basename($user_image);
        if (!move_uploaded_file($user_imageTemp, $uploadFile)) {
            $errormsg = "Failed to upload profile photo!";
            $user_image = '';
        }
    }

    // Handle upload_id file
    $upload_id = '';
    if (!empty($_FILES['upload_id']['name'])) {
        $upload_id = $_FILES['upload_id']['name'];
        $upload_idTemp = $_FILES['upload_id']['tmp_name'];
        $upload_idFile = $uploadDir . basename($upload_id);
        if (!move_uploaded_file($upload_idTemp, $upload_idFile)) {
            $errormsg = "Failed to upload ID!";
            $upload_id = '';
        }
    }

    // Update the user profile in the database
    $email = mysqli_real_escape_string($conn, $_SESSION['login']);
    $updateFields = [
        "firstname = '$firstName'",
        "middlename = '$middleName'",
        "lastname = '$lastName'",
        "username = '$username'",
        "contact_no = '$contact_no'",
        "address = '$address'"
    ];

    if ($user_image) {
        $updateFields[] = "user_image = '$uploadFile'";
    }
    if ($upload_id) {
        $updateFields[] = "upload_id = '$upload_idFile'";
    }

    $updateQuery = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE user_email = '$email'";
    $query = mysqli_query($conn, $updateQuery);

    if ($query) {
        $successmsg = "Profile Updated Successfully!";
    } else {
        $errormsg = "Profile not updated! Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - INGAT</title>
    <link rel="shortcut icon" href="asset/images/ingat.ico">
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style>
        /* General Styling */
        body {
            background-color: #f5f5f5;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            overflow-x: hidden;
        }

        /* Header and Sidebar Assumptions */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px; /* Adjust based on your header height */
            background-color: #060270;
            z-index: 1000;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #060270;
            z-index: 1000;
            padding-top: 60px; /* Offset for header height */
        }

        #main-content {
            margin-left: 250px; /* Match sidebar width */
            margin-top: 60px; /* Match header height */
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .wrapper {
            min-height: calc(100vh - 60px); /* Adjust based on footer height */
        }

        .container {
            max-width: 100%;
            padding: 0 15px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 900px;
            margin-left: 10%;
        }

        .card-header {
            background-color: #060270;
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 1.5rem;
        }

        .card-body {
            padding: 30px;
            background-color: white;
            border-radius: 0 0 10px 10px;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            font-family: 'Arial', sans-serif;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #98DED9;
            box-shadow: 0 0 5px rgba(152, 222, 217, 0.5);
            outline: none;
        }

        /* Profile Photo Styling */
        .profile-photo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-photo-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #060270;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .profile-photo-container img:hover {
            transform: scale(1.05);
        }

        .profile-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-info h4 {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
            color: #021D58;
        }

        .profile-info p {
            font-size: 1.2rem;
            color: #666;
            margin-top: 5px;
        }

        /* Form Layout */
        .form-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .col-md-6 {
            flex: 1;
            min-width: 300px;
        }

        /* Contact No Styling */
        .contact_no-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact_no-group .prefix {
            background-color: #060270;
            color: white;
            text-align: center;
            font-weight: 600;
            padding: 10px;
            border-radius: 5px;
            min-width: 60px;
        }

        .contact_no-group .phone-number {
            flex: 1;
        }

        /* Button Styling */
        .btn-primary {
            background-color: #98DED9;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            color: #021D58;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #004A99;
            color: white;
        }

        /* Alerts */
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert .close {
            font-size: 1rem;
            color: inherit;
        }

        /* Responsive Layout */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-250px);
            }

            #main-content {
                margin-left: 0;
                margin-top: 60px;
            }
        }

        @media (max-width: 768px) {
            .card {
                margin: 15px;
            }

            .form-row {
                flex-direction: column;
            }

            .col-md-6 {
                min-width: 100%;
            }

            .profile-photo-container img {
                width: 120px;
                height: 120px;
            }

            .profile-info h4 {
                font-size: 1.5rem;
            }

            .profile-info p {
                font-size: 1rem;
            }

            .contact_no-group {
                flex-direction: column;
                align-items: flex-start;
            }

            .contact_no-group .prefix {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .card-body {
                padding: 20px;
            }

            .profile-photo-container img {
                width: 100px;
                height: 100px;
            }

            .profile-info h4 {
                font-size: 1.3rem;
            }

            .profile-info p {
                font-size: 0.9rem;
            }

            .btn-primary {
                font-size: 0.9rem;
                padding: 8px 15px;
            }
        }
    </style>
</head>
<body>
    <section id="container">
        <?php include('includes/header.php'); ?>
        <?php include("includes/sidebar.php"); ?>

        <section id="main-content">
            <section class="wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Edit Profile</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($successmsg)) { ?>
                                        <div class="alert alert-success alert-dismissable" id="success-alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <b>Success!</b> <?php echo htmlspecialchars($successmsg); ?>
                                        </div>
                                    <?php } elseif (isset($errormsg)) { ?>
                                        <div class="alert alert-danger alert-dismissable" id="error-alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <b>Error!</b> <?php echo htmlspecialchars($errormsg); ?>
                                        </div>
                                    <?php } ?>

                                    <script>
                                        setTimeout(function() {
                                            const successAlert = document.getElementById('success-alert');
                                            const errorAlert = document.getElementById('error-alert');
                                            if (successAlert) successAlert.style.display = 'none';
                                            if (errorAlert) errorAlert.style.display = 'none';
                                        }, 4000);
                                    </script>

                                    <?php
                                    $email = mysqli_real_escape_string($conn, $_SESSION['login']);
                                    $query = mysqli_query($conn, "SELECT * FROM users WHERE user_email='$email'");
                                    if ($query && $row = mysqli_fetch_array($query)) {
                                    ?>
                                        <div class="profile-photo-container">
                                            <?php
                                            $user_image = !empty($row['user_image']) ? htmlspecialchars($row['user_image']) : 'uploads/profile.jpg';
                                            ?>
                                            <img src="<?php echo $user_image; ?>" alt="Profile Image">
                                        </div>
                                        <div class="profile-info">
                                            <h4><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?></h4>
                                            <p>@<?php echo htmlspecialchars($row['username']); ?></p>
                                        </div>
                                        <form method="post" name="profile" enctype="multipart/form-data">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="user_image">Profile Photo</label>
                                                        <input type="file" class="form-control" name="user_image" accept="image/*">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="upload_id">Valid ID</label>
                                                        <input type="file" class="form-control" name="upload_id" accept="image/jpeg,image/png,application/pdf">
                                                        <?php if (!empty($row['upload_id'])) { ?>
                                                            <small>Current: <a href="<?php echo htmlspecialchars($row['upload_id']); ?>" target="_blank">View ID</a></small>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="firstName">First Name</label>
                                                        <input type="text" class="form-control" name="firstName" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="middleName">Middle Name</label>
                                                        <input type="text" class="form-control" name="middleName" value="<?php echo htmlspecialchars($row['middlename']); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="lastName">Last Name</label>
                                                        <input type="text" class="form-control" name="lastName" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="username">Username</label>
                                                        <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contact_no">Contact No</label>
                                                        <div class="contact_no-group">
                                                            <span class="prefix">+63</span>
                                                            <input type="text" class="form-control phone-number" name="contact_no" placeholder="10-digit number" value="<?php echo htmlspecialchars(substr($row['contact_no'], 0)); ?>" pattern="\d{10}" maxlength="10" title="Enter 10 digits after +63" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="user_email">Email</label>
                                                        <input type="email" name="user_email" value="<?php echo htmlspecialchars($row['user_email']); ?>" class="form-control" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="address">Address</label>
                                                        <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($row['address']); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    <?php } else { ?>
                                        <div class="alert alert-danger">
                                            <b>Error!</b> Unable to fetch user data.
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </section>
    <?php include('includes/footer.php'); ?>
</body>
</html>
<?php
mysqli_close($conn);
?>