<?php
// Include database configuration
include_once 'users/includes/config.php';

// Fetch and process data from tblcomplaints
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

// Convert PHP array to JSON for JavaScript
$locationsJson = json_encode($locations);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Map</title>

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="shortcut icon" href="users/asset/images/ingat.ico">
    <!-- Mapbox Script and Styles -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E1F7F5;
            color: #1E0342;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #0E46A3;
            padding: 15px 30px;
            color: white;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container img {
            height: 40px;
            margin-right: 10px;
        }

        .logo-container span {
            font-size: 24px;
            font-weight: bold;
        }

        .go-back-btn {
            background-color: #E1F7F5;
            color: #0E46A3;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            font-weight: bold;
        }

        .go-back-btn:hover {
            background-color: #9AC8CD;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #0E46A3;
        }

        .dropdown {
            text-align: center;
            margin: 20px auto;
        }

        .dropdown button {
            background-color: #0E46A3;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .dropdown button:hover {
            background-color: #1E0342;
            transform: scale(1.05);
        }

        #map {
            width: 90%;
            height: 500px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            #map {
                height: 400px;
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

    <h1>Crime Map</h1>

    <div class="dropdown">
        <button id="filter-high" data-risk="high">High</button>
        <button id="filter-moderate" data-risk="moderate">Moderate</button>
        <button id="filter-low" data-risk="low">Low</button>
    </div>

    <div id="map"></div>

 <?php include 'footer.php'; ?>


    <script>
        // Set Mapbox access token
        mapboxgl.accessToken = 'pk.eyJ1Ijoib3Jlb2hvbGljIiwiYSI6ImNtMWFwdnR6bzF2c2QycXM4aW54Nmkxa3MifQ.0YVnZngmFw98M9yv9ZfFRw';

        // Initialize map
        const map = L.map('map').setView([14.59, 121.02], 8);

        // Add Mapbox tiles
        L.tileLayer(`https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=${mapboxgl.accessToken}`).addTo(map);

        // Locations data from PHP
        const locations = <?= $locationsJson; ?>;
        let markers = [];

        // Function to add markers to map
        async function addMarkers(filteredLocations) {
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            for (let location of filteredLocations) {
                const coords = await fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(location.location)}.json?access_token=${mapboxgl.accessToken}`)
                    .then(response => response.json())
                    .then(data => data.features.length > 0 ? data.features[0].center : null)
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

                    const marker = L.marker([coords[1], coords[0]], { icon })
                        .bindPopup(`<b>${location.location}</b><br>${location.count} incidents`)
                        .addTo(map);

                    markers.push(marker);
                }
            }
        }

        // Add markers initially
        addMarkers(locations);

        // Filter buttons
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
