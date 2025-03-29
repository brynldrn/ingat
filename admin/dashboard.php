<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {    
    header('location:index.php');
} else {

// Fetch current counts
$userCountQuery = "SELECT COUNT(*) AS user_count FROM users";
$complaintCountQuery = "SELECT COUNT(*) AS complaint_count FROM tblcomplaints";
$pendingCountQuery = "SELECT COUNT(*) AS pending_count FROM tblcomplaints WHERE status = 'In Progress'";
$solvedCountQuery = "SELECT COUNT(*) AS solved_count FROM tblcomplaints WHERE status = 'Solved'";

// Execute the queries
$userCountResult = $conn->query($userCountQuery);
$complaintCountResult = $conn->query($complaintCountQuery);
$pendingCountResult = $conn->query($pendingCountQuery);
$solvedCountResult = $conn->query($solvedCountQuery);

// Fetch results
$userCount = $userCountResult->fetch_assoc()['user_count'];
$complaintCount = $complaintCountResult->fetch_assoc()['complaint_count'];
$pendingCount = $pendingCountResult->fetch_assoc()['pending_count'];
$solvedCount = $solvedCountResult->fetch_assoc()['solved_count'];

$previousUserCount = $_SESSION['previous_user_count'] ?? $userCount;
$previousComplaintCount = $_SESSION['previous_complaint_count'] ?? $complaintCount;
$previousPendingCount = $_SESSION['previous_pending_count'] ?? $pendingCount;
$previousSolvedCount = $_SESSION['previous_solved_count'] ?? $solvedCount;

// Calculate increases
$userIncrease = $userCount - $previousUserCount;
$complaintIncrease = $complaintCount - $previousComplaintCount;
$pendingIncrease = $pendingCount - $previousPendingCount;
$solvedIncrease = $solvedCount - $previousSolvedCount;

// Store current counts for future comparison
$_SESSION['previous_user_count'] = $userCount;
$_SESSION['previous_complaint_count'] = $complaintCount;
$_SESSION['previous_pending_count'] = $pendingCount;
$_SESSION['previous_solved_count'] = $solvedCount;

// Query to get data
$sql = "
    SELECT 
        c.crime_type, 
        COUNT(tc.crime_type_id) AS complaint_count, 
        SUM(IF(TIMESTAMPDIFF(DAY, tc.registered_at, CURDATE()) <= 1, 1, 0)) AS complaints_today, 
        SUM(IF(TIMESTAMPDIFF(DAY, tc.registered_at, CURDATE()) <= 30, 1, 0)) AS complaints_1_month, 
        SUM(IF(TIMESTAMPDIFF(DAY, tc.registered_at, CURDATE()) <= 180, 1, 0)) AS complaints_6_months, 
        SUM(IF(TIMESTAMPDIFF(DAY, tc.registered_at, CURDATE()) <= 365, 1, 0)) AS complaints_12_months 
    FROM 
        crime_types c 
    LEFT JOIN 
        tblcomplaints tc 
    ON 
        c.id = tc.crime_type_id 
    GROUP BY 
        c.crime_type";

$result = $conn->query($sql);

// Initialize arrays
$crimeTypes = [];
$complaintCounts = [];
$complaintsToday = [];
$complaints1Month = [];
$complaints6Months = [];
$complaints12Months = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $crimeTypes[] = $row['crime_type'];
        $complaintCounts[] = $row['complaint_count'];
        $complaintsToday[] = $row['complaints_today'];
        $complaints1Month[] = $row['complaints_1_month'];
        $complaints6Months[] = $row['complaints_6_months'];
        $complaints12Months[] = $row['complaints_12_months'];
    }
}

// Query to get monthly complaint data
$sql = "
    SELECT 
        MONTH(tc.registered_at) AS complaint_month, 
        c.crime_type, 
        COUNT(tc.crime_type_id) AS complaint_count
    FROM 
        crime_types c
    LEFT JOIN 
        tblcomplaints tc 
    ON 
        c.id = tc.crime_type_id
    GROUP BY 
        MONTH(tc.registered_at), c.crime_type
    ORDER BY 
        MONTH(tc.registered_at), c.crime_type";

$result = $conn->query($sql);

