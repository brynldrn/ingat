<?php
session_start();
include('include/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $message = isset($_GET['message']) ? $_GET['message'] : ''; // Initialize message variable

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
        $weapon_type = mysqli_real_escape_string($conn, $_POST['weapon_type']);
        $details = mysqli_real_escape_string($conn, $_POST['details']);
        
        $stmt = $conn->prepare("INSERT INTO weapons (weapon_type, details) VALUES (?, ?)");
        $stmt->bind_param("ss", $weapon_type, $details);
        if ($stmt->execute()) {
            $message = "Weapon Created!";
        } else {
            $message = "Error: Could not create weapon.";
        }
        $stmt->close();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
        $id = intval($_POST['id']);
        $weapon_type = mysqli_real_escape_string($conn, $_POST['weapon_type']);
        $details = mysqli_real_escape_string($conn, $_POST['details']);
        
        $stmt = $conn->prepare("UPDATE weapons SET weapon_type = ?, details = ? WHERE id = ?");
        $stmt->bind_param("ssi", $weapon_type, $details, $id);
        if ($stmt->execute()) {
            $message = "Weapon Updated!";
        } else {
            $message = "Error: Could not update weapon.";
        }
        $stmt->close();
    }

    if (isset($_GET['del'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM weapons WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Weapon deleted!";
        } else {
            $message = "Error: Could not delete weapon.";
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Manage Weapons</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <!-- App icon -->
    <link rel="shortcut icon" href="assets/images/ingat.ico">
    <!-- Google Font Family link -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&amp;display=swap" rel="stylesheet">
    <!-- Vendor css -->
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="assets/css/style.min.css" rel="stylesheet" type="text/css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- Custom DataTables CSS -->
    <link rel="stylesheet" href="assets/css/table.dataTable-th.css">
    <!-- Theme Config js -->
    <script src="assets/js/config.js"></script>
    <style>
        table.dataTable tbody tr {
            background-color: #eaeaea00;
        }

        table.dataTable tbody tr:nth-child(2n+1) {
            background-color: rgba(238, 237, 235, 0.03);
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

        @media (min-width: 1200px) {
            .offset-xl-3 {
                margin-left: 22%;
                margin-top: 2%;
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
            <h4 class="mb-0">Manage Weapons</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Others</a></li>
                <li class="breadcrumb-item active">Types of Weapon</li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Add Weapon</h4>
            </div>
            <!-- end card-header -->
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-info"> <?php echo $message; ?> </div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="weapon_type" class="form-label">Weapon Type</label>
                        <input type="text" id="weapon_type" name="weapon_type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="details" class="form-label">Details</label>
                        <textarea id="details" name="details" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">All Weapons</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="weaponsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Weapon Type</th>
                                <th>Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $query = mysqli_query($conn, "SELECT * FROM weapons");
                            $cnt = 1;
                            while($row = mysqli_fetch_array($query)) {
                            ?>
                            <tr>
                                <td><?php echo htmlentities($cnt); ?></td>
                                <td><?php echo htmlentities($row['weapon_type']); ?></td>
                                <td><?php echo htmlentities($row['details']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $row['id']; ?>" data-weapon_type="<?php echo $row['weapon_type']; ?>" data-details="<?php echo $row['details']; ?>">Edit</button>
                                    <a href="weapons.php?id=<?php echo $row['id']?>&del=delete" onClick="return confirm('Are you sure you want to delete?')">
                                        <button type="button" class="btn btn-danger btn-sm">Delete</button>
                                    </a>
                                </td>
                            </tr>
                            <?php $cnt = $cnt + 1; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Weapon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_weapon_type" class="form-label">Weapon Type</label>
                        <input type="text" id="edit_weapon_type" name="weapon_type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_details" class="form-label">Details</label>
                        <textarea id="edit_details" name="details" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>

<?php include('include/footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/vendor.min.js"></script>
<script src="assets/js/app.js"></script>

<script>
    $(document).ready(function() {
        $('#weaponsTable').DataTable();

        // Edit button click event
        $('.edit-btn').on('click', function () {
            var id = $(this).data('id');
            var weapon_type = $(this).data('weapon_type');
            var details = $(this).data('details');

            $('#edit_id').val(id);
            $('#edit_weapon_type').val(weapon_type);
            $('#edit_details').val(details);

            $('#editModal').modal('show');
        });
    });
</script>
</body>
</html>
<?php } ?>