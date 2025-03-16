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
<!DOCTYPE html>
<html lang="en">
<head>
     <!-- App icon -->
     <link rel="shortcut icon" href="asset/images/ingat.ico"></head>
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
            <div class="col-12 col-lg-4 pb-4">
                <div class="bg-white p-3 shadow-sm border rounded-2">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include "plugins-footer.php";?>

<style>
    #map {
        width: 100%;
        height: 60vh; 
        min-height: 300px;
    }

    @media (max-width: 991px) {
        #map {
            height: 300px; 
        }
    }


    @media (min-width: 992px) {
        .col-lg-4 {
            height: calc(100vh - 120px);
        }
        .col-lg-4 .bg-white {
            height: 100%; 
        }
        #map {
            height: 100%; 
        }
    }
</style>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleApiKey; ?>"></script>
<script>
    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 14.750564, lng: 121.051379 },
        zoom: 12,
        mapTypeId: 'roadmap'
    });

    const locations = <?php echo $locationsJson; ?>;
    let markers = [];

    locations.forEach(location => {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: location.location }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const coords = results[0].geometry.location;

                const pinColor = location.count > 2 ? 'red' : (location.count === 2 ? 'yellow' : 'green');
                const icon = {
                    url: `https://maps.google.com/mapfiles/ms/icons/${pinColor}-dot.png`,
                    scaledSize: new google.maps.Size(32, 32)
                };

                const marker = new google.maps.Marker({
                    position: coords,
                    map: map,
                    icon: icon,
                    title: location.location
                });

                const infowindow = new google.maps.InfoWindow({
                    content: `<b>${location.location}</b><br>${location.count} incidents`
                });

                marker.addListener('click', () => {
                    infowindow.open(map, marker);
                });

                markers.push(marker);
            } else {
                console.error(`Geocode was not successful for the following reason: ${status}`);
            }
        });
    });
</script>