// Initialize data array
$monthlyData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $month = $row['complaint_month'];
        $crimeType = $row['crime_type'];
        $count = $row['complaint_count'];

        // Group data by month
        if (!isset($monthlyData[$month])) {
            $monthlyData[$month] = [];
        }
        $monthlyData[$month][$crimeType] = $count;
    }
}

// Fetch locations for the crime map
$locationsQuery = "SELECT location, COUNT(*) AS count FROM tblcomplaints GROUP BY location";
$locationsResult = $conn->query($locationsQuery);
$locations = [];

if ($locationsResult->num_rows > 0) {
    while ($row = $locationsResult->fetch_assoc()) {
        $locations[] = [
            'location' => $row['location'],
            'count' => $row['count']
        ];
    }
}


$monthlyDataJson = json_encode($monthlyData);
$locationsJson = json_encode($locations);

// Fetch recent complaints from the database
$query = "SELECT c.complaint_number, c.registered_at, c.location, ct.crime_type AS crime_type, 
                 w.weapon_type AS weapon, c.status
          FROM tblcomplaints c
          LEFT JOIN crime_types ct ON c.crime_type_id = ct.id
          LEFT JOIN weapons w ON c.weapon_id = w.id
          ORDER BY c.registered_at DESC
          LIMIT 5"; // Adjust the limit as needed

$recentComplaintsResult = mysqli_query($conn, $query);

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <!-- App icon -->
    <link rel="shortcut icon" href="assets/images/ingat.ico">
    <!-- Google Font Family link -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&amp;display=swap" rel="stylesheet">
    <!-- Vendor css -->
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="assets/css/style.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme Config js -->
    <script src="assets/js/config.js"></script>
</head><style>@media (min-width: 1200px) {
  .col-xl-6 {
    -webkit-box-flex: 0;
    -ms-flex: 0 0 auto;
    flex: 0 0 auto;
    width: 100%;
  }
}</style>
<body>
    <!-- START Wrapper -->
    <div class="app-wrapper">
    <?php include('include/header.php'); ?>
    <?php include('include/sidebar.php'); ?>
        <!-- App Menu End -->

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-fluid">

                <!-- ========== Page Title Start ========== -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <h4 class="mb-0">Dashboard</h4>
                        </div>
                    </div>
                </div>
                <!-- ========== Page Title End ========== -->
               
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
    <!-- Card 1: Users -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h3 class="text-dark"><?php echo $userCount; ?></h3>
                        <p class="mb-0">Users</p>
                        <?php if ($userIncrease > 0): ?>
                            <span class="text-success">+<?php echo $userIncrease; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                    <iconify-icon icon="solar:users-group-two-rounded-broken" class="fs-60 avatar-title text-primary"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Complaints -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h3 class="text-dark"><?php echo $complaintCount; ?></h3>
                        <p class="mb-0">Complaints</p>
                        <?php if ($complaintIncrease > 0): ?>
                            <span class="text-success">+<?php echo $complaintIncrease; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                    <iconify-icon icon="mdi:bell-alert-outline" class="fs-60 avatar-title text-primary"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Pendings -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h3 class="text-dark"><?php echo $pendingCount; ?></h3>
                        <p class="mb-0">Pendings</p>
                        <?php if ($pendingIncrease > 0): ?>
                            <span class="text-success">+<?php echo $pendingIncrease; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                    <iconify-icon icon="mdi:progress-clock" class="fs-60 avatar-title text-primary"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Solved -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h3 class="text-dark"><?php echo $solvedCount; ?></h3>
                        <p class="mb-0">Solved Cases</p>
                        <?php if ($solvedIncrease > 0): ?>
                            <span class="text-success">+<?php echo $solvedIncrease; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                    <iconify-icon icon="mdi:shield-check-outline" class="fs-60 avatar-title text-primary"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <h4 class="mb-0 flex-grow-1">Complaints</h4>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light" data-bs-toggle="dropdown" aria-expanded="false">
                        View Data
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item" data-value="today">Today</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item" data-value="1Month">Last 1 Month</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item" data-value="6Months">Last 6 Months</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item" data-value="12Months">Last 12 Months</a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <div dir="ltr">
                    <canvas id="crimeTypeChart" height="400"></canvas> <!-- Set the height here -->
                </div>
            </div>
        </div> 


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('crimeTypeChart').getContext('2d');

           
            const crimeTypes = <?php echo json_encode($crimeTypes); ?>;
            const complaintCountsToday = <?php echo json_encode($complaintsToday); ?>;
            const complaintCounts1Month = <?php echo json_encode($complaints1Month); ?>;
            const complaintCounts6Months = <?php echo json_encode($complaints6Months); ?>;
            const complaintCounts12Months = <?php echo json_encode($complaints12Months); ?>;

            const datasets = {
                today: {
                    label: 'Today',
                    data: complaintCountsToday,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                },
                '1Month': {
                    label: 'Last 1 Month',
                    data: complaintCounts1Month,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                },
                '6Months': {
                    label: 'Last 6 Months',
                    data: complaintCounts6Months,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1,
                },
                '12Months': {
                    label: 'Last 12 Months',
                    data: complaintCounts12Months,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1,
                }
            };

            
            const crimeTypeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: crimeTypes, 
                    datasets: [datasets.today] 
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                        },
                        y: {
                            beginAtZero: true,
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15, 
                            }
                        },
                        tooltip: {
                            position: 'nearest', 
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(54, 162, 235, 0.9)',
                            titleFont: { size: 16 },
                            bodyFont: { size: 14 },
                            footerFont: { size: 12 },
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.raw + ' complaints'; 
                                }
                            }
                        }
                    }
                }
            });

            
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                item.addEventListener('click', function() {
                    const selectedPeriod = this.getAttribute('data-value');
                    crimeTypeChart.data.datasets = [datasets[selectedPeriod]];
                    crimeTypeChart.update();
                });
            });
        });
    </script>
