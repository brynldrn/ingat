<?php
session_start();
include('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $message = isset($_GET['message']) ? $_GET['message'] : ''; // Initialize message variable

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_post'])) {
        $id = intval($_POST['post_id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $details = mysqli_real_escape_string($conn, $_POST['details']);
        $imagePath = $_POST['existing_image'];

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
            $image = $_FILES['image'];
            $targetDir = "../users/complaintdocs/";
            $targetFile = $targetDir . basename($image["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $uploadOk = 1;

            // Validate image
            $check = getimagesize($image["tmp_name"]);
            if ($check === false) {
                $message = "File is not an image.";
                $uploadOk = 0;
            }

            // Remove the file size check, so there's no limitation on the image size
            // Validate image file type (only allow JPG, PNG, JPEG, and GIF)
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($image["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile;
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        }

        // Update post in the database
        $sql = "UPDATE posts SET name = ?, details = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $details, $imagePath, $id);
        if ($stmt->execute()) {
            $message = "The post has been updated successfully.";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update_post'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $details = mysqli_real_escape_string($conn, $_POST['details']);
        $image = $_FILES['image'];
        $imagePath = '';
    
        // Handle image upload
        if (isset($image) && $image['error'] != UPLOAD_ERR_NO_FILE) {
            $targetDir = "../users/complaintdocs/";
            $targetFile = $targetDir . basename($image["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $uploadOk = 1;
    
            // Validate image
            $check = getimagesize($image["tmp_name"]);
            if ($check === false) {
                $message = "File is not an image.";
                $uploadOk = 0;
            }
    
            // Validate image file type (only allow JPG, PNG, JPEG, and GIF)
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
    
            if ($uploadOk == 1) {
                if (move_uploaded_file($image["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile; // Save the dynamically generated file path
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        }
    
        // Insert post into database
        $sql = "INSERT INTO posts (name, details, image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $details, $imagePath);
        if ($stmt->execute()) {
            $message = "The post has been uploaded successfully.";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
    <title>Update</title>
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
    </style>
</head>
<body>
<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>

<div class="row">
        <div class="col-xl-9 offset-xl-3">
            <div class="page-title-box">
                <h4 class="mb-0">Publish Update</h4>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Others</a></li>
                    <li class="breadcrumb-item active">Publish Update</li>
                </ol>
            </div>
           
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Create Post</h4>
                </div>
                <!-- end card-header -->
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-info"> <?php echo $message; ?> </div>
                    <?php endif; ?>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Title</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="details" class="form-label">Details</label>
                            <textarea id="details" name="details" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">All Posts</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="announcementsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Details</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM posts");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlentities($row['name']) . "</td>";
                                    echo "<td>" . htmlentities($row['details']) . "</td>";
                                    echo "<td><img src='" . htmlentities($row['image']) . "' alt='Post Image' style='width: 100px;'></td>";
                                    echo "<td><button class='btn btn-sm btn-primary edit-btn' data-id='" . htmlentities($row['id']) . "' data-name='" . htmlentities($row['name']) . "' data-details='" . htmlentities($row['details']) . "' data-image='" . htmlentities($row['image']) . "'>Edit</button> <a href='delete.php?id=" . htmlentities($row['id']) . "' class='btn btn-sm btn-danger'>Delete</a></td>";
                                    echo "</tr>";
                                }
                                ?>
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
                <h5 class="modal-title" id="editModalLabel">Edit Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="post_id" name="post_id">
                    <input type="hidden" id="existing_image" name="existing_image">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Title</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_details" class="form-label">Details</label>
                        <textarea id="edit_details" name="details" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Image</label>
                        <input type="file" id="edit_image" name="image" class="form-control" accept="image/*">
                        <img id="current_image" src="" alt="Post Image" style="width: 100px; margin-top: 10px;">
                    </div>
                    <button type="submit" name="update_post" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="assets/js/vendor.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
    $(document).ready(function () {
        $('#announcementsTable').DataTable();

        // Edit button click event
        $('.edit-btn').on('click', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var details = $(this).data('details');
            var image = $(this).data('image');

            $('#post_id').val(id);
            $('#edit_name').val(name);
            $('#edit_details').val(details);
            $('#existing_image').val(image);
            $('#current_image').attr('src', image);

            $('#editModal').modal('show');
        });
    });
</script>
</body>
</html>
