<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['userId'];
    $location = mysqli_real_escape_string($conn, $_POST['locations']);
    $complain_details = mysqli_real_escape_string($conn, $_POST['description']);
    $compfile = $_FILES["docs"]["name"];
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;

    if ($anonymous) {
        $userId = NULL;
    }

    if ($compfile) {
        $target_dir = "complaintdocs/"; 
        $target_file = $target_dir . uniqid() . basename($compfile);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $mimeType = mime_content_type($_FILES["docs"]["tmp_name"]);

        $allowedImageTypes = ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'tiff'];
        $allowedVideoTypes = ['mp4', 'avi', 'mov'];

        if (in_array($fileType, $allowedImageTypes) || in_array($fileType, $allowedVideoTypes)) {
            if (move_uploaded_file($_FILES["docs"]["tmp_name"], $target_file)) {
                $endpoint = in_array($fileType, $allowedImageTypes) 
                    ? 'https://api.sightengine.com/1.0/check.json' 
                    : 'https://api.sightengine.com/1.0/video/check-sync.json';

                $params = array(
                    'media' => new CURLFile($target_file),
                    'models' => in_array($fileType, $allowedImageTypes) ? 'nudity-2.1,genai' : 'nudity-2.1',
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
                        echo '<script>alert("The image contains nudity and cannot be uploaded."); window.history.back();</script>';
                        unlink($target_file);
                        exit();
                    } elseif ($aiGenerated > 0.01) {
                        echo '<script>alert("The image is AI-generated and cannot be uploaded."); window.history.back();</script>';
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
                        echo '<script>alert("The video contains nudity and cannot be uploaded."); window.history.back();</script>';
                        unlink($target_file);
                        exit();
                    }
                }

                echo '<script>alert("File is clean and uploaded successfully.");</script>';
            } else {
                echo '<script>alert("File upload failed. Please try again."); window.history.back();</script>';
                exit();
            }
        } else {
            echo '<script>alert("Invalid file type. Only JPG, JPEG, PNG, WEBP, BMP, TIFF, MP4, AVI, MOV allowed."); window.history.back();</script>';
            exit();
        }
    }

    $complaint_number = 'CMP-' . time() . '-' . rand(1000, 9999);
    $complaint_file = $compfile ? $target_file : NULL;

    // Insert into tblcomplaints, allowing userId to be NULL for anonymous reports
    $stmt = $conn->prepare("INSERT INTO tblcomplaints (complaint_number, userId, location, complaint_details, complaint_file, registered_at, last_updated_at, anonymous) 
                            VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)");
    $stmt->bind_param('sisssi', $complaint_number, $userId, $location, $complain_details, $complaint_file, $anonymous);

    // Execute the statement and handle success/failure
    if ($stmt->execute()) {
        // Store the complaint number in session for anonymous reports
        if ($anonymous) {
            if (!isset($_SESSION['anonymous_complaint_ids'])) {
                $_SESSION['anonymous_complaint_ids'] = [];
            }
            $_SESSION['anonymous_complaint_ids'][] = $complaint_number;
        }
        echo '<script>alert("Your complaint has been successfully filed. Complaint Number: ' . $complaint_number . '"); window.location.href="dashboard.php";</script>';
    } else {
        echo '<script>alert("Failed to register complaint. Please try again."); window.history.back();</script>';
    }
}
?>

<?php include "plugins-header.php"; ?>
<body>
    <?php include "header.php"; ?>
    <div class="container-fluid px-1 py-4">
        <div class="row row-gap-3 mx-0 h-100" style="max-height: calc(100vh - 145px);">
            <div class="col-12 pb-4 h-100">
                <div class="bg-white p-3 shadow-sm border rounded-2 position-relative bg-danger">
                    <div>
                        <button data-bs-toggle="modal" data-bs-target="#report-modal" class="position-absolute" style="
                            background: #007bff;
                            color: white;
                            border: none;
                            border-radius: 50%;
                            width: 50px;
                            height: 50px;
                            font-size: 18px;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                            cursor: pointer !important;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            top: 22px;
                            right: 22px;
                        " onclick="locateMe()">📍</button>
                    </div>
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.3275990994407!2d121.05137887458613!3d14.75056428575377!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b03dd2641cfd%3A0x438c583c22de5bdd!2sCaloocan%20City%20Hall%20North!5e0!3m2!1sen!2sph!4v1742112468901!5m2!1sen!2sph" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="report-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="position-relative">
                    <div role="button" class="position-absolute top-0 end-0 pt-3 pe-3 fs-4 text-danger" data-bs-dismiss="modal">
                        <i class="ri-close-fill"></i>
                    </div>
                    <h5 class="page-title py-3 px-4">Register Complaint</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" class="reminders-form d-flex flex-column row-gap-4 pb-2 px-4" id="complaintForm">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous">
                            <label class="form-check-label" for="anonymous">Report Anonymously</label>
                        </div>
                        <div class="form-floating">
                            <input required type="text" name="locations" id="locations" class="form-control rounded-1" placeholder="input locations">
                            <label for="locations">Select Location</label>
                        </div>
                        <div class="form-floating w-100">
                            <textarea required type="text" name="description" id="description" class="form-control rounded-1" placeholder="input description" style="height: 100px;"></textarea>
                            <label for="description">Complaint Details</label>
                        </div>
                        <div class="form-floating">
                            <input required type="file" name="docs" id="docs" class="form-control rounded-1" placeholder="upload docs" accept="image/jpeg,image/png,image/webp,image/bmp,image/tiff,video/mp4,video/avi,video/quicktime">
                            <label for="docs">Upload Evidence</label>
                            <p id="scanResult" style="color: red; display: none;">Scanning...</p>
                        </div>
                        <button type="submit" class="save-button btn btn-primary rounded-1 w-100" id="submitBtn" disabled>Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include "plugins-footer.php"; ?>
</body>

<script>
function locateMe() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            const mapFrame = document.querySelector('iframe');
            const mapSrc = `https://www.google.com/maps/embed/v1/place?key=AIzaSyAgUzZvcyWFzeG2bY8qNctYWFgadxGah0M&q=${lat},${lng}`;
            mapFrame.src = mapSrc;

            const apiKey = "AIzaSyAgUzZvcyWFzeG2bY8qNctYWFgadxGah0M";
            const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`;

            fetch(geocodeUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "OK") {
                        const location = data.results[0].formatted_address;
                        document.getElementById("locations").value = location;

                        const reportModal = new bootstrap.Modal(document.getElementById('report-modal'));
                        reportModal.show();
                    } else {
                        alert("Failed to retrieve location details. Please try again.");
                    }
                })
                .catch(() => alert("Error connecting to Google Maps API."));
        }, error => {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("Location access denied by user.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("Location request timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
            }
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

document.getElementById('docs').addEventListener('change', function(event) {
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