</div>
<div class="col-lg-4">
    <div class="card card-height-100">
        <div class="card-header d-flex align-items-center justify-content-between gap-2">
            <h4 class="card-title flex-grow-1 mb-0">Crimes</h4>
            <div>
                <select id="monthDropdown" class="btn btn-sm btn-outline-light">
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
        </div>

        <div class="card-body">
            <div dir="ltr">
                <canvas id="monthlyPieChart" height="400"></canvas>
            </div>
            <div class="table-responsive mb-n1 mt-2">
                <table class="table table-nowrap table-borderless table-sm table-centered mb-0">
                    <thead class="bg-light bg-opacity-50 thead-sm">
                        <tr>
                            <th class="py-1">Incident</th>
                            <th class="py-1">Reports</th>
                        </tr>
                    </thead>
                    <tbody id="crimeTableBody">
                       
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Parse the monthly data from PHP
                const monthlyData = <?php echo $monthlyDataJson; ?>;

                // Prepare data for the selected month
                let selectedMonth = 12; // Default to December
                const dataForMonth = monthlyData[selectedMonth] || {};

                const labels = Object.keys(dataForMonth); // Crime types
                const dataValues = Object.values(dataForMonth); // Complaint counts

                // Donut Chart Configuration
                const ctx = document.getElementById('monthlyPieChart').getContext('2d');
                const monthlyPieChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Complaints Distribution',
                            data: dataValues,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        return `${tooltipItem.raw} complaints`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Function to update the table
                function updateTable(data) {
                    const tableBody = document.getElementById('crimeTableBody');
                    tableBody.innerHTML = ''; // Clear existing rows
                    for (const [crime, count] of Object.entries(data)) {
                        const row = `<tr>
                            <td>${crime}</td>
                            <td>${count}</td>
                        </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    }
                }

                // Initial table update
                updateTable(dataForMonth);

                // Update chart and table data when month is changed
                document.getElementById('monthDropdown').addEventListener('change', function () {
                    selectedMonth = this.value;
                    const newDataForMonth = monthlyData[selectedMonth] || {};
                    monthlyPieChart.data.labels = Object.keys(newDataForMonth);
                    monthlyPieChart.data.datasets[0].data = Object.values(newDataForMonth);
                    monthlyPieChart.update();
                    updateTable(newDataForMonth);
                });
            });
        </script>
    </div> <!-- end card-->
</div> <!-- end col -->


<!-- Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="col-lg-4">
    <div class="card">
        <div class="d-flex card-header justify-content-between align-items-center border-bottom border-dashed">
            <h4 class="card-title mb-0">Locations of Incident</h4>
        </div>

        <div class="card-body pt-0">
            <div id="mapContainer" style="height: 309px;">
                <div id="map" style="height: 100%;"></div>
            </div>
        </div> <!-- end card-body -->
    </div>
</div>

<!-- Mapbox Script -->
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
<script>
    mapboxgl.accessToken = 'pk.eyJ1Ijoib3Jlb2hvbGljIiwiYSI6ImNtMWFwdnR6bzF2c2QycXM4aW54Nmkxa3MifQ.0YVnZngmFw98M9yv9ZfFRw';
    const map = L.map('map').setView([14.59, 121.02], 8); // Metro Manila coordinates

    // Add Mapbox GL JS tiles
    L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=' + mapboxgl.accessToken).addTo(map);

    const locations = <?= $locationsJson; ?>;
    let markers = [];

    async function addMarkers(filteredLocations) {
        // Remove existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        for (let location of filteredLocations) {
            const coords = await fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(location.location)}.json?access_token=${mapboxgl.accessToken}`)
                .then(response => response.json())
                .then(data => {
                    if (data.features.length > 0) {
                        return data.features[0].center; // [lng, lat]
                    } else {
                        console.log(`No geocoding data found for location: ${location.location}`);
                        return null;
                    }
                })
                .catch(err => {
                    console.error(`Error fetching geocoding data: ${err}`);
                    return null;
                });

            if (coords) {
                const pinColor = location.count > 2 ? 'red' : (location.count === 2 ? 'yellow' : 'green');
                const icon = L.icon({
                    iconUrl: `https://maps.google.com/mapfiles/ms/icons/${pinColor}-dot.png`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
                const marker = L.marker([coords[1], coords[0]], { icon: icon })
                    .bindPopup(`<b>${location.location}</b><br>${location.count} incidents`)
                    .addTo(map);

                markers.push(marker);
            }
        }
    }

    // Initial markers
    addMarkers(locations);

    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', async function () {
            const riskLevel = this.getAttribute('data-risk').toLowerCase();
            let filteredLocations;

            if (riskLevel === 'high') {
                filteredLocations = locations.filter(location => location.count > 2);
            } else if (riskLevel === 'moderate') {
                filteredLocations = locations.filter(location => location.count === 2);
            } else if (riskLevel === 'low') {
                filteredLocations = locations.filter(location => location.count === 1);
            }

            console.log(`Filtered locations for ${riskLevel}:`, filteredLocations);

            if (filteredLocations.length > 0) {
                await addMarkers(filteredLocations);
            } else {
                console.log(`No locations found for risk level: ${riskLevel}`);
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];
            }
        });
    });
