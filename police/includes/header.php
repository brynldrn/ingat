<?php
include("includes/config.php");

// Debugging: Check session data
if (!isset($_SESSION['police_id'])) {
    echo "Session 'police_id' not set. Session data: " . print_r($_SESSION, true);
    header("Location: index.php");
    exit();
}

$police_id = $_SESSION['police_id'];
$query = "SELECT firstname, middlename, lastname, badge_number FROM police WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $police_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$stmt->bind_result($firstname, $middlename, $lastname, $badge_number);
$stmt->fetch();
$stmt->close();

if (!$firstname) {
    echo "No police data found for ID: $police_id";
    exit();
}

$police_name = trim("$firstname $middlename $lastname");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INGAT - Police</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="shortcut icon" href="asset/images/ingat.ico">
    <style>
        .navbar {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 1rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            color: #fff !important;
            font-family: 'Segoe UI', sans-serif;
            font-weight: 600;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .navbar-brand img {
            height: 40px;
            margin-right: 12px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
        .nav-link {
            color: #e0e0e0 !important;
            font-family: 'Segoe UI', sans-serif;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: #0f4c75;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 50%;
        }
        .badge-info {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.95rem;
            backdrop-filter: blur(5px);
        }
        .logout-icon {
            color: #ff4c4c;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }
        .logout-icon:hover {
            color: #ff1a1a;
            transform: scale(1.1);
        }
        .navbar-toggler {
            border: none;
            color: #fff;
        }
        @media (max-width: 991px) {
            .navbar-nav {
                padding-top: 1rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <img src="asset/images/ingat_logo.png" alt="INGAT Logo"> INGAT | Police
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="complaint-management.php"><i class="fas fa-clipboard-list me-1"></i> Complaints</a>
                </li>
                <li class="nav-item">
                    <span class="nav-link badge-info">
                        <i class="fas fa-id-badge me-1"></i> <?= htmlentities($police_name); ?> | #<?= htmlentities($badge_number); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Logout">
                        <i class="fas fa-sign-out-alt logout-icon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>