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
            z-index:2;
        }

        .leaflet-control-fullscreen a {
            background-color: #fff;
            border-radius: 4px;
            padding: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.4);
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


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.min.js"></script>

    <script>

        mapboxgl.accessToken = 'pk.eyJ1Ijoib3Jlb2hvbGljIiwiYSI6ImNtMWFwdnR6bzF2c2QycXM4aW54Nmkxa3MifQ.0YVnZngmFw98M9yv9ZfFRw';

 
        const map = L.map('map').setView([14.59, 121.02], 8);

 
        L.tileLayer(`https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=${mapboxgl.accessToken}`, {
            attribution: '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a>'
        }).addTo(map);


        map.addControl(new L.Control.FullScreen());


        L.control.zoom({
            position: 'topright'
        }).addTo(map);


        const locations = <?= $locationsJson; ?>;
        let markers = [];


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