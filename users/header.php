<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("includes/config.php");

// Redirect if user is not logged in
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

// Fetch user ID if not set in session
if (!isset($_SESSION['userId'])) {
    $email = mysqli_real_escape_string($conn, $_SESSION['login']);
    $user_query = "SELECT id FROM users WHERE user_email = '$email' LIMIT 1";
    $user_result = mysqli_query($conn, $user_query);
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user = mysqli_fetch_assoc($user_result);
        $_SESSION['userId'] = $user['id'];
    } else {
        header("Location: logout.php");
        exit();
    }
}

// Time difference calculation function
function facebook_time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);     
    $hours = round($seconds / 3600);      
    $days = round($seconds / 86400);         
    $weeks = round($seconds / 604800);      
    $months = round($seconds / 2629440);    
    $years = round($seconds / 31553280);    

    if ($seconds <= 60) {
        return "Just Now";
    } elseif ($minutes <= 60) {
        return $minutes == 1 ? "one minute ago" : "$minutes minutes ago";
    } elseif ($hours <= 24) {
        return $hours == 1 ? "an hour ago" : "$hours hrs ago";
    } elseif ($days <= 7) {
        return $days == 1 ? "yesterday" : "$days days ago";
    } elseif ($weeks <= 4.3) {
        return $weeks == 1 ? "a week ago" : "$weeks weeks ago";
    } elseif ($months <= 12) {
        return $months == 1 ? "a month ago" : "$months months ago";
    } else {
        return $years == 1 ? "one year ago" : "$years years ago";
    }
}

// Handle search query
$search_placeholder = "Search reports...";
$search_value = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_query'])) {
    $search_query = mysqli_real_escape_string($conn, trim($_POST['search_query']));

    $post_query = "SELECT * FROM posts WHERE name LIKE '%$search_query%' OR details LIKE '%$search_query%' OR upload_date LIKE '%$search_query%'";
    $post_result = mysqli_query($conn, $post_query);

    $complaint_query = "SELECT * FROM tblcomplaints WHERE complaint_number LIKE '%$search_query%'";
    $complaint_result = mysqli_query($conn, $complaint_query);

    if (mysqli_num_rows($post_result) > 0) {
        header("Location: dashboard.php?search=" . urlencode($search_query));
        exit();
    } elseif (mysqli_num_rows($complaint_result) > 0) {
        header("Location: status.php?complaint_number=" . urlencode($search_query));
        exit();
    } else {
        $search_value = $search_query;
        $search_placeholder = "No similar content found";
    }
}

// Fetch user image
$userId = mysqli_real_escape_string($conn, $_SESSION['userId']);
$user_query = "SELECT user_image FROM users WHERE id = '$userId'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$user_image = $user_data['user_image'] ?? '../img/3.png'; 

// Fetch recent complaints
if (!isset($_SESSION['anonymous_complaint_ids'])) {
    $_SESSION['anonymous_complaint_ids'] = [];
}
$anonymous_complaint_ids = array_map('mysqli_real_escape_string', array_fill(0, count($_SESSION['anonymous_complaint_ids']), $conn), $_SESSION['anonymous_complaint_ids']);
$anonymous_complaint_ids_list = empty($anonymous_complaint_ids) ? "''" : "'" . implode("','", $anonymous_complaint_ids) . "'";

$recent_complaints_query = "SELECT complaint_number, status, registered_at, last_updated_at, is_read 
                           FROM tblcomplaints 
                           WHERE (userId = '$userId' OR (complaint_number IN ($anonymous_complaint_ids_list) AND anonymous = 1))
                           ORDER BY registered_at DESC 
                           LIMIT 3";
$recent_complaints_result = mysqli_query($conn, $recent_complaints_query);
$recent_complaints = mysqli_fetch_all($recent_complaints_result, MYSQLI_ASSOC);

