<?php
session_start();
include "includes/config.php"; // Database connection

if (!isset($_SESSION['police_id'])) {
    header("Location: index.php");
    exit();
}

// Get complaint number from URL
$cid = $_GET['cid'] ?? null;
if (!$cid) {
    die("No complaint number provided.");
}

// Fetch complaint details to ensure it exists and belongs to the officer
$stmt = $conn->prepare("SELECT complaint_number, status FROM tblcomplaints WHERE complaint_number = ? AND police_id = ?");
$stmt->bind_param("si", $cid, $_SESSION['police_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Complaint not found or you don't have permission to access it.");
}
$complaint = $result->fetch_assoc();
$stmt->close();

// Handle form submission
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $officer_name = $_POST['officername'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $status = $_POST['status'] ?? '';
    $police_id = $_SESSION['police_id'];

    // Validate inputs
    if (empty($officer_name) || empty($notes) || empty($status)) {
        $error = "All fields are required.";
    } else {
        // Handle file upload
        $upload_dir = __DIR__ . '/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $uploaded_file = '';
        if (isset($_FILES['photos_actions']) && $_FILES['photos_actions']['error'] === UPLOAD_ERR_OK) {
            $file_name = basename($_FILES['photos_actions']['name']);
            $file_path = $upload_dir . time() . '_' . $file_name;
            if (move_uploaded_file($_FILES['photos_actions']['tmp_name'], $file_path)) {
                $uploaded_file = basename($file_path); // Store filename only
            } else {
                $error = "Failed to upload file.";
            }
        }

        if (!$error) {
            // Start transaction
            $conn->begin_transaction();
            try {
                // Update tblcomplaints
                $update_stmt = $conn->prepare("UPDATE tblcomplaints SET status = ?, last_updated_at = NOW() WHERE complaint_number = ?");
                $update_stmt->bind_param("ss", $status, $cid);
                $update_stmt->execute();
                $update_stmt->close();

                // Insert into complaintremark
                $remark_stmt = $conn->prepare("INSERT INTO complaintremark (complaint_number, status, remark, remark_date) VALUES (?, ?, ?, NOW())");
                $remark = "Officer: $officer_name\nAction: $notes";
                $remark_stmt->bind_param("sss", $cid, $status, $remark);
                $remark_stmt->execute();
                $remark_id = $conn->insert_id; // Get the ID of the inserted remark
                $remark_stmt->close();

                // Insert into complaint_action_files if file uploaded
                if ($uploaded_file) {
                    $file_stmt = $conn->prepare("INSERT INTO complaint_action_files (complaint_number, remark_id, police_id, file_path) VALUES (?, ?, ?, ?)");
                    $file_stmt->bind_param("siis", $cid, $remark_id, $police_id, $uploaded_file);
                    $file_stmt->execute();
                    $file_stmt->close();
                }

                // Commit transaction
                $conn->commit();
                $success = "Action taken successfully.";
                header("Location: complaint-management.php?success=" . urlencode($success));
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<style>
    .upload-box {
        width: 100%;
        height: 150px;
        border: 2px dashed #ccc;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: #f9f9f9;
        cursor: pointer;
        transition: 0.3s;
    }
    .upload-box:hover {
        background-color: #f0f0f0;
    }
    .upload-box i {
        font-size: 32px;
        color: #666;
        margin-bottom: 8px;
    }
    .upload-box p {
        margin: 0;
        font-size: 14px;
        color: #555;
    }
    .upload-box small {
        font-size: 12px;
        color: #888;
    }
    .upload-box input {
        display: none;
    }
    .error {
        color: red;
        font-size: 0.9em;
    }
    .success {
        color: green;
        font-size: 0.9em;
    }
</style>
<!DOCTYPE html>
<html lang="en">
<link rel="shortcut icon" href="asset/images/ingat.ico">
<body>
	<?php include "includes/header.php"; ?>
    <div class="container px-1 py-4">
        <div class="row row-gap-3 column-gap-2 mx-0 flex-wrap-reverse">
            <div class="col-12 row row-gap-4 column-gap-2 mx-0" style="max-height: calc(100vh - 120px);">
                <a href="complaint-management.php" class="text-decoration-none text-dark mb-0"><i class="ri-arrow-left-double-line"></i> Back to Complaint</a>
                <div class="col-12 col-lg-11">
                    <div>
                        <h4 class="fw-bold">Take Action</h4>
                        <p class="text-secondary">Update status and add details for complaint <?php echo htmlspecialchars($complaint['complaint_number']); ?></p>
                    </div>
                </div>

                <div class="col-12 row mx-0 justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="bg-white p-4 shadow-sm border rounded-2 mb-4">
                            <?php if ($error): ?>
                                <p class="error"><?php echo htmlspecialchars($error); ?></p>
                            <?php endif; ?>
                            <?php if ($success): ?>
                                <p class="success"><?php echo htmlspecialchars($success); ?></p>
                            <?php endif; ?>
                            <form class="row mx-0 row-gap-3" method="POST" enctype="multipart/form-data">
                                <div class="col-12">
                                    <strong class="d-block m-0">Action Report</strong>
                                    <small class="text-secondary">Document the actions taken and update the complaint status</small>
                                </div>

                                <div>
                                    <label for="officername" class="form-label">Officer Name</label>
                                    <div class="form-floating">
                                        <input required type="text" name="officername" id="officername" class="form-control rounded-1" placeholder="Enter your name" value="<?php echo htmlspecialchars($_SESSION['displayName'] ?? ''); ?>">
                                        <label for="officername">Enter your name</label>
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="form-label">Action Notes</label>
                                    <div class="form-floating">
                                        <textarea required name="notes" id="notes" class="form-control rounded-1" placeholder="Describe the actions taken..." style="height: 100px;"></textarea>
                                        <label for="notes" class="text-wrap">Describe the actions taken...</label>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Case Status</label>
                                    <div class="form-check">
                                        <input class="form-check-input border-dark" type="radio" value="In Progress" name="status" id="In_Progress" <?php echo $complaint['status'] === 'In Progress' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="In_Progress">In Progress</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input border-dark" type="radio" value="Solved" name="status" id="Solved" <?php echo $complaint['status'] === 'Solved' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="Solved">Solved</label>
                                    </div>
                                </div>

                                <div>
                                    <div class="d-flex justify-content-center">
                                        <label class="upload-box">
                                            <i class="ri-camera-line"></i>
                                            <p>Drag photos here or click to upload</p>
                                            <input type="file" name="photos_actions" accept="image/*">
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="complaint-management.php" class="btn btn-outline-dark">Cancel</a>
                                    <button type="submit" class="btn btn-dark">Submit Report</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "plugins-footer.php"; ?>
</body>