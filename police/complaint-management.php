<?php
session_start();
include "includes/config.php"; // Database connection

if (!isset($_SESSION['police_id'])) {
    header("Location: index.php");
    exit();
}

$police_id = $_SESSION['police_id'];
// Join tblcomplaints with crime_types and users
$query = "SELECT tc.*, ct.crime_type, u.firstname, u.middlename, u.lastname 
          FROM tblcomplaints tc 
          LEFT JOIN crime_types ct ON tc.crime_type_id = ct.id 
          LEFT JOIN users u ON tc.userId = u.id 
          WHERE tc.police_id = ? 
          ORDER BY tc.registered_at DESC";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $police_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Management - INGAT</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .container-lg {
            margin-top: 20px;
        }
        .bg-white {
            background: #fff;
            border-radius: 10px;
            transition: transform 0.2s ease;
        }
        .bg-white:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .btn-dark {
            background: #1a1a2e;
            border: none;
        }
        .btn-dark:hover {
            background: #0f4c75;
        }
        .badge {
            padding: 0.5em 1em;
            font-size: 0.9em;
        }
        .anonymous-text {
            color: #6c757d;
            font-style: italic;
        }
        .complaint-card {
            display: none; /* Hidden by default, shown by JS if matches search */
        }
        .complaint-card.visible {
            display: block; /* Shown when matches search */
        }
    </style>
</head>
<body>
    <?php include "includes/header.php"; ?>
    <div class="container-lg px-1 py-4">
        <div class="row row-gap-3 column-gap-2 mx-0 flex-wrap-reverse">
            <div class="col row row-gap-4 mx-0" style="max-height: calc(100vh - 120px); overflow-y: auto;">
                <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold">Complaint Management</h5>
                        <p>Complaints</p>
                    </div>
                    <div class="d-flex align-items-center column-gap-2">
                        <div class="input-group" style="width: 100%;">
                            <span class="input-group-text border-end-0" id="basic-addon1">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search reports..." aria-label="Search" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>

                <div id="complaintList">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $complainant_name = $row['anonymous'] == 1 
                                ? '<span class="anonymous-text">Anonymous</span>' 
                                : (trim(($row['firstname'] ?? '') . ' ' . 
                                        (!empty($row['middlename']) ? $row['middlename'] . ' ' : '') . 
                                        ($row['lastname'] ?? '')) ?: 'Unknown User');

                            $badge_class = "";
                            switch ($row['status']) {
                                case "Pending":
                                    $badge_class = "text-bg-warning";
                                    break;
                                case "In Progress":
                                    $badge_class = "text-bg-primary";
                                    break;
                                case "Solved":
                                    $badge_class = "text-bg-success";
                                    break;
                                default:
                                    $badge_class = "text-bg-secondary";
                            }
                            ?>
                            <div class="bg-white p-4 shadow-sm border rounded-2 complaint-card" 
                                 data-search="<?php echo htmlspecialchars(strtolower($row['complaint_number'] . ' ' . ($row['crime_type'] ?? '') . ' ' . $row['location'] . ' ' . $row['complaint_details'] . ' ' . strip_tags($complainant_name))); ?>">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="m-0 fw-bold"><?php echo htmlspecialchars($row['crime_type'] ?? 'Unknown Crime Type'); ?></h5>
                                        <small><?php echo htmlspecialchars($row['complaint_number']) . " • " . htmlspecialchars($row['location']); ?></small>
                                    </div>
                                    <div>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($row['status'] ?? 'Not Assigned'); ?></span>
                                    </div>
                                </div>
                                <div class="my-3">
                                    <small class="fw-bold m-0 d-block"><?php echo htmlspecialchars($row['complaint_details']); ?></small>
                                    <small>Reported on: <?php echo htmlspecialchars($row['registered_at']); ?></small>
                                    <small class="d-block">Complainant: <?php echo $complainant_name; ?></small>
                                </div>
                                <div class="d-flex align-items-center justify-content-end column-gap-2">
                                    <a href="view-details.php?cid=<?php echo urlencode($row['complaint_number']); ?>" class="btn btn-outline-dark text-nowrap">View Details</a>
                                    <a href="take-action.php?cid=<?php echo urlencode($row['complaint_number']); ?>" class="btn btn-dark text-nowrap">Take Action</a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-center' id='noResults'>No complaints found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const complaintCards = document.querySelectorAll('.complaint-card');
            const noResults = document.getElementById('noResults');

            // Show all cards initially
            complaintCards.forEach(card => card.classList.add('visible'));

            searchInput.addEventListener('input', function () {
                const searchTerm = searchInput.value.trim().toLowerCase();
                let hasVisible = false;

                complaintCards.forEach(card => {
                    const searchData = card.getAttribute('data-search');
                    if (searchData.includes(searchTerm)) {
                        card.classList.add('visible');
                        hasVisible = true;
                    } else {
                        card.classList.remove('visible');
                    }
                });

                if (noResults) {
                    noResults.style.display = hasVisible ? 'none' : 'block';
                }
            });
        });
    </script>
</body>
</html>