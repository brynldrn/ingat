<?php
session_start();
include("includes/config.php");

if (!isset($_SESSION['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complaint_number'])) {
    $complaint_number = mysqli_real_escape_string($conn, $_POST['complaint_number']);
    $userId = mysqli_real_escape_string($conn, $_SESSION['userId']);

    if (!isset($_SESSION['anonymous_complaint_ids'])) {
        $_SESSION['anonymous_complaint_ids'] = [];
    }
    $anonymous_complaint_ids = array_map('mysqli_real_escape_string', array_fill(0, count($_SESSION['anonymous_complaint_ids']), $conn), $_SESSION['anonymous_complaint_ids']);
    $anonymous_complaint_ids_list = empty($anonymous_complaint_ids) ? "''" : "'" . implode("','", $anonymous_complaint_ids) . "'";

    $check_query = "SELECT complaint_number 
                    FROM tblcomplaints 
                    WHERE complaint_number = '$complaint_number' 
                    AND (userId = '$userId' OR (complaint_number IN ($anonymous_complaint_ids_list) AND anonymous = 1))";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE tblcomplaints 
                         SET is_read = 1 
                         WHERE complaint_number = '$complaint_number' 
                         AND (userId = '$userId' OR (complaint_number IN ($anonymous_complaint_ids_list) AND anonymous = 1))";
        if (mysqli_query($conn, $update_query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to mark notification as read']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Complaint not found or not authorized']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>