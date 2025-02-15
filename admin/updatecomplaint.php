<?php
session_start();
include('include/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and fetch data from POST
    $complaint_number = $_POST['complaint_number'];
    $status = $_POST['status'];
    $remark = $_POST['remark'];

    // Validate complaint number format
    if (!preg_match('/^CMP-\d{10,15}-\d{3,4}$/', $complaint_number)) {
        die("<p>Invalid Complaint Number.</p>");
    }

    // Prepare and execute the query to insert the remark
    try {
        $stmt = $conn->prepare("
            INSERT INTO complaintremark (complaint_number, status, remark, remark_date) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("sss", $complaint_number, $status, $remark);
        $stmt->execute();

        // Optionally update the complaint's status
        if ($status) {
            $stmtUpdate = $conn->prepare("
                UPDATE tblcomplaints 
                SET status = ? 
                WHERE complaint_number = ?
            ");
            $stmtUpdate->bind_param("ss", $status, $complaint_number);
            $stmtUpdate->execute();
        }

        // Redirect to the complaint details page or any success page
        header("Location: complaint-details.php?cid=" . urlencode($complaint_number));
        exit();
    } catch (Exception $e) {
        die("<p>Error updating remark: " . htmlspecialchars($e->getMessage()) . "</p>");
    }
}
?>
