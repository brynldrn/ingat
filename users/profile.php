<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
} else {
    date_default_timezone_set('Asia/Kolkata');
    $currentTime = date('d-m-Y h:i:s A', time());

    if (isset($_POST['submit'])) {
        $firstName = $_POST['firstName'];
        $middleName = $_POST['middleName'];
        $lastName = $_POST['lastName'];
        $username = $_POST['username'];
        $contact_no = $_POST['contact_no'];
        $address = $_POST['address'];

        // Handle profile photo upload
        $user_image = $_FILES['user_image']['name'];
        $user_imageTemp = $_FILES['user_image']['tmp_name'];
        $uploadDir = "../uploads/";
        $uploadFile = $uploadDir . basename($user_image);

        // Handle upload_id file
        $upload_id = $_FILES['upload_id']['name'];
        $upload_idTemp = $_FILES['upload_id']['tmp_name'];
        $upload_idFile = $uploadDir . basename($upload_id);

        if ($user_image != '') {
            move_uploaded_file($user_imageTemp, $uploadFile);
            $query = mysqli_query($conn, "UPDATE users SET firstname='$firstName', middlename='$middleName', lastname='$lastName', username='$username', contact_no='$contact_no', address='$address', user_image='$uploadFile' WHERE user_email='" . $_SESSION['login'] . "'");
        } else {
            $query = mysqli_query($conn, "UPDATE users SET firstname='$firstName', middlename='$middleName', lastname='$lastName', username='$username', contact_no='$contact_no', address='$address' WHERE user_email='" . $_SESSION['login'] . "'");
        }

        if ($upload_id != '') {
            if (move_uploaded_file($upload_idTemp, $upload_idFile)) {
                $query = mysqli_query($conn, "UPDATE users SET upload_id='$upload_idFile' WHERE user_email='" . $_SESSION['login'] . "'");
                if ($query) {
                    $successmsg = "Profile Updated Successfully!";
                } else {
                    $errormsg = "Profile not updated!";
                }
            } else {
                $errormsg = "Failed to upload ID!";
            }
        } else {
            if ($query) {
                $successmsg = "Profile Updated Successfully!";
            } else {
                $errormsg = "Profile not updated!";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="shortcut icon" href="asset/images/ingat.ico">
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style>
       /* General Styling */
body {
    background-color: #f5f5f5;
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-right: -29%;
  margin-left: 25%;
}
.card-header {
    background-color: #000;
    color: white;
    border-radius: 10px 10px 0 0;
    padding: 20px;
    text-align: center;
    position: sticky;
    z-index: 10; /* Make sure it's above the background */
}

.card-body {
    padding: 20px;
    background-color: white;
    position: relative;
    z-index: 5; 
    padding-bottom: 40px;
  border-bottom-right-radius: 10px;
  border-bottom-left-radius: 10px;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 5px;
}

.form-control {
    border-radius: 5px;
}

/* Profile Photo Styling */
.profile-photo-container {
    text-align: center;
    margin-bottom: 20px;
}

.profile-photo-container img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 5px solid #000;
    transition: transform 0.3s ease;
}

.profile-info {
    text-align: center;
}

.profile-info h4 {
    font-size: 28px;
    font-weight: bold;
    margin: 0;
}

.profile-info p {
    font-size: 22px;
    margin-top: -10px;
    color: #0000008f;
}

/* Form Group Styling */
.form-row {
    gap: 20px;
}

.row {
    margin-top: 9%;
    margin-bottom: 9%;
    margin-bottom: 20px;
}

.col-md-8 h4 {
    font-size: 24px;
    font-weight: bold;
}

.col-md-8 p {
    font-size: 16px;
    margin: 5px 0;
}

/* Contact No Styling */
.contact_no-group {
    display: flex;
    align-items: center;
}

.contact_no-group .prefix {
    background-color: black;
    color: white;
    text-align: center;
    font-weight: bold;
    max-width: 60px;
    border-radius: 5px;
    padding: 0.375rem 0.75rem;
    border: none;
    flex-shrink: 0;
}

.contact_no-group .phone-number {
    border-radius: 5px;
    padding: 0.375rem 0.75rem;
    flex: 1;
    border: 1px solid #ccc;
    font-size: 14px;
}

.contact_no-group .phone-number:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    outline: none;
}

/* Button Styling */
.btn-primary, .btn-secondary {
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
}

.btn-primary {
    background-color: #007bff;
    border: none;
}

.btn-primary:hover {
    background-color: #005bb5;
}

.btn-secondary {
    background-color: #6c757d;
    border: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

/* Responsive Layout */
@media (max-width: 768px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }

    .card {
        margin-right: 1px;
        margin-left: 10px;
    }

    .form-row {
        display: flex;
        flex-direction: column;
    }

    .col-md-6 {
        width: 100%;
        margin-bottom: 15px;
    }

    .profile-photo-container img {
        width: 120px;
        height: 120px;
    }

    .profile-info h4 {
        font-size: 22px;
    }

    .profile-info p {
        font-size: 18px;
    }

    .contact_no-group {
        flex-direction: column;
    }

    .contact_no-group .prefix {
        width: 100%;
        margin-bottom: 10px;
    }

    .contact_no-group .phone-number {
        width: 100%;
    }

    .form-control {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .profile-info h4 {
        font-size: 20px;
    }

    .profile-info p {
        font-size: 16px;
    }

    .btn-primary, .btn-secondary {
        font-size: 12px;
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
                                <div class="card-body" style="font-family: 'Arial', sans-serif; font-size: 16px; color: #333;">
                                <?php if ($successmsg) { ?>
    <div class="alert alert-success alert-dismissable" id="success-alert">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <b>Success!</b> <?php echo htmlentities($successmsg); ?>
    </div>
<?php } elseif ($errormsg) { ?>
    <div class="alert alert-danger alert-dismissable" id="error-alert">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <b>Error!</b> <?php echo htmlentities($errormsg); ?>
    </div>
<?php } ?>

<script>
    setTimeout(function() {
        var successAlert = document.getElementById('success-alert');
        var errorAlert = document.getElementById('error-alert');
        if (successAlert) {
            successAlert.style.display = 'none';
        }
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
    }, 4000); // 4 seconds
</script>

                                    <?php
                                    $query = mysqli_query($conn, "SELECT * FROM users WHERE user_email='" . $_SESSION['login'] . "'");
                                    if ($row = mysqli_fetch_array($query)) {
                                    ?>
                                        <div class="profile-photo-container">
                                            <?php
                                            $user_image = !empty($row['user_image']) ? $row['user_image'] : 'uploads/profile.jpg';
                                            ?>
                                            <img src="<?php echo $user_image; ?>" alt="Profile Image">
                                        </div>
                                        <div class="profile-info">
                                            <h4 style="font-weight: bold; font-size: 28px;"><?php echo htmlentities($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?></h4>
                                            <p style="font-weight: bold; font-size: 22px; margin-top:-10px;color: #0000008f;">@<?php echo htmlentities($row['username']); ?></p>
                                        </div>
                                        <form method="post" name="profile" enctype="multipart/form-data" style="font-family: 'Arial', sans-serif; font-size: 16px; color: #333;">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="user_image">Profile Photo</label>
                                                        <input type="file" class="form-control" name="user_image">
                                                    </div>
                                                    <div class="form-group">
                                                  <label for="upload_id">Valid ID</label>
                                                  <input type="file" class="form-control" name="upload_id" accept="image/jpeg,image/png,application/pdf">
                                                   <?php if (!empty($row['upload_id'])) { ?>
                                                  <small>Current: <a href="<?php echo $row['upload_id']; ?>" target="_blank">View ID</a></small>
                                                   <?php } ?>
                                                  </div>
                                                    <div class="form-group">
                                                        <label for="firstName">First Name</label>
                                                        <input type="text" class="form-control" name="firstName" value="<?php echo htmlentities($row['firstname']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="middleName">Middle Name</label>
                                                        <input type="text" class="form-control" name="middleName" value="<?php echo htmlentities($row['middlename']); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="lastName">Last Name</label>
                                                        <input type="text" class="form-control" name="lastName" value="<?php echo htmlentities($row['lastname']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="username">Username</label>
                                                        <input type="text" class="form-control" name="username" value="<?php echo htmlentities($row['username']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contact_no">Contact No</label>
                                                        <div class="input-group contact_no-group">
                                                            <input type="text" class="form-control prefix" value="+63" disabled>
                                                            <input type="text" class="form-control phone-number" name="contact_no" placeholder="10-digit number" value="<?php echo substr(htmlentities($row['contact_no']), 0); ?>" pattern="\d{10}" maxlength="10" title="Enter 10 digits after +63" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="user_email">User Email</label>
                                                        <input type="email" name="user_email" required value="<?php echo htmlentities($row['user_email']); ?>" class="form-control" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="address">Address</label>
                                                        <textarea class="form-control" name="address" rows="3" required><?php echo htmlentities($row['address']); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
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
<?php } ?>