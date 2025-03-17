<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) { 
    header('location:index.php');
    exit; 
} else {  
   
    $userId = $_SESSION['userId']; // Changed from $_SESSION['id']
    
    if (!is_numeric($userId)) {
        die("Invalid session data. Please login again.");
    }

    if (!isset($_SESSION['anonymous_complaint_ids'])) {
        $_SESSION['anonymous_complaint_ids'] = [];
    }

    $anonymous_complaint_ids = array_map('mysqli_real_escape_string', array_fill(0, count($_SESSION['anonymous_complaint_ids']), $conn), $_SESSION['anonymous_complaint_ids']);
    $anonymous_complaint_ids_list = "'" . implode("','", $anonymous_complaint_ids) . "'";

    $query = "
        SELECT c.complaint_number, c.registered_at, c.last_updated_at, c.status, 
               ct.crime_type, w.weapon_type, c.location
        FROM tblcomplaints c
        LEFT JOIN crime_types ct ON c.crime_type_id = ct.id
        LEFT JOIN weapons w ON c.weapon_id = w.id
        WHERE c.userId = '$userId' 
        OR (c.complaint_number IN ($anonymous_complaint_ids_list) AND c.anonymous = 1)
        ORDER BY c.registered_at DESC
    ";

    $result = mysqli_query($conn, $query);

    $newCountQuery = "SELECT COUNT(*) FROM tblcomplaints WHERE (userId='$userId' OR (complaint_number IN ($anonymous_complaint_ids_list) AND anonymous = 1)) AND status IS NULL";
    $inProgressCountQuery = "SELECT COUNT(*) FROM tblcomplaints WHERE (userId='$userId' OR (complaint_number IN ($anonymous_complaint_ids_list) AND anonymous = 1)) AND status='In Progress'";
    $solvedCountQuery = "SELECT COUNT(*) FROM tblcomplaints WHERE (userId='$userId' OR (complaint_number IN ($anonymous_complaint_ids_list) AND anonymous = 1)) AND status='Solved'";
    $totalCountQuery = "SELECT COUNT(*) FROM tblcomplaints WHERE (userId='$userId' OR (complaint_number IN ($anonymous_complaint_ids_list) AND anonymous = 1))";

    $newResult = mysqli_query($conn, $newCountQuery);
    $inProgressResult = mysqli_query($conn, $inProgressCountQuery);
    $solvedResult = mysqli_query($conn, $solvedCountQuery);
    $totalResult = mysqli_query($conn, $totalCountQuery);

    $num1 = mysqli_fetch_array($newResult)[0];
    $num2 = mysqli_fetch_array($inProgressResult)[0];
    $num3 = mysqli_fetch_array($solvedResult)[0];
    $num4 = mysqli_fetch_array($totalResult)[0];
?>

<?php include "plugins-header.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
     <!-- App icon -->
     <link rel="shortcut icon" href="asset/images/ingat.ico"></head>
<body>
    <?php include "header.php"; ?>
    <div class="container-fluid px-1 py-4">
        <div class="row row-gap-3 mx-0" style="max-height: calc(100vh - 145px);">
            <h4 class="fw-bold">Dashboard Status</h4>

            <div class="col-12 col-xl-3">
                <div class="border border-top-0 border-bottom-0 border-end-1 p-4 rounded-2 shadow-sm bg-white" style="border-left: 4px solid rgba(37, 99, 235, 1) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold m-0">New</h6>
                        <small>Pending reports</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(219, 234, 254, 1)">
                            <i class="ri-error-warning-line" style="color: rgba(37, 99, 235, 1) !important; font-size: 1.8rem;"></i>
                        </div>
                        <div class="m-0 fw-bold" style="font-size: 2.5rem;">
                            <?php echo htmlentities($num1); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-3">
                <div class="border border-top-0 border-bottom-0 border-end-1 p-4 rounded-2 shadow-sm bg-white" style="border-left: 4px solid rgba(234, 88, 12, 1) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold m-0">In Progress</h6>
                        <small>Active cases</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(255, 237, 213, 1)">
                            <i class="ri-time-line" style="color: rgba(234, 88, 12, 1) !important; font-size: 1.8rem;"></i>
                        </div>
                        <div class="m-0 fw-bold" style="font-size: 2.5rem;">
                            <?php echo htmlentities($num2); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-3">
                <div class="border border-top-0 border-bottom-0 border-end-1 p-4 rounded-2 shadow-sm bg-white" style="border-left: 4px solid rgba(22, 163, 74, 1) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold m-0">Solved</h6>
                        <small>Resolved cases</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(220, 252, 231, 1)">
                            <i class="ri-checkbox-circle-line" style="color: rgba(22, 163, 74, 1) !important; font-size: 1.8rem;"></i>
                        </div>
                        <div class="m-0 fw-bold" style="font-size: 2.5rem;">
                            <?php echo htmlentities($num3); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-3">
                <div class="border border-top-0 border-bottom-0 border-end-1 p-4 rounded-2 shadow-sm bg-white" style="border-left: 4px solid rgba(147, 51, 234, 1) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold m-0">Total Complaints</h6>
                        <small>All time</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(243, 232, 255, 1)">
                            <i class="ri-article-line" style="color: rgba(147, 51, 234, 1) !important; font-size: 1.8rem;"></i>
                        </div>
                        <div class="m-0 fw-bold" style="font-size: 2.5rem;">
                            <?php echo htmlentities($num4); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="p-3 rounded-2 shadow-sm border bg-white">
                    <h5 class="fw-bold mb-3">Recent Reports</h5>
                    <table id="reports_table" class="table table-bordered table-striped">
                        <thead class="position-sticky top-0">
                            <tr class="py-5">
                                <th>#</th>
                                <th>Date Filed</th>
                                <th>Last Update</th>
                                <th>Status</th>
                                <th>Incident</th>
                                <th>Weapon Involve</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_array($result)) {
                                    $registered_at = date("F d, Y h:i A", strtotime($row['registered_at']));
                                    $last_updated_at = date("F d, Y h:i A", strtotime($row['last_updated_at']));
                            ?>
                                <tr>
                                    <td data-label="#" class="py-3"><?php echo htmlentities($row['complaint_number']); ?></td>
                                    <td data-label="Date Filed" class="py-3"><?php echo htmlentities($registered_at); ?></td>
                                    <td data-label="Last Update" class="py-3"><?php echo htmlentities($last_updated_at); ?></td>
                                    <td data-label="Status" class="py-3">
                                        <?php 
                                        $status = $row['status'];
                                        if (empty($status) || $status == "NULL") { ?>
                                            <button type="button" class="btn btn-theme04">New</button>
                                        <?php } elseif ($status == "In Progress") { ?>
                                            <button type="button" class="btn btn-warning">In Progress</button>
                                        <?php } elseif ($status == "Solved") { ?>
                                            <button type="button" class="btn btn-success">Solved</button>
                                        <?php } ?>
                                    </td>
                                    <td data-label="Crime Type" class="py-3"><?php echo htmlentities($row['crime_type']); ?></td>
                                    <td data-label="Weapon Used" class="py-3"><?php echo htmlentities($row['weapon_type']); ?></td>
                                    <td data-label="Location" class="py-3"><?php echo htmlentities($row['location']); ?></td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='7' align='center'>No complaints found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include "plugins-footer.php"; ?>
<?php } ?>