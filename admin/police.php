<?php
session_start();
include('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    date_default_timezone_set('Asia/Manila');
    $currentTime = date('d-m-Y h:i:s A', time());

    // Handle Add Police Form Submission
    if (isset($_POST['submit'])) {
        $badge_number = $_POST['badge_number'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'] ?? '';
        $lastname = $_POST['lastname'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $status = isset($_POST['status']) ? 1 : 0;

        $check_stmt = $conn->prepare("SELECT id FROM police WHERE badge_number = ?");
        $check_stmt->bind_param("s", $badge_number);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errormsg = "Badge number already in use.";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO police (badge_number, firstname, middlename, lastname, password, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssssi", $badge_number, $firstname, $middlename, $lastname, $password, $status);

            if ($stmt->execute()) {
                $successmsg = "Police account created successfully!";
            } else {
                $errormsg = "Error creating police account.";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }

    // Handle Update Police Form Submission
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $badge_number = $_POST['badge_number'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'] ?? '';
        $lastname = $_POST['lastname'];
        $status = isset($_POST['status']) ? 1 : 0;
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        $check_stmt = $conn->prepare("SELECT id FROM police WHERE badge_number = ? AND id != ?");
        $check_stmt->bind_param("si", $badge_number, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errormsg = "Badge number already in use by another officer.";
        } else {
            if ($password) {
                $stmt = $conn->prepare("
                    UPDATE police SET badge_number = ?, firstname = ?, middlename = ?, lastname = ?, password = ?, status = ?
                    WHERE id = ?
                ");
                $stmt->bind_param("sssssii", $badge_number, $firstname, $middlename, $lastname, $password, $status, $id);
            } else {
                $stmt = $conn->prepare("
                    UPDATE police SET badge_number = ?, firstname = ?, middlename = ?, lastname = ?, status = ?
                    WHERE id = ?
                ");
                $stmt->bind_param("ssssii", $badge_number, $firstname, $middlename, $lastname, $status, $id);
            }

            if ($stmt->execute()) {
                $successmsg = "Police details updated successfully!";
            } else {
                $errormsg = "Error updating police details.";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }

    // Handle Delete Police
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM police WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $successmsg = "Police officer deleted successfully!";
        } else {
            $errormsg = "Error deleting police officer.";
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Manage Police - INGAT Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="assets/images/ingat.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/table.dataTable-th.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/config.js"></script>
    <style>
        table.dataTable tbody tr {
            background-color: #fff;
        }

        table.dataTable tbody tr:nth-child(2n+1) {
            background-color:rgba(238, 237, 235, 0.03);
        }
        table.dataTable tbody tr {
            background-color:rgba(255, 255, 255, 0.07);
        }

        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_processing, .dataTables_wrapper .dataTables_paginate {
            color: inherit;
            margin-bottom: 20px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #aaa;
            border-radius: 3px;
            padding: 5px;
            background-color: #ffebcd05;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #0d0d0d26;
        }
        .modal-content {
            background-color: #fff;
            border-radius: 0.3rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .btn-add {
            background-color: #4a90e2;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
        }
        .btn-add:hover {
            background-color: #357abd;
        }
        .btn-delete {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 0.3rem 0.6rem;
            border-radius: 0.25rem;
            margin-left: 5px;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .modal-header {
            background-color: #343a40;
            color: #fff;
            border-bottom: none;
        }
        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #000;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            font-size: 1rem;
            color: black !important;
            background-color: white !important;
        }
        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
            outline: none;
        }
        .btn-primary {
            background-color: #4a90e2;
            border: none;
            padding: 0.5rem 1rem;
            color: #fff;
            border-radius: 0.25rem;
        }
        .btn-primary:hover {
            background-color: #357abd;
        }
        .alert-danger, .alert-success {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .password-container {
            position: relative;
        }
        .password-container .fa {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        .form-check-label {
            font-size: 0.875rem;
            color: #000;
        }
        #loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .col-xl-9.offset-xl-3 {
                margin-left: 0;
                padding: 10px;
            }
            .page-title-box h4 {
                font-size: 1.25rem;
            }
            .card-header h4 {
                font-size: 1.1rem;
            }
            .card {
                margin: 0;
            }
            .table-responsive {
                overflow-x: auto;
            }
            table.dataTable thead th {
                font-size: 0.85rem;
            }
            table.dataTable tbody td {
                font-size: 0.9rem;
            }
            .btn-sm {
                font-size: 0.8rem;
                padding: 0.3rem 0.6rem;
            }
            .btn-add {
                width: 100%;
                margin-bottom: 10px;
            }
            .modal-title {
                font-size: 1.1rem;
            }
            .form-label {
                font-size: 0.85rem;
            }
            .form-control {
                padding: 0.4rem;
                font-size: 0.9rem;
            }
            .btn-primary, .btn-delete {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
            .alert-danger, .alert-success {
                font-size: 0.9rem;
            }
        }

        @media (min-width: 769px) and (max-width: 991px) {
            .col-xl-9.offset-xl-3 {
                margin-left: 10%;
            }
            .card {
                margin: 0 10px;
            }
        }

        @media (min-width: 1200px) {
            .col-xl-9.offset-xl-3 {
                margin-left: 22%;
                margin-top: 2%;
            }
        }
        /* Ensure modal is centered on all screen sizes */
.modal-dialog-centered {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh; 
}

.modal-content{
    margin: auto;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

@media (max-width: 768px) {
    .modal-dialog {
        max-width: 90%; 
        margin: 1rem auto; 
    }
}
    </style>
</head>
<body>
<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>  

<div class="row">
    <div class="col-xl-9 offset-xl-3">
        <div class="page-title-box">
            <h4 class="mb-0">Manage Police</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Menu</a></li>
                <li class="breadcrumb-item active">Manage Police</li>
            </ol>
        </div>
       
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Police List</h4>
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addPoliceModal">Add Police</button>
            </div>
            <div class="card-body">
                <?php if (isset($errormsg)): ?>
                    <div class="alert alert-danger"><?= htmlentities($errormsg); ?></div>
                <?php endif; ?>
                <?php if (isset($successmsg)): ?>
                    <div class="alert alert-success"><?= htmlentities($successmsg); ?></div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="policeTable" class="table table-hover mb-0 table-centered">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-1">#</th>
                                <th class="py-1">Badge Number</th>
                                <th class="py-1">Name</th>
                                <th class="py-1">Reg Date</th>
                                <th class="py-1">Status</th>
                                <th class="py-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $query = mysqli_query($conn, "SELECT id, badge_number, firstname, middlename, lastname, reg_date, status FROM police");
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($query)) {
                            $date = new DateTime($row['reg_date']);
                            $fullName = $row['firstname'];
                            if (!empty($row['middlename'])) {
                                $fullName .= ' ' . $row['middlename'];
                            }
                            $fullName .= ' ' . $row['lastname'];
                            $statusText = $row['status'] == 1 ? 'Active' : 'Inactive';
                        ?>
                            <tr>
                                <td><?php echo htmlentities($cnt); ?></td>
                                <td><?php echo htmlentities($row['badge_number']); ?></td>
                                <td><?php echo htmlentities($fullName); ?></td>
                                <td><?php echo $date->format('m/d/Y h:i A'); ?></td>
                                <td><?php echo $statusText; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-btn" data-id="<?php echo htmlentities($row['id']); ?>">Details</button>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this officer?');">
                                        <input type="hidden" name="id" value="<?php echo htmlentities($row['id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-delete" name="delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php $cnt = $cnt + 1; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

 <!-- Edit Police Modal -->
<div class="modal fade" id="policeModal" tabindex="-1" aria-labelledby="policeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="policeModalLabel">Edit Police Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
    <!-- Add Police Modal -->
    <div class="modal fade" id="addPoliceModal" tabindex="-1" aria-labelledby="addPoliceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPoliceModalLabel">Add New Police</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="badge_number" class="form-label">Badge Number</label>
                            <input type="text" class="form-control" name="badge_number" id="badge_number" placeholder="e.g., 025684" required>
                        </div>
                        <div class="form-group">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" name="firstname" id="firstname" required>
                        </div>
                        <div class="form-group">
                            <label for="middlename" class="form-label">Middle Name (Optional)</label>
                            <input type="text" class="form-control" name="middlename" id="middlename">
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lastname" id="lastname" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-container">
                                <input type="password" class="form-control" name="password" id="password" required>
                                <i class="fa fa-eye" id="togglePassword"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="status" id="status" checked>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include('include/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="assets/js/vendor.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
<script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
<script src="assets/vendor/jsvectormap/maps/world.js"></script>
<script src="assets/js/pages/dashboard.js"></script>


<script>
$(document).ready(function() {
    $('#policeTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true
    });

    $('.edit-btn').on('click', function() {
        var policeId = $(this).data('id');
        loadPoliceDetails(policeId);
    });

    function loadPoliceDetails(policeId) {
        $('#modalBodyContent').html(`
            <div id="loading-spinner">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>Loading details...</p>
            </div>
        `);
        $('#policeModal').modal('show');
        
        $.ajax({
            url: 'police_details.php',
            type: 'GET',
            data: {id: policeId},
            success: function(response) {
                $('#modalBodyContent').html(response);
                initializePasswordToggle();
                $('#modalBodyContent form').on('submit', function(e) {
                    e.preventDefault();
                    updatePoliceDetails($(this), policeId);
                });
            },
            error: function() {
                $('#modalBodyContent').html('<div class="alert alert-danger">Error loading details.</div>');
            }
        });
    }

    function updatePoliceDetails(form, policeId) {
        var formData = form.serialize();
        $.ajax({
            url: 'police.php',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#modalBodyContent').html(`
                    <div id="loading-spinner">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Updating details...</p>
                    </div>
                `);
            },
            success: function() {
                $('#modalBodyContent').html('<div class="alert alert-success">Details updated successfully!</div>');
                setTimeout(function() { location.reload(); }, 1500);
            },
            error: function() {
                $('#modalBodyContent').html('<div class="alert alert-danger">Error updating details.</div>');
            }
        });
    }

    function initializePasswordToggle() {
        $('#edit_togglePassword').on('click', function() {
            const passwordField = $('#edit_password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });
        $('#togglePassword').on('click', function() {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });
    }

    initializePasswordToggle();
    $('#policeModal').on('hidden.bs.modal', function() {
        history.pushState(null, null, window.location.pathname);
    });
});
</script>
</body>
</html>
<?php } ?>