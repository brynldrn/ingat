<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit;
}

date_default_timezone_set('Asia/Manila');

$cid = $_GET['cid'] ?? null;
if (!$cid || !preg_match('/^CMP-\d{10,15}-\d{3,4}$/', $cid)) {
    header('Location: errorPage.php?msg=Invalid Complaint Number');
    exit();
}

try {
    $stmt = $conn->prepare("
        SELECT 
            tblcomplaints.*, 
            users.firstname, 
            users.middlename, 
            users.lastname, 
            weapons.weapon_type, 
            crime_types.crime_type 
        FROM tblcomplaints 
        LEFT JOIN users ON users.id = tblcomplaints.userId 
        LEFT JOIN weapons ON weapons.id = tblcomplaints.weapon_id
        LEFT JOIN crime_types ON crime_types.id = tblcomplaints.crime_type_id
        WHERE tblcomplaints.complaint_number = ?
    ");
    $stmt->bind_param("s", $cid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("<p>Complaint not found.</p>");
    }

    $complaint_details = $result->fetch_assoc();
    $complaint_details['name'] = $complaint_details['anonymous'] == 1 
        ? '<span class="anonymous-text">Anonymous</span>' 
        : (trim(($complaint_details['firstname'] ?? '') . ' ' . 
               (!empty($complaint_details['middlename']) ? $complaint_details['middlename'] . ' ' : '') . 
               ($complaint_details['lastname'] ?? '')) ?: 'Unknown User');
} catch (Exception $e) {
    die("<p>Error fetching complaint details: " . htmlspecialchars($e->getMessage()) . "</p>");
}

try {
    $stmt = $conn->prepare("SELECT remark, status, remark_date FROM complaintremark WHERE complaint_number = ? ORDER BY remark_date DESC");
    $stmt->bind_param("s", $cid);
    $stmt->execute();
    $remarks = $stmt->get_result();
} catch (Exception $e) {
    die("<p>Error fetching remarks: " . htmlspecialchars($e->getMessage()) . "</p>");
}

$uploadBasePath = __DIR__ . '/../users/complaintdocs/';
$filePaths = [];
if ($complaint_details['complaint_file']) {
    $fileNames = explode(',', $complaint_details['complaint_file']);
    foreach (array_slice($fileNames, 0, 3) as $fileName) {
        $filePath = $uploadBasePath . trim(basename($fileName));
        if (file_exists($filePath)) {
            $filePaths[] = ['path' => $filePath, 'type' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION))];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Complaint Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="assets/images/ingat.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/table.dataTable-th.css">
    <script src="assets/js/config.js"></script>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table.dataTable tbody tr {
            background-color: #fff;
        }
        .table-centered {
            text-align: center;
        }
        @media (min-width: 1200px) {
            .offset-xl-3 {
                margin-left: 22%;
                margin-top: 2%;
            }
        }
        .anonymous-text {
            font-style: italic;
            color: #6c757d;
        }
        #map {
            width: 400px;
            height: 250px;
        }
        .file-content {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-start;
        }
        .file-content img {
            width: 400px;
            height: 250px;
            object-fit: cover;
        }
        .file-content video {
            width: 400px;
            height: 250px;
        }
        .file-content iframe {
            width: 400px;
            height: 250px;
        }
    </style>
</head>
<body>
<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>  

<div class="row">
    <div class="col-xl-9 offset-xl-3">
        <div class="page-title-box">
            <h4 class="mb-0">Complaint Details</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="manage-complaints.php">Manage Complaints</a></li>
                <li class="breadcrumb-item active">Complaint Details</li>
            </ol>
        </div>
       
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Complaint Information</h4>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>#</th>
                        <td><?= htmlspecialchars($complaint_details['complaint_number']); ?></td>
                        <th>Complainant</th>
                        <td><?= $complaint_details['name']; ?></td>
                    </tr>
                    <tr>
                        <th>Date Filed</th>
                        <td>
                            <?php
                            $date = new DateTime($complaint_details['registered_at']);
                            echo $date->format('m/d/Y h:i A');
                            ?>
                        </td>
                        <th>Complaint Details</th>
                        <td><?= htmlspecialchars($complaint_details['complaint_details']); ?></td>
                    </tr>
                    <tr>
                        <th>Weapon Involve</th>
                        <td><?= htmlspecialchars($complaint_details['weapon_type'] ?? 'Not specified'); ?></td>
                        <th>Incident</th>
                        <td><?= htmlspecialchars($complaint_details['crime_type'] ?? 'Not specified'); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= htmlspecialchars($complaint_details['status'] ?? "New"); ?></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td colspan="3">
                            <div><?= htmlspecialchars($complaint_details['location']); ?></div>
                            <div id="map"></div>
                        </td>
                    </tr>
                    <tr>
                        <th>File (if any)</th>
                        <td colspan="3" class="file-content">
                            <?php if (!empty($filePaths)): ?>
                                <?php foreach ($filePaths as $file): ?>
                                    <?php if (in_array($file['type'], ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'tiff'])): ?>
                                        <img src="../users/complaintdocs/<?= htmlspecialchars(basename($file['path'])); ?>" alt="Complaint File">
                                    <?php elseif (in_array($file['type'], ['mp4', 'avi', 'mov'])): ?>
                                        <video controls>
                                            <source src="../users/complaintdocs/<?= htmlspecialchars(basename($file['path'])); ?>" type="video/<?= $file['type'] === 'mov' ? 'quicktime' : $file['type']; ?>">
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php else: ?>
                                        <iframe src="../users/complaintdocs/<?= htmlspecialchars(basename($file['path'])); ?>" frameborder="0"></iframe>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php elseif ($complaint_details['complaint_file']): ?>
                                <span class="text-danger">File(s) not found: <?= htmlspecialchars($complaint_details['complaint_file']); ?></span>
                            <?php else: ?>
                                File NA
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <?php if ($complaint_details['status'] != "Solved") { ?>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#updateComplaintModal">Take Action</button>
                <?php } ?>
            </div>
        </div>
    </div>


