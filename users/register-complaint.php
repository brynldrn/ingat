<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userId = $_SESSION['userId'];
        $weaponType = mysqli_real_escape_string($conn, $_POST['weaponType']);
        $crimeType = mysqli_real_escape_string($conn, $_POST['crimeType']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $complain_details = mysqli_real_escape_string($conn, $_POST['complaindetails']);
        $compfile = $_FILES["compfile"]["name"];

        $weaponQuery = mysqli_query($conn, "SELECT id FROM weapons WHERE weapon_type = '$weaponType'");
        $weaponRow = mysqli_fetch_assoc($weaponQuery);
        $weaponId = $weaponRow['id'];

        $crimeQuery = mysqli_query($conn, "SELECT id FROM crime_types WHERE crime_type = '$crimeType'");
        $crimeRow = mysqli_fetch_assoc($crimeQuery);
        $crimeId = $crimeRow['id'];

        if ($compfile) {
            $target_dir = "complaintdocs/";
            $target_file = $target_dir . uniqid() . basename($compfile);
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $mimeType = mime_content_type($_FILES["compfile"]["tmp_name"]);

     
            $allowedImageTypes = ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'tiff'];
            $allowedVideoTypes = ['mp4', 'avi', 'mov'];

            if (in_array($fileType, $allowedImageTypes) || in_array($fileType, $allowedVideoTypes)) {
                if (move_uploaded_file($_FILES["compfile"]["tmp_name"], $target_file)) {
                 
                    $endpoint = in_array($fileType, $allowedImageTypes) 
                        ? 'https://api.sightengine.com/1.0/check.json' 
                        : 'https://api.sightengine.com/1.0/video/check-sync.json';

                    $params = array(
                        'media' => new CURLFile($target_file),
                        'models' => in_array($fileType, $allowedImageTypes) ? 'nudity-2.1,genai' : 'nudity-2.1', // genai only for images
                        'api_user' => '1404146414',
                        'api_secret' => 'SNxrhUxrGT3MmEUHmHdfmjtoTTYrbnUr',
                    );

                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    $output = json_decode($response, true);

                 
                    if (in_array($fileType, $allowedImageTypes)) {
                        $nudityNone = isset($output['nudity']['none']) ? $output['nudity']['none'] : 0;
                        $aiGenerated = isset($output['type']['ai_generated']) ? $output['type']['ai_generated'] : 1;

                        if ($nudityNone < 0.99) {
                            echo '<script>alert("The image contains nudity and cannot be uploaded.");</script>';
                            unlink($target_file);
                            exit();
                        } elseif ($aiGenerated > 0.01) {
                            echo '<script>alert("The image is AI-generated and cannot be uploaded.");</script>';
                            unlink($target_file);
                            exit();
                        }
                    } else {
                        
                        $frames = $output['data']['frames'] ?? [];
                        $hasNudity = false;
                        foreach ($frames as $frame) {
                            if (isset($frame['nudity']['none']) && $frame['nudity']['none'] < 0.99) {
                                $hasNudity = true;
                                break;
                            }
                        }
                        if ($hasNudity) {
                            echo '<script>alert("The video contains nudity and cannot be uploaded.");</script>';
                            unlink($target_file);
                            exit();
                        }
                    }

                    echo '<script>alert("File is clean and uploaded successfully.");</script>';
                } else {
                    echo '<script>alert("File upload failed. Please try again.");</script>';
                    exit();
                }
            } else {
                echo '<script>alert("Invalid file type. Only JPG, JPEG, PNG, WEBP, BMP, TIFF, MP4, AVI, MOV allowed.");</script>';
                exit();
            }
        }

        $complaint_number = 'CMP-' . time() . '-' . rand(1000, 9999);
        $stmt = $conn->prepare("INSERT INTO tblcomplaints (complaint_number, userId, crime_type_id, weapon_id, location, complaint_details, complaint_file, registered_at, last_updated_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('siiisss', $complaint_number, $userId, $crimeId, $weaponId, $location, $complain_details, $target_file);
        $stmt->execute();

        echo '<script>alert("Your complaint has been successfully filed. Complaint Number: ' . $complaint_number . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register Complaint</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
#map {
    height: calc(100vh - 60px); 
    margin-top: 60px;
    margin-left: 285px;
}

@media (max-width: 768px) {
    #map {
        margin-left: 0;
        height: calc(100vh - 60px);
    }
}
.header{
    z-index: 1;
}
#main-content {
    padding: 0;
}

.leaflet-top.leaflet-right {
    top: 70px; 
    right: 20px; 
    z-index: 2000; 
    pointer-events: auto;
}

.locate-me-btn {
    background: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 18px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000; /* Ensure button is above everything else */
}

.locate-me-btn:hover {
    background: #0056b3;
}

.modal-header {
    background: #040073;
}

.mb-3 {
    padding-bottom: 10px;
}

.btn-close-large {
    background-color: transparent;
    border: none;
    font-size: 24px; 
    color: #fff; 
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2000;
}