</script>
                                </div>
                            </div> <!-- end card-body-->

                            <div class="col-xl-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                Recent Complaints
            </h4>
            <a href="notprocess-complaint.php" class="btn btn-sm btn-light">
                View Recent Complaints
            </a>
        </div>
        <!-- end card-header-->

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-centered">
                    <thead>
                        <tr>
                            <th class="py-1">ID</th>
                            <th class="py-1">Date</th>
                            <th class="py-1">Location</th>
                            <th class="py-1">Incident</th>
                            <th class="py-1">Weapon Involve</th>
                            <th class="py-1">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($recentComplaintsResult) > 0) {
                            while ($row = mysqli_fetch_assoc($recentComplaintsResult)) {
                                $status = $row['status'] ? $row['status'] : 'new';
                                $badgeClass = $status == 'Solved' ? 'bg-success' : ($status == 'In Progress' ? 'bg-warning' : 'bg-danger');
                                echo "<tr>";
                                echo "<td>" . htmlentities($row['complaint_number']) . "</td>";
                                echo "<td>" . htmlentities(date('d M, Y', strtotime($row['registered_at']))) . "</td>";
                                echo "<td>" . htmlentities($row['location']) . "</td>";
                                echo "<td>" . htmlentities($row['crime_type']) . "</td>";
                                echo "<td>" . htmlentities($row['weapon']) . "</td>";
                                echo "<td><span class='badge $badgeClass'>" . htmlentities($status) . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No recent complaints found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end card body -->
    </div>

    

            </div>
            <!-- End Container Fluid -->

            <!-- Footer Start -->
            <?php include('include/footer.php'); ?>
            <!-- Footer End -->

        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->

    </div>
    <!-- END Wrapper -->

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
</body>
</html>
<?php } ?>