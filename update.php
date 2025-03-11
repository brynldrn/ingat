<?php
include 'users/includes/config.php';

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'recent';
$sortOrderQuery = $sortOrder === 'oldest' ? 'ASC' : 'DESC';

$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM posts WHERE name LIKE ? AND (upload_date LIKE ?) ORDER BY upload_date $sortOrderQuery LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$searchQueryParam = "%$searchQuery%";
$dateFilterParam = "%$dateFilter%";
mysqli_stmt_bind_param($stmt, 'ssii', $searchQueryParam, $dateFilterParam, $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();

$totalPostsQuery = "SELECT COUNT(*) FROM posts WHERE name LIKE ? AND (upload_date LIKE ?)";
$stmtTotal = $conn->prepare($totalPostsQuery);
mysqli_stmt_bind_param($stmtTotal, 'ss', $searchQueryParam, $dateFilterParam);
mysqli_stmt_execute($stmtTotal);
$totalPostsResult = mysqli_stmt_get_result($stmtTotal);
$totalPosts = mysqli_fetch_array($totalPostsResult)[0];
$totalPages = ceil($totalPosts / $limit);
$stmtTotal->close();

$basePath = $_SERVER['DOCUMENT_ROOT'] . '/git/ingat/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Awareness Posts - INGAT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="users/asset/images/ingat.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header-top {
            background-color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }
        .header-top img {
            height: 50px;
        }
        .header-top nav {
            display: flex;
            gap: 20px;
        }
        .header-top nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .header-top nav a:hover {
            color: #F7931E;
        }
        .header-top nav a.active {
            border-bottom: 2px solid #28a745;
            padding-bottom: 5px;
        }
        .hero-section {
            position: relative;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
        }
        .hero-section video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        .hero-section h1 {
            position: relative;
            z-index: 1;
            font-size: 2.5rem;
            color: #F7931E;
            text-transform: uppercase;
        }
        .content {
            flex: 1 0 auto;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .content h2 {
            text-align: center;
            text-transform: uppercase;
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #28a745;
            display: inline-block;
        }
        .newsfeed-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .newsfeed-post {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .post-header {
            margin-bottom: 15px;
        }
        .post-header h2 {
            font-size: 1.3rem;
            color: #1A2A6C;
            margin: 0;
            border: none;
            display: block;
            text-align: left;
        }
        .post-time {
            font-style: italic;
            color: #555;
            margin: 5px 0 0;
        }
        .post-image img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-top: 10px;
        }
        .post-details p {
            line-height: 1.6;
            margin: 0;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        .pagination .page-item.active .page-link {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }
        .pagination .page-link {
            color: #1A2A6C;
        }
        .pagination .page-link:hover {
            background-color: #F7931E;
            color: #fff;
        }
        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #F7931E;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, background-color 0.3s ease;
        }

        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            background-color: #e07b1a;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="header-top">
        <img src="users/asset/images/ingat.ico" alt="INGAT Logo">
        <nav>
            <a href="#">Home</a>
            <a href="#">About Us</a>
            <a href="#" class="active">Awareness Posts</a>
            <a href="#">FAQ</a>
            <a href="#">Contact Us</a>
        </nav>
    </div>

    <div class="hero-section">
        <video autoplay loop muted>
            <source src="img/flag.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <h1>Published Posts</h1>
    </div>

    <div class="content">
        <h2>Stay Informed</h2>
        <div class="newsfeed-container">
            <form method="GET" action="" class="search-bar">
                <div class="form-group" style="display: flex; align-items: center;">
                    <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                </div>
            </form>

            <?php if (empty($posts)): ?>
                <p>No posts found.</p>
            <?php else: ?>
                <?php foreach ($posts as $row): ?>
                    <div class="newsfeed-post">
                        <div class="post-header">
                            <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                            <p class="post-time"><?php echo htmlspecialchars(date("F j, Y g:ia", strtotime($row['upload_date']))); ?></p>
                        </div>
                        <?php if (!empty($row['image'])): ?>
                            <div class="post-image">
                                <?php
                                $imagePath = $row['image'];
                                $absolutePath = realpath($basePath . '/' . ltrim($imagePath, './'));
                                $webPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $absolutePath);

                                if ($absolutePath && file_exists($absolutePath)) {
                                    echo "<img src='$webPath' alt='Post Image'>";
                                } else {
                                    echo "<p>Image not found: $imagePath</p>";
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="post-details">
                            <p><?php echo htmlspecialchars($row['details']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>&date=<?php echo urlencode($dateFilter); ?>&sort=<?php echo $sortOrder; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <?php $conn->close(); ?>
    <button class="scroll-to-top" id="scrollToTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>
    <script>
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>