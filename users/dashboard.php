<?php 
include "includes/config.php";
include "plugins-header.php";

$query = "SELECT * FROM posts ORDER BY upload_date DESC";
$result = mysqli_query($conn, $query);

$locationQuery = "SELECT location, COUNT(*) AS count FROM tblcomplaints GROUP BY location";
$locationResult = $conn->query($locationQuery);

$locations = [];
if ($locationResult->num_rows > 0) {
    while ($row = $locationResult->fetch_assoc()) {
        $locations[] = [
            'location' => $row['location'],
            'count' => (int)$row['count']
        ];
    }
}

$locationsJson = json_encode($locations);
$googleApiKey = 'AIzaSyAgUzZvcyWFzeG2bY8qNctYWFgadxGah0M';
?>
<body>
    <?php include "header.php";?>
    <div class="container-fluid px-1 py-4">
        <div class="row row-gap-3 column-gap-2 mx-0 flex-wrap-reverse">
            <div class="col row row-gap-4 mx-0 overflow-y-auto" style="max-height: calc(100vh - 120px);">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white p-3 shadow-sm border rounded-2">
                    <div class="d-flex column-gap-3 mb-4">
                        <div>
                            <img src="../img/2.png" class="rounded-circle border" width="48" height="48">
                        </div>
                        <div>
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                            <small class="d-flex column-gap-3"><span><?php echo date('F j, Y', strtotime($row['upload_date'])); ?></span> <li><?php echo date('g:i a', strtotime($row['upload_date'])); ?></li></small>
                        </div>
                    </div>
                    <div>
                        <p><?php echo nl2br(htmlspecialchars($row['details'])); ?></p>
                        <?php if (!empty($row['image'])): ?>
                        <div class="w-100">
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" width="100%" height="auto">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>

<?php include "plugins-footer.php";?>
<style>.mx-0 {
    margin-right: 10 !important;
    margin-left: 10 !important;
}</style>