.modal-content {
    margin-top: 70px;
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1040; 
}
</style>
</head>
<body>
<section id="container">
    <?php include("includes/header.php"); ?>
    <?php include("includes/sidebar.php"); ?>

    <section id="main-content">
        <div id="map"></div>

        <!-- Locate Me Button -->
        <div class="leaflet-top leaflet-right">
            <button class="locate-me-btn" onclick="locateMe()">📍</button>
        </div>

        <!-- Form Modal -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel">Register Complaint</h5>
                        <button type="button" class="btn-close-large" data-bs-dismiss="modal" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal style-form" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" id="location" name="location" class="form-control" required placeholder="Enter or select location">
                            </div>
                            <div class="mb-3">
                                <label for="weaponType" class="form-label">Weapon Type</label>
                                <select name="weaponType" id="weaponType" class="form-control select2" required>
                                    <option value="">Select Weapon Type</option>
                                    <?php
                                    $weaponQuery = mysqli_query($conn, "SELECT * FROM weapons");
                                    while ($weapon = mysqli_fetch_array($weaponQuery)) {
                                        echo '<option value="' . $weapon['weapon_type'] . '">' . $weapon['weapon_type'] . ' - ' . $weapon['details'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="crimeType" class="form-label">Crime Type</label>
                                <select name="crimeType" id="crimeType" class="form-control select2" required>
                                    <option value="">Select Crime Type</option>
                                    <?php
                                    $crimeQuery = mysqli_query($conn, "SELECT * FROM crime_types");
                                    while ($crime = mysqli_fetch_array($crimeQuery)) {
                                        echo '<option value="' . $crime['crime_type'] . '">' . $crime['crime_type'] . ' - ' . $crime['details'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="complaindetails" class="form-label">Complaint Details</label>
                                <textarea name="complaindetails" id="complaindetails" class="form-control" required rows="5" maxlength="2000"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="compfile" class="form-label">Upload Evidence</label>
                                <input type="file" name="compfile" id="compfile" class="form-control" accept="image/jpeg,image/png,image/webp,image/bmp,image/tiff,video/mp4,video/avi,video/quicktime">
                                <p id="scanResult" style="color: red; display: none;">Scanning...</p>
                            </div>
                            <div class="mb-3 text-end">
                                <button type="submit" id="submitBtn" name="submit" class="btn btn-primary" disabled>Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include("includes/footer.php"); ?>
</section>

<script>
document.getElementById('compfile').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const submitBtn = document.getElementById('submitBtn');
    const scanResult = document.getElementById('scanResult');

    if (!file) {
        scanResult.style.display = 'none';
        submitBtn.disabled = true;
        return;
    }

    scanResult.style.display = 'block';
    scanResult.innerText = 'Scanning...';
    scanResult.style.color = 'orange';
    submitBtn.disabled = true;

    const formData = new FormData();
    formData.append('media', file);
    const isImage = file.type.startsWith('image/');
    formData.append('models', isImage ? 'nudity-2.1,genai' : 'nudity-2.1');
    formData.append('api_user', '1404146414');
    formData.append('api_secret', 'SNxrhUxrGT3MmEUHmHdfmjtoTTYrbnUr');

    const endpoint = isImage 
        ? 'https://api.sightengine.com/1.0/check.json' 
        : 'https://api.sightengine.com/1.0/video/check-sync.json';

    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (isImage) {
            const nudityNone = data.nudity && data.nudity.none ? (data.nudity.none * 100) : 0;
            const aiGenerated = data.type && data.type.ai_generated ? (data.type.ai_generated * 100) : 100;

            if (nudityNone < 99) {
                scanResult.innerText = `Warning! Nudity detected: ${(100 - nudityNone).toFixed(2)}%`;
                scanResult.style.color = 'red';
                submitBtn.disabled = true;
            } else if (aiGenerated > 1) {
                scanResult.innerText = `Warning! AI-generated content: ${aiGenerated.toFixed(2)}%`;
                scanResult.style.color = 'red';
                submitBtn.disabled = true;
            } else {
                scanResult.innerText = 'File is clean. You can submit.';
                scanResult.style.color = 'green';
                submitBtn.disabled = false;
            }
        } else {
            const frames = data.data && data.data.frames ? data.data.frames : [];
            let hasNudity = false;
            for (const frame of frames) {
                if (frame.nudity && frame.nudity.none < 0.99) {
                    hasNudity = true;
                    break;
                }
            }
            if (hasNudity) {
                scanResult.innerText = 'Warning! Nudity detected in video.';
                scanResult.style.color = 'red';
                submitBtn.disabled = true;
            } else {
                scanResult.innerText = 'File is clean. You can submit.';
                scanResult.style.color = 'green';
                submitBtn.disabled = false;
            }
        }
    })
    .catch(error => {
        scanResult.innerText = 'Error scanning file. Please try again.';
        scanResult.style.color = 'red';
        submitBtn.disabled = true;
        console.error('Error:', error);
    });
});
</script>
    <!-- JS Scripts -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.11.0/mapbox-gl.js"></script>

    <script>
        mapboxgl.accessToken = 'pk.eyJ1Ijoib3Jlb2hvbGljIiwiYSI6ImNtMWFwdnR6bzF2c2QycXM4aW54Nmkxa3MifQ.0YVnZngmFw98M9yv9ZfFRw';

        var map = L.map('map').setView([0, 0], 2); // Default center

        // Add Mapbox GL JS tiles
        L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=' + mapboxgl.accessToken).addTo(map);

        var marker;

        // Reverse geocode to get location name
        function reverseGeocode(lat, lng) {
            var url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.features.length > 0) {
                        var locationName = data.features[0].place_name;
                        document.getElementById("location").value = locationName;
                        $('#formModal').modal('show'); // Show the form modal
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        
    // Function to handle "Locate Me" button click
    function locateMe() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                let lat = position.coords.latitude;
                let lng = position.coords.longitude;
                map.setView([lat, lng], 14);

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map);
                }

                reverseGeocode(lat, lng);
            });
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    }

    map.on('click', function (e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        if (marker) marker.remove();
        marker = L.marker([lat, lng]).addTo(map);

        reverseGeocode(lat, lng);
    });
    $('.btn-close').on('click', function () {
    $('#formModal').modal('hide');
});

    </script>
</body>
</html>