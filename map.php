<?php
include_once 'users/includes/config.php';

$query = "SELECT location, COUNT(*) AS count FROM tblcomplaints GROUP BY location";
$result = $conn->query($query);

$locations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = [
            'location' => $row['location'],
            'count' => (int)$row['count']
        ];
    }
}

$locationsJson = json_encode($locations);
$googleApiKey = getenv('GOOGLE_MAPS_API_KEY');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Map</title>

    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $googleApiKey; ?>"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="users/asset/images/ingat.ico">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E1F7F5;
            color: #1E0342;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1 0 auto;
            padding: 60px 0;
            width: 100%;
            margin: 0 auto;
        }

        .content h2 {
            text-align: center;
            text-transform: uppercase;
            font-size: 1.5rem;
            color: #333;
            margin-left: 39%;
            margin-top: 3%;
            margin-bottom: 20px;
            border-bottom: 2px solid #28a745;
            display: inline-block;
        }

        .dropdown {
            text-align: center;
            margin: 20px auto;
            position: relative;
            z-index: 3;
        }

        .dropdown button {
            background: linear-gradient(135deg, #0E46A3, #1E0342);
            color: #fff;
            border: none;
            padding: 12px 24px;
            margin: 0 10px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .dropdown button:hover {
            background: #28a745;
            transform: translateY(-2px);
        }

        .background-shadow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('users/asset/images/flag_police_badge.png') no-repeat center center;
            background-size: contain;
            opacity: 0.08;
            z-index: 1;
        }

        #map {
            width: 90vw;
            max-width: none;
            height: 700px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        @media (max-width: 768px) {
            #map {
                width: 95vw;
                height: 500px;
            }

            .dropdown button {
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="content">
        <h2>Filter Crime Incidents</h2>
        <div class="dropdown">
            <button id="filter-high" data-risk="high">High</button>
            <button id="filter-moderate" data-risk="moderate">Moderate</button>
            <button id="filter-low" data-risk="low">Low</button>
        </div>

        <div id="map"></div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        const map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 14.59, lng: 121.02 },
            zoom: 8,
            mapTypeId: 'roadmap'
        });

        const locations = <?= $locationsJson; ?>;
        let markers = [];

        async function addMarkers(filteredLocations) {

            markers.forEach(marker => marker.setMap(null));
            markers = [];

            for (let location of filteredLocations) {
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
            }
        }

        addMarkers(locations);

        document.querySelectorAll('.dropdown button').forEach(button => {
            button.addEventListener('click', async () => {
                const riskLevel = button.getAttribute('data-risk');
                let filteredLocations = [];

                if (riskLevel === 'high') {
                    filteredLocations = locations.filter(location => location.count > 2);
                } else if (riskLevel === 'moderate') {
                    filteredLocations = locations.filter(location => location.count === 2);
                } else if (riskLevel === 'low') {
                    filteredLocations = locations.filter(location => location.count === 1);
                }

                await addMarkers(filteredLocations);
            });
        });
    </script>
</body>

</html>