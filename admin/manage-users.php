<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
{   
    header('location:index.php');
}
else {
    date_default_timezone_set('Asia/Kolkata');
    $currentTime = date( 'd-m-Y h:i:s A', time() );
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Manage Users</title>
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
        /* Ensure the table rows have a white background */
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
        @media (min-width: 1200px) {
            .offset-xl-3 {
                margin-left: 22%;
                margin-top: 2%;
            }
        }

        /* Modal content styling */
        .modal-content {
            background-color: #fff;
            border-radius: 0.3rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>
<body>
<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>  

<div class="row">
    <div class="col-xl-9 offset-xl-3">
        <div class="page-title-box">
            <h4 class="mb-0">Manage Users</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Menu</a></li>
                <li class="breadcrumb-item active">Manage Users</li>
            </ol>
        </div>
       
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Manage Users</h4>
            </div>
            <!-- end card-header -->

            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover mb-0 table-centered">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-1">#</th>
                                <th class="py-1">Name</th>
                                <th class="py-1">Email</th>
                                <th class="py-1">Contact</th>
                                <th class="py-1">Date</th>
                                <th class="py-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
$query = mysqli_query($conn, "SELECT id, firstName, middleName, lastName, user_email, contact_no, reg_date FROM users");
$cnt = 1;
while ($row = mysqli_fetch_array($query)) {
    $date = new DateTime($row['reg_date']);
    // Combine first, middle, and last name
    $fullName = $row['firstName'];
    if (!empty($row['middleName'])) {
        $fullName .= ' ' . $row['middleName'];
    }
    $fullName .= ' ' . $row['lastName'];
?>
                            <tr>
                                <td><?php echo htmlentities($cnt); ?></td>
                                <td><?php echo htmlentities($fullName); ?></td>
                                <td><?php echo htmlentities($row['user_email']); ?></td>
                                <td><?php echo htmlentities($row['contact_no']); ?></td>
                                <td><?php echo $date->format('m/d/Y h:i A'); ?></td>
                                <td><button class="btn btn-sm btn-primary edit-btn" data-id="<?php echo htmlentities($row['id']); ?>">Details</button></td>
                            </tr>
<?php $cnt = $cnt + 1; } ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->

    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">          
            <div class="modal-body">
                <!-- User profile content will be loaded here -->
            </div>  
    </div>
</div>


<?php include('include/footer.php'); ?>

<!-- Include jQuery and DataTables CSS and JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- Vendor Javascript -->
<script src="assets/js/vendor.min.js"></script>
<!-- App Javascript -->
<script src="assets/js/app.js"></script>
<!-- Vector Map Js -->
<script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
<script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
<script src="assets/vendor/jsvectormap/maps/world.js"></script>
<!-- Dashboard Js -->
<script src="assets/js/pages/dashboard.js"></script>

<script>
$('.edit-btn').on('click', function (event) {
    event.preventDefault();
    var userId = $(this).data('id');

    $.ajax({
        url: 'userprofile.php',
        type: 'GET',
        data: { uid: userId },
        success: function (response) {
            $('#userModal .modal-body').html(response);
            $('#userModal').modal('show');
        },
        error: function () {
            $('#userModal .modal-body').html(
                '<div class="text-center text-danger p-3">Failed to load user details. Please try again.</div>'
            );
            $('#userModal').modal('show');
        },
    });
});
</script>
</body>
</html>
<?php } ?>