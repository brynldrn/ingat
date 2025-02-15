<?php
session_start();
include('include/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $query->bind_param("i", $id);
    if ($query->execute()) {
        $message = "The post has been deleted successfully.";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
    header('location: updates.php?message=' . urlencode($message));
    exit();
} else {
    header('location: updates.php');
    exit();
}
?>