<?php 
session_start();
error_reporting(0);
include('includes/config.php');

// Check if the user is logged in
if(strlen($_SESSION['login'])==0) { 
    header('location:index.php');
    exit; // Ensure no further code executes if user is not logged in
} else {  
    // Fetch and store userId for the logged-in user
    $userId = $_SESSION['id']; // Ensure the correct session key is used here
    
    // Optional: Validate or sanitize the userId if used in queries later
    if (!is_numeric($userId)) {
        die("Invalid session data. Please login again.");
    }

    // Query to fetch complaints for the logged-in user
    $query = "
        SELECT c.complaint_number, c.registered_at, c.last_updated_at, c.status, 
               ct.crime_type, w.weapon_type, c.location
        FROM tblcomplaints c
        LEFT JOIN crime_types ct ON c.crime_type_id = ct.id
        LEFT JOIN weapons w ON c.weapon_id = w.id
        WHERE c.userId = '$userId'
        ORDER BY c.registered_at DESC
    ";

    $result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>Status</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="assets/css/zabuto_calendar.css">
    <link rel="stylesheet" type="text/css" href="assets/js/gritter/css/jquery.gritter.css" />
    <link rel="stylesheet" type="text/css" href="assets/lineicons/style.css">    
    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <script src="assets/js/chart-master/Chart.js"></script>
  </head>
  <style>
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card-icon {
            font-size: 2.5rem;
            color: #007bff;
        }
        @media (max-width: 768px) {
            .card {
                margin-bottom: 20px;
            }
            .card-icon {
                font-size: 2rem;
            }
            .card-title {
                font-size: 1.2rem;
            }
            .card-text {
                font-size: 1.5rem;
            }
        }
        .content-panel {
            margin-top: 20px;
        }
        .table-responsive {
            overflow-x: auto;
        }
#main-content {
    margin-top: 60px; 
    margin-left: 285px;
    padding: 20px;
}

@media (max-width: 768px) {
    #main-content {
        margin-left: 0; 
        margin-top: 50px; 
    }
}

    </style>
  <body>
    <section id="container">
        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <section id="main-content">
            <section class="wrapper py-4">
                <div class="container">
                    <div class="row text-center animate__animated animate__fadeIn">

                        <!-- Complaints not processed -->
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-icon mb-3">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <?php 
                                    $rt = mysqli_query($conn, "SELECT * FROM tblcomplaints WHERE userId='$userId' AND status IS NULL");
                                    $num1 = mysqli_num_rows($rt);
                                    ?>
                                    <h5 class="card-title">New</h5>
                                    <p class="card-text fs-3 fw-bold"><?php echo htmlentities($num1); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Complaints in process -->
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-icon mb-3">
                                        <i class="fas fa-spinner"></i>
                                    </div>
                                    <?php 
                                    $status = "In Progress";
                                    $rt = mysqli_query($conn, "SELECT * FROM tblcomplaints WHERE userId='$userId' AND status='$status'");
                                    $num2 = mysqli_num_rows($rt);
                                    ?>
                                    <h5 class="card-title">In Progress</h5>
                                    <p class="card-text fs-3 fw-bold"><?php echo htmlentities($num2); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Complaints closed -->
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-icon mb-3">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <?php 
                                    $status = "Solved";
                                    $rt = mysqli_query($conn, "SELECT * FROM tblcomplaints WHERE userId='$userId' AND status='$status'");
                                    $num3 = mysqli_num_rows($rt);
                                    ?>
                                    <h5 class="card-title">Solved</h5>
                                    <p class="card-text fs-3 fw-bold"><?php echo htmlentities($num3); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Total complaints -->
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-icon mb-3">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <?php 
                                    $rt = mysqli_query($conn, "SELECT * FROM tblcomplaints WHERE userId='$userId'");
                                    $num4 = mysqli_num_rows($rt);
                                    ?>
                                    <h5 class="card-title">Total Complaints</h5>
                                    <p class="card-text fs-3 fw-bold"><?php echo htmlentities($num4); ?></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section class="wrapper">
                <div class="row mt">
                    <div class="col-lg-12">
                      <div class="content-panel">
                          <section id="unseen">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed">
                                  <thead>
                                  <tr>
                                      <th>#</th>
                                      <th>Date Filed</th>
                                      <th>Last Update</th>
                                      <th>Status</th>
                                      <th>Crime Type</th>
                                      <th>Weapon Used</th>
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
                                            <td align="center"><?php echo htmlentities($row['complaint_number']); ?></td>
                                            <td align="center"><?php echo htmlentities($registered_at); ?></td>
                                            <td align="center"><?php echo htmlentities($last_updated_at); ?></td>
                                            <td align="center">
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
                                            <td align="center"><?php echo htmlentities($row['crime_type']); ?></td>
                                            <td align="center"><?php echo htmlentities($row['weapon_type']); ?></td>
                                            <td align="center"><?php echo htmlentities($row['location']); ?></td>
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
                          </section>
                      </div><!-- /content-panel -->
                   </div><!-- /col-lg-12 -->            
                </div><!-- /row -->
            </section> 
        </section><!-- /MAIN CONTENT -->
        
        <?php include("includes/footer.php"); ?>
    </section>
  
    <!-- JavaScript at the end for faster page load -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/jquery-1.8.3.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="assets/js/common-scripts.js"></script>
  </body>
</html>
<?php } ?>