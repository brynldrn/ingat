<?php
session_start();
include('include/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and fetch data from POST
    $complaint_number = $_POST['complaint_number'] ?? '';
    $police_id = $_POST['police_id'] ?? ''; // New field for police assignment
    $status = $_POST['status'] ?? '';
    $remark = $_POST['remark'] ?? '';

    // Validate inputs
    if (empty($complaint_number) || empty($status) || empty($remark)) {
        header("Location: complaint-details.php?cid=" . urlencode($complaint_number) . "&msg=All fields are required except police assignment");
        exit;
    }

    if (!preg_match('/^CMP-\d{10,15}-\d{3,4}$/', $complaint_number)) {
        header("Location: complaint-details.php?cid=" . urlencode($complaint_number) . "&msg=Invalid Complaint Number");
        exit;
    }

    try {
        $conn->begin_transaction();

        // Update tblcomplaints with status and police_id (if provided)
        if ($police_id) {
            $stmtUpdate = $conn->prepare("
                UPDATE tblcomplaints 
                SET status = ?, police_id = ?, last_updated_at = NOW()
                WHERE complaint_number = ?
            ");
            $stmtUpdate->bind_param("sis", $status, $police_id, $complaint_number);
        } else {
            $stmtUpdate = $conn->prepare("
                UPDATE tblcomplaints 
                SET status = ?, last_updated_at = NOW()
                WHERE complaint_number = ?
            ");
            $stmtUpdate->bind_param("ss", $status, $complaint_number);
        }
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Insert remark into complaintremark
        $stmt = $conn->prepare("
            INSERT INTO complaintremark (complaint_number, status, remark, remark_date) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("sss", $complaint_number, $status, $remark);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        header("Location: complaint-details.php?cid=" . urlencode($complaint_number) . "&msg=Complaint updated successfully");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: complaint-details.php?cid=" . urlencode($complaint_number) . "&msg=Error updating complaint: " . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: manage-complaints.php");
    exit;
}
?>