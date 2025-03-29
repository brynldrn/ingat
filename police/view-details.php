<?php
session_start();
include "includes/config.php"; 

if (!isset($_SESSION['police_id'])) {
    header("Location: index.php");
    exit();
}


$cid = $_GET['cid'] ?? null;
if (!$cid) {
    die("No complaint number provided.");
}

// Fetch complaint details
$stmt = $conn->prepare("
    SELECT c.complaint_number, c.complaint_details, c.status, c.registered_at, c.last_updated_at, 
           c.location, c.anonymous, c.complaint_file, u.firstname, u.lastname, u.user_email, u.contact_no, 
           p.firstname AS officer_firstname, p.lastname AS officer_lastname, 
           ct.crime_type, w.weapon_type
    FROM tblcomplaints c
    LEFT JOIN users u ON c.userId = u.id
    LEFT JOIN police p ON c.police_id = p.id
    LEFT JOIN crime_types ct ON c.crime_type_id = ct.id
    LEFT JOIN weapons w ON c.weapon_id = w.id
    WHERE c.complaint_number = ? AND c.police_id = ?
");
$stmt->bind_param("si", $cid, $_SESSION['police_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Complaint not found or you don't have permission to access it.");
}
$complaint = $result->fetch_assoc();
$stmt->close();

// Fetch action history (remarks and police files)
$remarks_stmt = $conn->prepare("
    SELECT cr.id, cr.status, cr.remark, cr.remark_date, caf.file_path
    FROM complaintremark cr
    LEFT JOIN complaint_action_files caf ON cr.id = caf.remark_id AND caf.complaint_number = cr.complaint_number
    WHERE cr.complaint_number = ?
    ORDER BY cr.remark_date ASC
");
$remarks_stmt->bind_param("s", $cid);
$remarks_stmt->execute();
$remarks_result = $remarks_stmt->get_result();
$action_history = [];
while ($row = $remarks_result->fetch_assoc()) {
    $action_history[] = $row;
}
$remarks_stmt->close();

// Complainant files
$uploadBasePath = __DIR__ . '/../users/complaintdocs/';
$filePaths = [];
if ($complaint['complaint_file']) {
    $fileNames = explode(',', $complaint['complaint_file']);
    foreach (array_slice($fileNames, 0, 3) as $fileName) {
        $filePath = $uploadBasePath . trim(basename($fileName));
        if (file_exists($filePath)) {
            $filePaths[] = ['path' => $filePath, 'type' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION))];
        }
    }
}
?>

<?php include "plugins-header.php"; ?>
<body>
	<?php include "includes/header.php"; ?>
    <div class="container px-1 py-4">
        <div class="row row-gap-3 column-gap-2 mx-0 flex-wrap-reverse">
            <div class="col-12 row row-gap-4 column-gap-2 mx-0" style="max-height: calc(100vh - 120px);">
                <a href="complaint-management.php" class="text-decoration-none text-dark mb-0"><i class="ri-arrow-left-double-line"></i> Back to Dashboard</a>
                <div class="col-12 col-lg-11 d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between">
                    <div>
                        <h5 class="fw-bold">Complaint Management</h5>
                        <p><i class="ri-map-pin-line"></i><?php echo htmlspecialchars($complaint['location']); ?></p>
                    </div>
                    <div>
                        <span class="badge text-bg-<?php echo $complaint['status'] === 'Solved' ? 'success' : 'primary'; ?>">
                            <?php echo htmlspecialchars($complaint['status'] ?? 'Pending'); ?>
                        </span>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="bg-white p-4 shadow-sm border rounded-2 mb-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong class="d-block m-0">Complaint Details</strong>
                                <small><i class="ri-calendar-line me-2"></i>Reported on <?php echo date('Y-m-d h:i A', strtotime($complaint['registered_at'])); ?></small>
                            </div>
                            <div>
                                <span class="badge text-bg-<?php echo $complaint['status'] === 'Solved' ? 'success' : ($complaint['status'] === 'In Progress' ? 'primary' : 'warning'); ?>">
                                    <?php echo htmlspecialchars($complaint['status'] ?? 'Pending'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="my-3">
                            <p><strong>Incident:</strong> <?php echo htmlspecialchars($complaint['crime_type'] ?? 'Not specified'); ?></p>
                            <p><strong>Weapon Involved:</strong> <?php echo htmlspecialchars($complaint['weapon_type'] ?? 'Not specified'); ?></p>
                            <p><strong>Details:</strong> <?php echo htmlspecialchars($complaint['complaint_details']); ?></p>
                            <p><strong>Location Map:</strong></p>
                            <div id="map" style="width: 100%; height: 250px;"></div>
                            <?php if (!empty($filePaths)): ?>
                                <p><strong>Complainant Evidence:</strong></p>
                                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                    <?php foreach ($filePaths as $file): ?>
                                        <?php if (in_array($file['type'], ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'tiff'])): ?>
                                            <img src="../users/complaintdocs/<?php echo htmlspecialchars(basename($file['path'])); ?>" width="300" height="200" style="object-fit: cover;" alt="Complaint File">
                                        <?php elseif (in_array($file['type'], ['mp4', 'avi', 'mov'])): ?>
                                            <video width="300" height="200" controls>
                                                <source src="../users/complaintdocs/<?php echo htmlspecialchars(basename($file['path'])); ?>" type="video/<?php echo $file['type'] === 'mov' ? 'quicktime' : $file['type']; ?>">
                                                Your browser does not support the video tag.
                                            </video>
                                        <?php else: ?>
                                            <iframe src="../users/complaintdocs/<?php echo htmlspecialchars(basename($file['path'])); ?>" width="300" height="200" frameborder="0"></iframe>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif ($complaint['complaint_file']): ?>
                                <p><strong>Complainant Evidence:</strong> <span class="text-danger">File(s) not found: <?php echo htmlspecialchars($complaint['complaint_file']); ?></span></p>
                            <?php else: ?>
                                <p><strong>Complainant Evidence:</strong> None</p>
                            <?php endif; ?>
                        </div>

                        <div class="mb-2 p-3" style="background-color: var(--light-gray);">
                            <div><i class="ri-user-line me-2"></i><b>Reporter Information</b></div>
                            <ul class="navbar-nav">
                                <li class="nav-item">Name: <span><?php echo $complaint['anonymous'] ? 'Anonymous' : htmlspecialchars($complaint['firstname'] . ' ' . $complaint['lastname']); ?></span></li>
                                <?php if (!$complaint['anonymous']): ?>
                                    <li class="nav-item">Phone: <span><?php echo htmlspecialchars($complaint['contact_no'] ?? 'N/A'); ?></span></li>
                                    <li class="nav-item">Email: <span><?php echo htmlspecialchars($complaint['user_email'] ?? 'N/A'); ?></span></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <?php foreach ($action_history as $action): ?>
                        <div class="bg-white p-4 shadow-sm border rounded-2 mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong class="d-block m-0"><?php echo htmlspecialchars($complaint['officer_firstname'] . ' ' . $complaint['officer_lastname']); ?></strong>
                                    <small><i class="ri-calendar-line me-2"></i><?php echo date('Y-m-d h:i A', strtotime($action['remark_date'])); ?></small>
                                </div>
                                <div>
                                    <span class="badge text-bg-<?php echo $action['status'] === 'Solved' ? 'success' : 'primary'; ?>">
                                        <?php echo htmlspecialchars($action['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="my-3">
                                <p><?php echo htmlspecialchars($action['remark']); ?></p>
                                <?php if ($action['file_path']): ?>
                                    <p><strong>Police Evidence:</strong></p>
                                    <?php $file_ext = strtolower(pathinfo($action['file_path'], PATHINFO_EXTENSION)); ?>
                                    <?php if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'tiff'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($action['file_path']); ?>" width="100%" height="auto" alt="Action File">
                                    <?php elseif (in_array($file_ext, ['mp4', 'avi', 'mov'])): ?>
                                        <video width="100%" height="auto" controls>
                                            <source src="uploads/<?php echo htmlspecialchars($action['file_path']); ?>" type="video/<?php echo $file_ext === 'mov' ? 'quicktime' : $file_ext; ?>">
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php else: ?>
                                        <iframe src="uploads/<?php echo htmlspecialchars($action['file_path']); ?>" width="100%" height="300" frameborder="0"></iframe>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-12 col-lg-3">
                    <div class="bg-white p-4 shadow-sm border rounded-2">
                        <strong>Case Summary</strong>
                        <div>
                            <small class="text-secondary">Case ID</small>
                            <p><?php echo htmlspecialchars($complaint['complaint_number']); ?></p>
                        </div>
                        <hr>
                        <div>
                            <small class="text-secondary d-block">Status</small>
                            <span class="badge text-bg-<?php echo $complaint['status'] === 'Solved' ? 'success' : 'primary'; ?>">
                                <?php echo htmlspecialchars($complaint['status'] ?? 'Pending'); ?>
                            </span>
                        </div>
                        <hr>
                        <div>
                            <small class="text-secondary d-block">Date Reported</small>
                            <span><?php echo date('Y-m-d h:i A', strtotime($complaint['registered_at'])); ?></span>
                        </div>
                        <hr>
                        <div>
                            <small class="text-secondary d-block">Last Updated</small>
                            <span><?php echo date('Y-m-d h:i A', strtotime($complaint['last_updated_at'])); ?></span>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <small class="text-secondary d-block">Assigned Officer</small>
                            <span><?php echo htmlspecialchars($complaint['officer_firstname'] . ' ' . $complaint['officer_lastname']); ?></span>
                        </div>
                        <div class="d-flex flex-column row-gap-2">
                            <button class="btn btn-outline-dark border-secondary-subtle" onclick="window.print()">Print Report</button>
                            <?php if ($complaint['status'] !== 'Solved'): ?>
                                <a href="take-action.php?cid=<?php echo urlencode($complaint['complaint_number']); ?>" class="btn btn-dark">Take Action</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgUzZvcyWFzeG2bY8qNctYWFgadxGah0M&libraries=places"></script>
    <script>
        let map, marker;

        function initMap() {
            const location = '<?php echo htmlspecialchars($complaint['location']); ?>';
            const geocoder = new google.maps.Geocoder();
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
                    document.getElementById('map').innerHTML = 'Map unavailable: Unable to geocode location';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initMap();
            setTimeout(function() {
                google.maps.event.trigger(map, 'resize');
            }, 100);
        });
    </script>
</body>
<?php include "plugins-footer.php"; ?>