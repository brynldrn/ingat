<?php 
session_start();
include "plugins-header.php";
include('includes/config.php'); // Assuming this contains your database connection

// Check if user is logged in
if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit;
}

date_default_timezone_set('Asia/Manila');
$currentTime = date('d-m-Y h:i:s A', time());

// Fetch existing user data if it exists
$email = mysqli_real_escape_string($conn, $_SESSION['login']);
$query = mysqli_query($conn, "SELECT * FROM users WHERE user_email='$email'");
$userExists = mysqli_num_rows($query) > 0;
$row = $userExists ? mysqli_fetch_array($query) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save-changes'])) {
    // Sanitize inputs
    $firstName = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
    $middleName = filter_input(INPUT_POST, 'middlename', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
    $suffix = filter_input(INPUT_POST, 'suffix', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    // Handle file upload
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $user_image = $userExists && !empty($row['user_image']) ? $row['user_image'] : '';
    if (!empty($_FILES['profile_img']['name'])) {
        $user_image = $uploadDir . basename($_FILES['profile_img']['name']);
        $user_imageTemp = $_FILES['profile_img']['tmp_name'];
        if (!move_uploaded_file($user_imageTemp, $user_image)) {
            $errormsg = "Failed to upload profile photo!";
            $user_image = $userExists && !empty($row['user_image']) ? $row['user_image'] : '';
        }
    }

    if ($userExists) {
        // Update existing record
        $updateQuery = "UPDATE users SET 
            firstname = '$firstName',
            middlename = '$middleName',
            lastname = '$lastName',
            suffix = '$suffix',
            username = '$username',
            contact_no = '$contact_no',
            address = '$address',
            user_image = '$user_image',
            updation_date = '$currentTime'
            WHERE user_email = '$email'";
        
        if (mysqli_query($conn, $updateQuery)) {
            $successmsg = "Profile Updated Successfully!";
        } else {
            $errormsg = "Error updating profile: " . mysqli_error($conn);
        }
    } else {
        // Insert new record
        $insertQuery = "INSERT INTO users (
            firstname, middlename, lastname, suffix, username, 
            contact_no, address, user_email, user_image, creation_date
        ) VALUES (
            '$firstName', '$middleName', '$lastName', '$suffix', '$username',
            '$contact_no', '$address', '$email', '$user_image', '$currentTime'
        )";
        
        if (mysqli_query($conn, $insertQuery)) {
            $successmsg = "Profile Created Successfully!";
        } else {
            $errormsg = "Error creating profile: " . mysqli_error($conn);
        }
    }
    
    // Refresh user data after save
    $query = mysqli_query($conn, "SELECT * FROM users WHERE user_email='$email'");
    $row = mysqli_fetch_array($query);
    $userExists = true;
}
?>

<style>
    /* ... Your existing styles remain unchanged ... */
    .disabled-btn {
        pointer-events: none;
        opacity: 0.6;
    }
</style>