<!-- Update Complaint Modal -->
<div class="modal fade" id="updateComplaintModal" tabindex="-1" aria-labelledby="updateComplaintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="updatecomplaint.php" method="post" id="updateComplaintForm">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="updateComplaintModalLabel">Update Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="complaint_number" value="<?= htmlspecialchars($complaint_details['complaint_number']); ?>">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Solved">Solved</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea name="remark" id="remark" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
                </div>
                

<?php include('include/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="assets/js/vendor.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
<script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
<script src="assets/vendor/jsvectormap/maps/world.js"></script>
<script src="assets/js/pages/dashboard.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgUzZvcyWFzeG2bY8qNctYWFgadxGah0M&libraries=places"></script>
<script>
    let map, marker;

    function initMap() {
        const location = '<?= htmlspecialchars($complaint_details['location']); ?>';
        const geocoder = new google.maps.Geocoder();

        // Corrected from getId to getElementById
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 14.59, lng: 121.02 }, // Default Metro Manila
            zoom: 8
        });

        geocoder.geocode({ 'address': location }, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK && results[0]) {
                const coords = results[0].geometry.location;
                map.setCenter(coords);
                map.setZoom(14);

                marker = new google.maps.Marker({
                    position: coords,
                    map: map,
                    title: location
                });

                const infowindow = new google.maps.InfoWindow({
                    content: `<b>${location}</b>`
                });
                infowindow.open(map, marker);
            } else {
                console.error('Geocode failed: ' + status);
                // Optionally display a fallback message in the map div
                document.getElementById('map').innerHTML = 'Map unavailable: Unable to geocode location';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initMap();
        // Optional: Trigger resize to ensure map renders correctly
        setTimeout(function() {
            google.maps.event.trigger(map, 'resize');
        }, 100);
    });
</script>
</body>
</html>