// Fetch latest complaint
$latest_complaint_number = isset($_SESSION['latest_complaint_number']) ? $_SESSION['latest_complaint_number'] : null;
if ($latest_complaint_number) {
    $latest_query = "SELECT complaint_number, status, registered_at, last_updated_at, is_read 
                     FROM tblcomplaints 
                     WHERE complaint_number = '$latest_complaint_number' 
                     LIMIT 1";
    $latest_result = mysqli_query($conn, $latest_query);
    $latest_complaint = mysqli_fetch_assoc($latest_result);
    if ($latest_complaint) {
        $recent_complaints[] = $latest_complaint; 
    }
    unset($_SESSION['latest_complaint_number']); 
}

// Prepare notifications
$notification_count = 0;
$notification_messages = [];
$processed_complaints = []; 
foreach ($recent_complaints as $complaint) {
    $complaint_number = $complaint['complaint_number'];
    if (in_array($complaint_number, $processed_complaints)) continue; 
    $processed_complaints[] = $complaint_number;

    if ($complaint['is_read']) continue;

    $status = $complaint['status'] ?? 'Null';
    $updated_at = $status === 'Null' ? $complaint['registered_at'] : $complaint['last_updated_at'];
    $time_ago = facebook_time_ago($updated_at);

    $message = '';
    switch ($status) {
        case 'Null':
            $message = "Your complaint <a href='status.php?complaint_number=" . urlencode($complaint_number) . "' class='complaint-link' data-complaint-number='$complaint_number'>#$complaint_number</a> has been successfully submitted.";
            break;
        case 'In Progress':
            $message = "Complaint <a href='status.php?complaint_number=" . urlencode($complaint_number) . "' class='complaint-link' data-complaint-number='$complaint_number'>#$complaint_number</a> is in progress. Rescue is on the way or action is being taken.";
            break;
        case 'Solved':
            $message = "Complaint <a href='status.php?complaint_number=" . urlencode($complaint_number) . "' class='complaint-link' data-complaint-number='$complaint_number'>#$complaint_number</a> has been solved. The issue has been resolved successfully!";
            break;
    }

    if ($message) {
        $notification_messages[] = [
            'message' => $message,
            'time_ago' => $time_ago,
            'complaint_number' => $complaint_number
        ];
        $notification_count++;
    }
}
?>

<style>
    .notification-dropdown .dropdown-menu {
        width: 300px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
    }
    .notification-header h6 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }
    .notification-header a {
        font-size: 14px;
        color: #007bff;
        text-decoration: none;
    }
    .notification-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
    }
    .notification-item img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .notification-item .notification-content {
        flex: 1;
    }
    .notification-item .notification-content p {
        margin: 0;
        font-size: 14px;
    }
    .notification-item .notification-content small {
        color: #6c757d;
        font-size: 12px;
    }
    .notification-footer {
        text-align: center;
        padding: 10px;
    }
    .notification-footer a {
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
    }
    .complaint-link {
        color: #007bff;
        text-decoration: none;
    }
    .complaint-link:hover {
        text-decoration: none;
    }
    
    .header-container {
        position: relative;
        width: 100%;
    }
    
    .mobile-top-header {
        display: none;
    }
    
    .mobile-bottom-nav {
        display: none;
    }
    
    @media (max-width: 991px) {
        .desktop-header {
            display: none;
        }
        
        .mobile-top-header {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
            background: white;
            padding: 10px;
            justify-content: space-between;
            align-items: center;
        }
        
        .mobile-bottom-nav {
            display: block;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
        }
        
        body {
            padding-top: 65px;
            padding-bottom: 65px;
        }
        
        .navbar-nav {
            flex-direction: row !important;
            justify-content: space-around !important;
            width: 100%;
        }
        
        .mobile-search {
            display: none;
        }
    }
    
    @media (min-width: 992px) {
        .mobile-top-header {
            display: none;
        }
        .mobile-bottom-nav {
            display: none;
        }
        .desktop-header {
            display: block;
        }
        .mobile-search {
            display: flex;
        }
    }
</style>