<body>
    <?php include "header.php"; ?>
    <div class="container-fluid px-1 py-4">
        <div class="row row-gap-3 mx-0" style="max-height: calc(100vh - 145px);">
            <div class="col-12 d-flex flex-column flex-lg-row align-items-start justify-content-between row-gap-3">
                <div>
                    <h4 class="fw-bold">Edit Profile</h4>
                    <p class="m-0">Update your personal information</p>
                </div>
            </div>

            <div class="col-12">
                <form id="profile-form" method="post" action="" enctype="multipart/form-data" class="rounded-2 shadow-sm bg-white">
                    <div class="position-relative rounded-top-2" style="background-color: var(--blue); height: 170px;">
                        <div id="change-image" class="position-absolute" style="top: 80px; left: 30px;">
                            <img src="<?php echo $userExists && !empty($row['user_image']) ? htmlspecialchars($row['user_image']) : '../img/3.png'; ?>" 
                                 width="150" height="auto" class="bg-white border rounded-circle" id="profile-preview">
                        </div>
                    </div>
                    <div class="py-4 px-lg-3 mt-5">
                        <div class="mt-4 row mx-0">
                            <div class="col-12 col-lg-6 row mx-0 row-gap-4 mb-3 mb-lg-0">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input required type="text" name="firstname" id="firstname" class="form-control rounded-1" 
                                               placeholder="input firstname" value="<?php echo htmlspecialchars($row['firstname'] ?? ''); ?>">
                                        <label for="firstname">First Name</label>
                                    </div>
                                </div>
                                <!-- ... Other form fields remain similar, just add value attributes ... -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input required type="text" name="middlename" id="middlename" class="form-control rounded-1" 
                                               placeholder="input middlename" value="<?php echo htmlspecialchars($row['middlename'] ?? ''); ?>">
                                        <label for="middlename">Middle Name</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input required type="text" name="lastname" id="lastname" class="form-control rounded-1" 
                                               placeholder="input lastname" value="<?php echo htmlspecialchars($row['lastname'] ?? ''); ?>">
                                        <label for="lastname">Last Name</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="suffix" id="suffix" class="form-control rounded-1" 
                                               placeholder="input suffix" value="<?php echo htmlspecialchars($row['suffix'] ?? ''); ?>">
                                        <label for="suffix">Suffix Name</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6 row mx-0 row-gap-4">
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">@</span>
                                        <div class="form-floating">
                                            <input required type="text" name="username" id="username" class="form-control rounded-1" 
                                                   placeholder="input username" value="<?php echo htmlspecialchars($row['username'] ?? ''); ?>">
                                            <label for="username">Username</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- ... Other fields ... -->
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">+63</span>
                                        <div class="form-floating">
                                            <input required type="number" name="contact_no" id="contact_no" class="form-control rounded-1" 
                                                   placeholder="input contact_no" value="<?php echo htmlspecialchars($row['contact_no'] ?? ''); ?>">
                                            <label for="contact_no">Contact No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input required type="email" name="email" id="email" class="form-control rounded-1" 
                                               placeholder="input email" value="<?php echo htmlspecialchars($row['user_email'] ?? $_SESSION['login']); ?>" 
                                               <?php echo $userExists ? 'readonly' : ''; ?>>
                                        <label for="email">Email Address</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="address" id="address" class="form-control rounded-1" 
                                               placeholder="input address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                        <label for="address">Address</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 pb-4 mt-4">
                                <div class="d-flex flex-column row-gap-3 rounded-2 shadow-sm bg-white p-4">
                                    <strong>Profile Photo</strong>
                                    <div class="d-flex flex-column flex-lg-row align-items-center column-gap-3">
                                        <div>
                                            <img src="<?php echo $userExists && !empty($row['user_image']) ? htmlspecialchars($row['user_image']) : '../img/3.png'; ?>" 
                                                 width="100" height="auto" class="bg-white rounded-circle border" id="profile-preview-small">
                                        </div>
                                        <div>
                                            <p class="text-center">Upload a new profile photo. Recommended size is 400x400px.</p>
                                            <div class="d-flex align-items-center justify-content-center justify-content-lg-start column-gap-2">
                                                <label for="profile_img" class="upload-label px-2 py-1">
                                                    <i class="ri-camera-line"></i>
                                                    <span>Upload New</span>
                                                </label>
                                                <input type="file" name="profile_img" id="profile_img" accept="image/*">
                                                <button type="button" class="btn btn-outline-danger btn-sm" id="remove-image">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" id="cancel-btn" class="btn">Cancel</button>
                                    <button type="submit" id="save-changes" name="save-changes" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "plugins-footer.php"; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profile-form');
        const saveChangesBtn = document.getElementById('save-changes');
        const cancelBtn = document.getElementById('cancel-btn');
        const profileImg = document.getElementById('profile_img');
        const profilePreview = document.getElementById('profile-preview');
        const profilePreviewSmall = document.getElementById('profile-preview-small');
        const removeImageBtn = document.getElementById('remove-image');

        // Store original values
        const originalValues = {};
        form.querySelectorAll('input').forEach(input => {
            originalValues[input.id] = input.value;
        });
        originalValues['profile_img'] = profilePreview.src;

        function checkForChanges() {
            let hasChanges = false;
            form.querySelectorAll('input').forEach(input => {
                if (input.value !== originalValues[input.id] && !input.readOnly) {
                    hasChanges = true;
                }
            });
            if (profileImg.files.length > 0 || profilePreview.src !== originalValues['profile_img']) {
                hasChanges = true;
            }
            saveChangesBtn.disabled = !hasChanges;
            saveChangesBtn.classList.toggle('disabled-btn', !hasChanges);
        }

        form.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', checkForChanges);
        });

        profileImg.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                    profilePreviewSmall.src = e.target.result;
                    checkForChanges();
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        removeImageBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove your profile photo?')) {
                profilePreview.src = '../img/3.png';
                profilePreviewSmall.src = '../img/3.png';
                profileImg.value = '';
                checkForChanges();
            }
        });

        cancelBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel? Changes will be discarded.')) {
                form.querySelectorAll('input').forEach(input => {
                    input.value = originalValues[input.id];
                });
                profilePreview.src = originalValues['profile_img'];
                profilePreviewSmall.src = originalValues['profile_img'];
                profileImg.value = '';
                checkForChanges();
            }
        });

        checkForChanges();
    });
    </script>
</body>
<?php mysqli_close($conn); ?>