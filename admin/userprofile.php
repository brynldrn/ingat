<?php
session_start();
include('include/config.php');

// Check login status
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// Validate and fetch user details
if (!isset($_GET['uid']) || !is_numeric($_GET['uid'])) {
    echo "<p>Invalid User ID.</p>";
    exit;
}

$uid = mysqli_real_escape_string($conn, $_GET['uid']);
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$uid'");

if (mysqli_num_rows($query) == 0) {
    echo "<p>User not found.</p>";
    exit;
}

$userDetails = mysqli_fetch_assoc($query);

// Format dates
$reg_date = date("F j, Y, g:i a", strtotime($userDetails['reg_date']));
$updation_date = date("F j, Y, g:i a", strtotime($userDetails['updation_date']));

// Get complaint ID (optional, for redirection)
$complaintID = isset($_GET['cid']) ? $_GET['cid'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- App icon -->
    <link rel="shortcut icon" href="assets/images/ingat.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Modal -->
    <div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userProfileModalLabel">User Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <th>ID:</th>
                                <td>
                                    <?php 
                                    if (!empty($userDetails['upload_id'])) { 
                                        echo '<img src="' . htmlentities($userDetails['upload_id']) . '" alt="Uploaded ID" style="max-width: 200px; max-height: 200px; border: 1px solid #ccc;">';
                                    } else { 
                                        echo 'No ID Uploaded';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>First Name:</th>
                                <td><?php echo htmlentities($userDetails['firstname']); ?></td>
                            </tr>
                            <tr>
                                <th>Middle Name:</th>
                                <td><?php echo htmlentities($userDetails['middlename']); ?></td>
                            </tr>
                            <tr>
                                <th>Last Name:</th>
                                <td><?php echo htmlentities($userDetails['lastname']); ?></td>
                            </tr>
                            <tr>
                                <th>Registration Date:</th>
                                <td><?php echo htmlentities($reg_date); ?></td>
                            </tr>
                            <tr>
                                <th>Email Address:</th>
                                <td><?php echo htmlentities($userDetails['user_email']); ?></td>
                            </tr>
                            <tr>
                                <th>Contact Number:</th>
                                <td><?php echo htmlentities($userDetails['contact_no']); ?></td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td><?php echo htmlentities($userDetails['address']); ?></td>
                            </tr>
                            <tr>
                                <th>Last Update:</th>
                                <td><?php echo htmlentities($updation_date); ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><?php echo $userDetails['status'] == 1 ? "Active" : "Blocked"; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print();">Print Profile</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var userProfileModal = new bootstrap.Modal(document.getElementById('userProfileModal'));
        userProfileModal.show();
        document.getElementById('userProfileModal').addEventListener('hidden.bs.modal', function () {
            const complaintID = "<?php echo $complaintID; ?>";
            if (complaintID) {
                window.location.href = "complaint-details.php?cid=" + complaintID;
            } else {
                window.location.href = window.location.href;
            }
        });
    </script>
</body>
</html>