<!-- Mobile Top Header -->
<div class="mobile-top-header shadow-sm bg-white">
    <div>
        <img src="../img/logo3.png" width="44px" height="auto">
    </div>
    <div class="d-flex align-items-center column-gap-2">
        <div class="position-relative" style="height: 25px; width: 25px;">
            <a href="#" class="text-dark text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="badge text-bg-danger rounded-circle p-1 position-absolute" style="top: -4px; right: 0; font-size: 11px;">
                    <?php echo $notification_count > 0 ? $notification_count : ''; ?>
                </span>
                <i class="ri-notification-line fs-5" style="height: 100%; width: 100%;"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                <li class="notification-header">
                    <h6>Activity Center</h6>
                </li>
                <?php if ($notification_count > 0): ?>
                    <?php foreach ($notification_messages as $notif): ?>
                        <li class="notification-item" data-complaint-number="<?php echo htmlspecialchars($notif['complaint_number']); ?>">
                            <div class="notification-content">
                                <p><?php echo $notif['message']; ?></p>
                                <small><?php echo htmlspecialchars($notif['time_ago']); ?></small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="notification-item">
                        <div class="notification-content">
                            <p>No new notifications</p>
                        </div>
                    </li>
                <?php endif; ?>
                <li class="notification-footer">
                    <a href="status.php">See All</a>
                </li>
            </ul>
        </div>
        <div class="dropdown">
            <div id="header-profile" class="btn btn-outline-secondary d-flex align-items-center border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?= htmlspecialchars($user_image) ?>" alt="Profile" width="25" height="25" class="rounded-circle me-2" onerror="this.src='../img/3.png';">
                <div class="d-flex align-items-center">
                    <span class="text-dark text-nowrap"><?= htmlentities($_SESSION['displayName']); ?></span>
                    <i class="ri-arrow-drop-down-line text-dark fs-4"></i>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end" style="width: 14rem;">
                <li>
                    <a class="dropdown-item" href="profile.php">
                        <i class="ri-user-3-line"></i> Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="logout.php">
                        <i class="ri-logout-circle-r-line"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav shadow-sm bg-white">
    <div class="container-fluid px-1">
        <div class="row mx-0 w-100 align-items-center" style="min-height: 65px;">
            <div class="col-12">
                <ul class="navbar-nav navbar-text-color flex-row align-items-center justify-content-between column-gap-4">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link d-flex flex-column text-center column-gap-1 px-2 rounded-2">
                            <i class="ri-home-4-line fs-5"></i>
                            <small>Home</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="register-complaint.php" class="nav-link d-flex flex-column text-center column-gap-1 px-2 rounded-2">
                            <i class="ri-shield-line fs-5"></i>
                            <small>Report</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="status.php" class="nav-link d-flex flex-column text-center column-gap-1 px-2 rounded-2">
                            <i class="ri-pie-chart-line fs-5"></i>
                            <small>Status</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Desktop Header (Original Layout) -->
