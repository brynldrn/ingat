<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login'])==0) { 
    header('location:index.php');
} else {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <title>Home</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
    <link href="assets/css/jquery-ui.css" rel="stylesheet">
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">

    <style>
   /* Global Styling */
body {
    background-color: #f5f5f5;
    font-family: 'Georgia', serif;
    color: #333;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
#main-content {
  margin-left: -100px;
}
h1, h2, h3 {
    font-family: 'Georgia', serif;
    color: #222;
}

/* Sidebar Styling */
.sidebar {
    height: 100vh;
    width: 250px;
    background-color: #333;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 20px;
    padding-left: 20px;
    color: #fff;
    transition: all 0.3s ease;
    z-index: 100;
}

.sidebar .profile {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.sidebar .profile img {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
}

.sidebar .nav-list a {
    text-decoration: none;
    color: #fff;
    margin: 10px 0;
    display: flex;
    align-items: center;
    font-size: 16px;
}

.sidebar .nav-list a:hover {
    background-color: #982B1C;
}

/* Main Content Area */
.container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    grid-gap: 30px;
    padding-left: 0px; /* Ensure the content starts after the sidebar */
    margin-top: 20px;
}

.newsfeed-container {
    display: flex;
    flex-direction: column;
}

.newsfeed-post {
    background: #fff;
    border-radius: 8px;
    margin-bottom: 20px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
}

.newsfeed-post:hover {
    transform: translateY(-5px);
}

.post-header h2 {
    font-size: 24px;
    margin-bottom: 15px;
    font-weight: bold;
    color: #222;
}

.post-header .post-time {
    font-size: 14px;
    color: #777;
}

.post-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 8px;
}

.post-details {
    margin-top: 15px;
    font-size: 16px;
    color: #555;
}

.form-control {
    border-radius: 30px;
    border: 1px solid #ddd;
    width: 100%;
    padding: 10px;
}

.filter-section {
    margin-top: 20px;
    padding: 20px;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.filter-section .form-group {
    margin-bottom: 20px;
}

/* Pagination Styling */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

.pagination .page-item a {
    padding: 10px 15px;
    margin: 0 5px;
    border-radius: 5px;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
}

.pagination .page-item.active a {
    background-color: #0056b3;
}
/* Mobile Responsive */
@media (max-width: 1024px) {
    .container {
        padding-left: 0;
    }

    .newsfeed-container {
        padding: 10px;
    }

    .newsfeed-post {
        padding: 20px; /* Increased padding for better spacing */
    }

    .post-header h2 {
        font-size: 22px; /* Increased font size for post titles */
    }

    .post-details {
        font-size: 18px; /* Increased font size for post details */
    }

    /* Sidebar hiding on smaller screens */
    .sidebar {
        display: none;
    }

    /* Main content area padding adjustments */
    .container {
        padding-left: 0;
        padding-right: 0;
    }

    /* Adjustments for content layout */
    .newsfeed-container {
        margin: 0;
        margin-left: 60px;
    margin-right: -50px;
    }

    /* Bigger input field for search bar */
    .search-bar input {
        font-size: 18px; /* Larger text for easier typing */
        padding: 15px;
    }
}

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
    .newsfeed-container{
    padding-right: 34px;
    margin-left: -256px;
}

    .newsfeed-post {
        padding: 15px;
        margin-bottom: 20px;
    }

    .post-header h2 {
        font-size: 22px; /* Larger font size for mobile */
    }

    .post-details {
        font-size: 16px; /* Adjusted for readability */
    }

    .search-bar input {
        font-size: 18px; /* Larger text for easier typing */
        padding: 12px;
    }
}

@media (max-width: 480px) {
    .post-header h2 {
        font-size: 20px; /* Adjusted for smaller screens */
    }

    .post-details {
        font-size: 14px; /* Adjusted for readability */
    }
}
    </style>
</head>

<body>
<section id="container">
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>

    <section id="main-content">
        <section class="wrapper site-min-height">
            <div class="container">
                <!-- Search Bar -->
                <div class="newsfeed-container">
                    <form method="GET" action="" class="search-bar">
                        <div class="form-group" style="display: flex; align-items: center;">
                            <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                        </div>
                    </form>

                    <?php
                    // Initialize variables for filtering and sorting
                    $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
                    $dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
                    $sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'recent';
                    $sortOrderQuery = $sortOrder === 'oldest' ? 'ASC' : 'DESC';

                    // Pagination setup
                    $limit = 10;
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    // Prepare SQL query with placeholders to prevent SQL injection
                    $sql = "SELECT * FROM posts WHERE name LIKE ? AND (upload_date LIKE ?) ORDER BY upload_date $sortOrderQuery LIMIT ? OFFSET ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    $searchQueryParam = "%$searchQuery%";
                    $dateFilterParam = "%$dateFilter%";
                    mysqli_stmt_bind_param($stmt, 'ssii', $searchQueryParam, $dateFilterParam, $limit, $offset);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if ($result->num_rows > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="newsfeed-post">';
                            echo '<div class="post-header">';
                            echo '<h2>' . htmlspecialchars($row['name']) . '</h2>';
                            echo '<p class="post-time">' . htmlspecialchars(date("F j, Y g:ia", strtotime($row['upload_date']))) . '</p>';
                            echo '</div>';
                            if ($row['image']) {
                                echo '<div class="post-image">';
                                echo "<img src='" . htmlentities($row['image']) . "' alt='Post Image'>";
                                echo '</div>';
                            }
                            echo '<div class="post-details">';
                            echo '<p>' . htmlspecialchars($row['details']) . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No posts found.</p>';
                    }

                    // Pagination links
                    $totalPostsQuery = "SELECT COUNT(*) FROM posts WHERE name LIKE ? AND (upload_date LIKE ?)";
                    $stmtTotal = mysqli_prepare($conn, $totalPostsQuery);
                    mysqli_stmt_bind_param($stmtTotal, 'ss', $searchQueryParam, $dateFilterParam);
                    mysqli_stmt_execute($stmtTotal);
                    $totalPostsResult = mysqli_stmt_get_result($stmtTotal);
                    $totalPosts = mysqli_fetch_array($totalPostsResult)[0];
                    $totalPages = ceil($totalPosts / $limit);

                    if ($totalPages > 1) {
                        echo '<nav aria-label="Page navigation">';
                        echo '<ul class="pagination">';
                        for ($i = 1; $i <= $totalPages; $i++) {
                            $active = $i == $page ? 'active' : '';
                            echo "<li class='page-item $active'><a class='page-link' href='?page=$i&search=$searchQuery&date=$dateFilter&sort=$sortOrder'>$i</a></li>";
                        }
                        echo '</ul>';
                        echo '</nav>';
                    }
                    ?>
                </div>
            </div>
        </section><!-- /wrapper -->
    </section><!-- /MAIN CONTENT -->

    <?php include('includes/footer.php'); ?>
</section>

<!-- js placed at the end of the document so the pages load faster -->
<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
<script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="assets/js/common-scripts.js"></script>

</body>
</html>
<?php } ?>
