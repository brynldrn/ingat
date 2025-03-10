<?php
session_start();
include('include/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
} else {
    date_default_timezone_set('Asia/Kolkata');
    $currentTime = date('d-m-Y h:i:s A', time());

    $cid = $_GET['cid'] ?? null;
    if (!$cid || !preg_match('/^CMP-\d{10,15}-\d{3,4}$/', $cid)) {
        header('Location: errorPage.php?msg=Invalid Complaint Number');
        exit();
    }

    try {
        $stmt = $conn->prepare("
            SELECT tblcomplaints.*, users.firstname, users.middlename, users.lastname, weapons.weapon_type, crime_types.crime_type 
            FROM tblcomplaints 
            JOIN users ON users.id = tblcomplaints.userId 
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
        $complaint_details['name'] = $complaint_details['anonymous'] ? 'Anonymous' : trim($complaint_details['firstname'] . ' ' . ($complaint_details['middlename'] ? $complaint_details['middlename'] . ' ' : '') . $complaint_details['lastname']);
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
    $filePath = $complaint_details['complaint_file'] ? $uploadBasePath . basename($complaint_details['complaint_file']) : null;
    $fileExists = $filePath && file_exists($filePath);
    $fileType = $fileExists ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;
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
    <!-- App icon -->
    <link rel="shortcut icon" href="assets/images/ingat.ico">
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
                        <td><?= htmlspecialchars($complaint_details['name']); ?></td>
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
                        <th>File (if any)</th>
                        <td colspan="3">
                            <?php if ($complaint_details['complaint_file'] && $fileExists): ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fileModal">View File</button>
                            <?php elseif ($complaint_details['complaint_file']): ?>
                                <span class="text-danger">File not found: <?= htmlspecialchars($complaint_details['complaint_file']); ?></span>
                            <?php else: ?>
                                File NA
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td><a href="#" id="locationLink"><?= htmlspecialchars($complaint_details['location']); ?></a></td>
                        <th>Weapon Type</th>
                        <td><?= htmlspecialchars($complaint_details['weapon_type']); ?></td>
                    </tr>
                    <tr>
                        <th>Crime Type</th>
                        <td><?= htmlspecialchars($complaint_details['crime_type']); ?></td>
                        <th>Status</th>
                        <td><?= htmlspecialchars($complaint_details['status'] ?? "New"); ?></td>
                    </tr>
                </table>

                <?php if ($complaint_details['status'] != "Solved") { ?>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#updateComplaintModal">Take Action</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- File Modal -->
<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileModalLabel">Complaint File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($fileExists): ?>
                    <?php if (in_array($fileType, ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'tiff'])): ?>
                        <img src="../users/complaintdocs/<?= htmlspecialchars(basename($complaint_details['complaint_file'])); ?>" alt="Complaint File" style="width: 100%; height: auto;">
                    <?php elseif (in_array($fileType, ['mp4', 'avi', 'mov'])): ?>
                        <video controls style="width: 100%; height: auto;">
                            <source src="../users/complaintdocs/<?= htmlspecialchars(basename($complaint_details['complaint_file'])); ?>" type="video/<?= $fileType === 'mov' ? 'quicktime' : $fileType; ?>">
                            Your browser does not support the video tag.
                        </video>
                    <?php else: ?>
                        <iframe src="../users/complaintdocs/<?= htmlspecialchars(basename($complaint_details['complaint_file'])); ?>" frameborder="0" style="width: 100%; height: 500px;"></iframe>
                    <?php endif; ?>
                <?php else: ?>
                    <p>File not available or path is incorrect.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div><script>
    $(document).ready(function() {
        $('#fileModal').on('shown.bs.modal', function () {
            const video = document.querySelector('video');
            if (video) video.load();
        });
    });
</script>

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
<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Location on Map</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mapContainer" style="height: 500px;">
                    <div id="map" style="height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>


<?php include('include/footer.php'); ?>

<!-- Include jQuery and DataTables CSS and JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- Vendor Javascript -->
<script src="assets/js/vendor.min.js"></script>
<!-- App Javascript -->
<script src="assets/js/app.js"></script>
<!-- Vector Map Js -->
<script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
<script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
<script src="assets/vendor/jsvectormap/maps/world.js"></script>
<!-- Dashboard Js -->
<script src="assets/js/pages/dashboard.js"></script>
<!-- Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Mapbox Script -->
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
<script>
    mapboxgl.accessToken = 'pk.eyJ1Ijoib3Jlb2hvbGljIiwiYSI6ImNtMWFwdnR6bzF2c2QycXM4aW54Nmkxa3MifQ.0YVnZngmFw98M9yv9ZfFRw';
    let map;

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize the map
        map = L.map('map').setView([14.59, 121.02], 8); // Default to Metro Manila coordinates

        // Add Mapbox GL JS tiles
        L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=' + mapboxgl.accessToken).addTo(map);

        // Handle location link click
        document.getElementById('locationLink').addEventListener('click', async function (e) {
            e.preventDefault();
            const location = '<?= htmlspecialchars($complaint_details['location']); ?>';
            const coords = await fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(location)}.json?access_token=${mapboxgl.accessToken}`)
                .then(response => response.json())
                .then(data => {
                    if (data.features.length > 0) {
                        return data.features[0].center; // [lng, lat]
                    } else {
                        console.log(`No geocoding data found for location: ${location}`);
                        return null;
                    }
                })
                .catch(err => {
                    console.error(`Error fetching geocoding data: ${err}`);
                    return null;
                });

            if (coords) {
                map.setView([coords[1], coords[0]], 14); // Zoom in to the location
                L.marker([coords[1], coords[0]], { icon: L.icon({
                    iconUrl: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                })}).addTo(map)
                    .bindPopup(`<b>${location}</b>`)
                    .openPopup();
            }

            // Show the modal
            $('#locationModal').modal('show');
        });

        // Resize map when modal is shown
        $('#locationModal').on('shown.bs.modal', function () {
            setTimeout(function () {
                map.invalidateSize();
            }, 10);
        });
    });
</script>
</body>
</html>
<?php } ?>