<div class="header-container desktop-header shadow-sm bg-white">
    <div class="container-fluid px-1">
        <div class="row mx-0 w-100 align-items-center" style="min-height: 65px;">
            <div class="col-12 col-lg-auto d-flex align-items-center justify-content-between pt-2 pt-lg-0">
                <div>
                    <img src="../img/logo3.png" width="44px" height="auto">
                </div>
            </div>
            <hr class="m-0 mt-2 text-secondary d-lg-none">
            <div class="col-12 col-lg order-3 order-lg-2">
                <ul class="navbar-nav navbar-text-color flex-row align-items-center justify-content-between justify-content-lg-center column-gap-4">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link d-flex flex-column flex-lg-row text-center column-gap-1 px-2 rounded-2">
                            <div class="d-lg-none d-flex flex-column">
                                <i class="ri-home-4-line fs-5"></i>
                                <small>Home</small>
                            </div>
                            <div class="d-lg-block d-none">
                                <i class="ri-home-4-line"></i>
                                <span>Home</span>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="register-complaint.php" class="nav-link d-flex flex-column flex-lg-row text-center column-gap-1 px-2 rounded-2">
                            <div class="d-lg-none d-flex flex-column">
                                <i class="ri-shield-line fs-5"></i>
                                <small>Report</small>
                            </div>
                            <div class="d-lg-block d-none">
                                <i class="ri-shield-line"></i>
                                <span>Report</span>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="status.php" class="nav-link d-flex flex-column flex-lg-row text-center column-gap-1 px-2 rounded-2">
                            <div class="d-lg-none d-flex flex-column">
                                <i class="ri-pie-chart-line fs-5"></i>
                                <small>Status</small>
                            </div>
                            <div class="d-lg-block d-none">
                                <i class="ri-pie-chart-line"></i>
                                <span>Status</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-lg-auto order-2 order-lg-3 d-flex align-items-center column-gap-5 mt-3 mt-lg-0">
                <div class="w-100 mobile-search">
                    <form method="POST" action="">
                        <div class="input-group" style="width: 100%;">
                            <span class="input-group-text border-end-0" id="basic-addon1">
                                <i class="ri-search-line"></i>
                            </span>
                            <input type="text" name="search_query" class="form-control border-start-0" 
                                   placeholder="<?php echo $search_placeholder; ?>" 
                                   value="<?php echo htmlspecialchars($search_value); ?>" 
                                   aria-label="Search" aria-describedby="basic-addon1" required>
                        </div>
                    </form>
                </div>
                <div class="d-none d-lg-flex">
                    <div class="position-relative" style="height: 25px; width: 25px;">
                        <a href="#" class="text-dark text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="badge text-bg-danger rounded-circle p-1 position-absolute" style="top: -4px; right: 0; font-size: 11px;">
                                <?php echo $notification_count > 0 ? $notification_count : ''; ?>
                            </span>
                            <i class="ri-notification-line fs-5" style="height: 100%; width: 100%;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                            <li class="notification-header">
                                <h6>Activity Center</h6>
                            </li>
                            <?php if ($notification_count > 0): ?>
                                <?php foreach ($notification_messages as $notif): ?>
                                    <li class="notification-item" data-complaint-number="<?php echo htmlspecialchars($notif['complaint_number']); ?>">
                                        <div class="notification-content">
                                            <p><?php echo $notif['message']; ?></p>
                                            <small><?php echo htmlspecialchars($notif['time_ago']); ?></small>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="notification-item">
                                    <div class="notification-content">
                                        <p>No new notifications</p>
                                    </div>
                                </li>
                            <?php endif; ?>
                            <li class="notification-footer">
                                <a href="status.php">See All</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="dropdown d-none d-lg-flex">
                    <div id="header-profile" class="btn btn-outline-secondary d-flex align-items-center border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= htmlspecialchars($user_image) ?>" alt="Profile" width="25" height="25" class="rounded-circle me-2" onerror="this.src='../img/3.png';">
                        <div class="d-flex align-items-center">
                            <span class="text-dark text-nowrap"><?= htmlentities($_SESSION['displayName']); ?></span>
                            <i class="ri-arrow-drop-down-line text-dark fs-4"></i>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 14rem;">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="ri-user-3-line"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="ri-logout-circle-r-line"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.complaint-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const complaintNumber = this.getAttribute('data-complaint-number');

            fetch('readnotif.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'complaint_number=' + encodeURIComponent(complaintNumber)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let badges = document.querySelectorAll('.badge.text-bg-danger');
                    badges.forEach(badge => {
                        let currentCount = parseInt(badge.textContent) || 0;
                        if (currentCount > 0) {
                            currentCount--;
                            badge.textContent = currentCount || '';
                            if (currentCount === 0) badge.style.display = 'none';
                        }
                    });

                    let item = this.closest('.notification-item');
                    if (item) item.style.opacity = '0.5';

                    window.location.href = this.getAttribute('href');
                } else {
                    console.error('Failed to mark as read:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>