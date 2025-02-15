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
    <title>Status</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f4f9;
        }
        .newsfeed-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .newsfeed-post {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
        }
        .newsfeed-post h2 {
            font-size: 18px;
            color: #333;
        }
        .newsfeed-post p {
            color: #555;
            margin: 10px 0;
        }
        .newsfeed-post img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 15px 0;
        }
        .newsfeed-post .post-time {
            font-size: 14px;
            color: #999;
        }
    </style>
</head>

<body>
<section id="container">
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    
    <section id="main-content">
        <section class="wrapper site-min-height">
            <h3><i class="fa fa-angle-right"></i> </h3>
            <hr />
            <div class="newsfeed-container">
                <?php
                $result = fetchPosts(); 
                if ($result->num_rows > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="newsfeed-post">';
                        echo '<h2>' . htmlspecialchars($row['name']) . '</h2>';
                        echo '<p class="post-time">' . htmlspecialchars(date("F j, Y, g:i a", strtotime($row['upload_date']))) . '</p>';
                        echo '<p>' . htmlspecialchars($row['details']) . '</p>';
                        if ($row['image']) {
                            echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="Status">';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<p>No post yet.</p>';
                }
                ?>
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
<script src="assets/js/jquery.scrollTo.min.js"></script>
<script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
<script src="assets/js/common-scripts.js"></script>

<script>
    $(function(){
        $('select.styled').customSelect();
    });
</script>

</body>
</html>
<?php } ?>