<?php
session_start();
include('includes/config.php');

date_default_timezone_set('Asia/Manila');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

$weapons_query = mysqli_query($conn, "SELECT id, weapon_type FROM weapons");
$weapons = mysqli_fetch_all($weapons_query, MYSQLI_ASSOC);

$crime_types_query = mysqli_query($conn, "SELECT id, crime_type FROM crime_types");
$crime_types = mysqli_fetch_all($crime_types_query, MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    date_default_timezone_set('Asia/Manila');
    $userId = $_SESSION['userId'];
    $location = mysqli_real_escape_string($conn, $_POST['locations']);
    $complain_details = mysqli_real_escape_string($conn, $_POST['description']);
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $weapon_id = !empty($_POST['weapon']) ? intval($_POST['weapon']) : NULL;
    $crime_type_id = !empty($_POST['crime_type']) ? intval($_POST['crime_type']) : NULL;

    if ($anonymous) {
        $userId = NULL;
    }

    if (empty($_FILES["docs"]["name"][0])) {
        echo '<script>alert("Please upload at least one evidence file."); window.history.back();</script>';
        exit();
    }

    $fileCount = count($_FILES["docs"]["name"]);
    if ($fileCount > 3) {
        echo '<script>alert("Maximum of 3 files allowed."); window.history.back();</script>';
        exit();
    }

    $maxImageSize = 10 * 1024 * 1024; 
    $maxVideoSize = 50 * 1024 * 1024; 
    $target_dir = "complaintdocs/";
    $complaint_files = [];
    $allowedImageTypes = ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'tiff'];
    $allowedVideoTypes = ['mp4', 'avi', 'mov'];

    for ($i = 0; $i < $fileCount; $i++) {
        $compfile = $_FILES["docs"]["name"][$i];
        $fileSize = $_FILES["docs"]["size"][$i];
        $fileType = strtolower(pathinfo($compfile, PATHINFO_EXTENSION));

        if (in_array($fileType, $allowedImageTypes)) {
            if ($fileSize > $maxImageSize) {
                echo '<script>alert("Image file ' . htmlspecialchars($compfile) . ' exceeds 10 MB limit."); window.history.back();</script>';
                exit();
            }
        } elseif (in_array($fileType, $allowedVideoTypes)) {
            if ($fileSize > $maxVideoSize) {
                echo '<script>alert("Video file ' . htmlspecialchars($compfile) . ' exceeds 50 MB limit."); window.history.back();</script>';
                exit();
            }
        } else {
            echo '<script>alert("Invalid file type for ' . htmlspecialchars($compfile) . '. Only JPG, JPEG, PNG, WEBP, BMP, TIFF, MP4, AVI, MOV allowed."); window.history.back();</script>';
            exit();
        }

        $target_file = $target_dir . uniqid() . basename($compfile);
        $mimeType = mime_content_type($_FILES["docs"]["tmp_name"][$i]);

        if (move_uploaded_file($_FILES["docs"]["tmp_name"][$i], $target_file)) {
            $endpoint = 'https://api.sightengine.com/1.0/check.json';

            $params = array(
                'media' => new CURLFile($target_file),
                'models' => 'genai',
                'api_user' => '1403739033',
                'api_secret' => 'chirtiFq4wzTxdb2nEff32sGUKqezYwv',
            );

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                echo '<script>alert("API request failed for ' . htmlspecialchars($compfile) . ': ' . htmlspecialchars($curl_error) . '"); window.history.back();</script>';
                unlink($target_file);
                foreach ($complaint_files as $file) {
                    unlink($file);
                }
                exit();
            }

            $output = json_decode($response, true);
            file_put_contents('sightengine_log.txt', "File: $compfile\nResponse: " . json_encode($output) . "\n\n", FILE_APPEND);

            if (!isset($output['status']) || $output['status'] !== 'success' || !isset($output['type'])) {
                $errorMsg = isset($output['error']) ? $output['error'] : 'Invalid response structure';
                echo '<script>alert("Error: Invalid response from AI detection API for ' . htmlspecialchars($compfile) . '. Details: ' . htmlspecialchars($errorMsg) . '"); window.history.back();</script>';
                unlink($target_file);
                foreach ($complaint_files as $file) {
                    unlink($file);
                }
                exit();
            }

            $aiGenerated = isset($output['type']['ai_generated']) ? $output['type']['ai_generated'] : 0;
            $aiThreshold = 0.05; // 5% threshold, stricter than before

            if ($aiGenerated > $aiThreshold) {
                echo '<script>alert("File ' . htmlspecialchars($compfile) . ' is AI-generated (Confidence: ' . round($aiGenerated * 100, 2) . '%). Cannot be uploaded."); window.history.back();</script>';
                unlink($target_file);
                foreach ($complaint_files as $file) {
                    unlink($file);
                }
                exit();
            }

            $complaint_files[] = $target_file;
        } else {
            echo '<script>alert("File upload failed for ' . htmlspecialchars($compfile) . '. Please try again."); window.history.back();</script>';
            foreach ($complaint_files as $file) {
                unlink($file);
            }
            exit();
        }
    }
    $weapon_id = NULL;
    $custom_weapon = !empty($_POST['custom_weapon']) ? mysqli_real_escape_string($conn, $_POST['custom_weapon']) : NULL;
    
    // Handle weapon selection
    if (!empty($_POST['weapon'])) {
        if ($_POST['weapon'] === 'other' && !empty($custom_weapon)) {
            // Insert new weapon type into database
            $stmt = $conn->prepare("INSERT INTO weapons (weapon_type) VALUES (?)");
            $stmt->bind_param('s', $custom_weapon);
            if ($stmt->execute()) {
                $weapon_id = $stmt->insert_id;
            } else {
                echo '<script>alert("Failed to save custom weapon type. Please try again."); window.history.back();</script>';
                exit();
            }
        } else {
            $weapon_id = intval($_POST['weapon']);
        }
    }
    
    $complaint_number = 'CMP-' . time() . '-' . rand(1000, 9999);
    $complaint_file = implode(',', $complaint_files);

    $current_time = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO tblcomplaints (complaint_number, userId, location, complaint_details, complaint_file, registered_at, last_updated_at, anonymous, weapon_id, crime_type_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sisssssiii', $complaint_number, $userId, $location, $complain_details, $complaint_file, $current_time, $current_time, $anonymous, $weapon_id, $crime_type_id);
    if ($stmt->execute()) {
        if ($anonymous) {
            if (!isset($_SESSION['anonymous_complaint_ids'])) {
                $_SESSION['anonymous_complaint_ids'] = [];
            }
            $_SESSION['anonymous_complaint_ids'][] = $complaint_number;
        }
        $_SESSION['latest_complaint_number'] = $complaint_number; 
        echo '<script>alert("Your complaint has been successfully filed. Complaint Number: ' . $complaint_number . '"); window.location.href="status.php";</script>';
    } else {
        echo '<script>alert("Failed to register complaint. Please try again."); window.history.back();</script>';
        foreach ($complaint_files as $file) {
            unlink($file);
        }
    }
}
?>

<style>
label[for="crime_type"],
label[for="weapon"] {
    margin-left: 10px;
}
</style>

<?php include "plugins-header.php"; ?>
<body>
    <?php include "header.php"; ?>
    <div class="container-fluid px-1 py-4">
        <div class="row row-gap-3 mx-0 h-100" style="max-height: calc(100vh - 145px);">
            <div class="col-12 pb-4 h-100">
                <div class="bg-white p-3 shadow-sm border rounded-2 position-relative">
                    <h5 class="page-title py-2 px-3">Register Complaint</h5>
                    <form method="POST" action="" enctype="multipart/form-data" class="reminders-form d-flex flex-column row-gap-4 p-4" id="complaintForm" style="height: calc(100% - 50px); overflow-y: auto;">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous">
                            <label class="form-check-label" for="anonymous">Report Anonymously</label>
                        </div>
                        <div class="form-floating position-relative">
                            <input required type="text" name="locations" id="locations" class="form-control rounded-1" placeholder="input locations">
                            <label for="locations">Location</label>
                            <small id="locationMessage" class="form-text text-muted d-block mt-1">Please turn on your device location to auto-fill this field.</small>
                            <button type="button" id="getLocationBtn" class="btn btn-outline-primary btn-sm mt-2">Get Location</button>
                        </div>
                        <div class="row row-gap-4">
                            <div class="col-12 col-md-6 form-floating position-relative">
                                <select name="crime_type" id="crime_type" class="form-control rounded-1" required>
                                    <option value="">Select Incidents</option>
                                    <?php foreach ($crime_types as $crime): ?>
                                        <option value="<?php echo $crime['id']; ?>"><?php echo htmlspecialchars($crime['crime_type']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="crime_type">Incidents</label>
                            </div>
                            <div class="col-12 col-md-6 form-floating position-relative">
                    <select name="weapon" id="weapon" class="form-control rounded-1">
                        <option value="">Select Weapon (if any)</option>
                        <?php foreach ($weapons as $weapon): ?>
                            <option value="<?php echo $weapon['id']; ?>"><?php echo htmlspecialchars($weapon['weapon_type']); ?></option>
                        <?php endforeach; ?>
                        <option value="other">Other (specify below)</option>
                    </select>
                    <label for="weapon">Weapon Used</label>
                    <div id="customWeaponContainer" style="display: none; margin-top: 10px;">
                        <input type="text" name="custom_weapon" id="custom_weapon" class="form-control rounded-1" placeholder="Enter custom weapon type">
                 </div>
                <script>document.getElementById('weapon').addEventListener('change', function() {
                    const weaponSelect = this;
                    const customWeaponContainer = document.getElementById('customWeaponContainer');
                    
                    if (weaponSelect.value === 'other') {
                        customWeaponContainer.style.display = 'block';
                    } else {
                        customWeaponContainer.style.display = 'none';
                    }
                });</script>
                        </div>
                        <div class="form-floating w-100">
                            <textarea required type="text" name="description" id="description" class="form-control rounded-1" placeholder="input description" style="height: 150px;"></textarea>
                            <label for="description">Complaint Details</label>
                        </div>
                        <div class="form-floating">
                            <input required type="file" name="docs[]" id="docs" class="form-control rounded-1" placeholder="upload docs" accept="image/jpeg,image/png,image/webp,image/bmp,image/tiff,video/mp4,video/avi,video/quicktime" multiple>
                            <label for="docs">Upload Evidence (Max 3 files, Images: 10 MB, Videos: 50 MB)</label>
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
document.addEventListener('DOMContentLoaded', function() {
    const locationInput = document.getElementById('locations');
    const getLocationBtn = document.getElementById('getLocationBtn');
    const locationMessage = document.getElementById('locationMessage');
    const fileInput = document.getElementById('docs');
    const scanResult = document.getElementById('scanResult');
    const submitBtn = document.getElementById('submitBtn');
    const maxImageSize = 10 * 1024 * 1024; 
    const maxVideoSize = 50 * 1024 * 1024; 

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const apiKey = "AIzaSyAgUzZvcyWFzeG2bY8qNctYWFgadxGah0M";
                const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`;

                fetch(geocodeUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "OK") {
                            const location = data.results[0].formatted_address;
                            locationInput.value = location;
                            locationMessage.style.display = 'none';
                            getLocationBtn.style.display = 'none';
                            checkFormValidity();
                        } else {
                            locationMessage.textContent = 'Failed to get location. Please try again or enter manually.';
                            locationMessage.style.color = 'red';
                        }
                    })
                    .catch(() => {
                        locationMessage.textContent = 'Error connecting to location service. Please try again or enter manually.';
                        locationMessage.style.color = 'red';
                    });
            },
            error => {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        locationMessage.textContent = 'Location access denied. Enable location in settings or enter manually.';
                        locationMessage.style.color = 'red';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        locationMessage.textContent = 'Location unavailable. Please ensure location services are on or enter manually.';
                        locationMessage.style.color = 'red';
                        break;
                    case error.TIMEOUT:
                        locationMessage.textContent = 'Location request timed out. Try again or enter manually.';
                        locationMessage.style.color = 'red';
                        break;
                    case error.UNKNOWN_ERROR:
                        locationMessage.textContent = 'An error occurred. Please try again or enter manually.';
                        locationMessage.style.color = 'red';
                        break;
                }
            }
        );
    } else {
        locationMessage.textContent = 'Geolocation not supported. Please enter your location manually.';
        locationMessage.style.color = 'red';
        getLocationBtn.style.display = 'none';
    }

    getLocationBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            locationMessage.textContent = 'Fetching location...';
            locationMessage.style.color = 'orange';
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const apiKey = "AIzaSyAgUzZvcyWFzeG2bY8qNctYWFgadxGah0M";
                    const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`;

                    fetch(geocodeUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "OK") {
                                const location = data.results[0].formatted_address;
                                locationInput.value = location;
                                locationMessage.style.display = 'none';
                                getLocationBtn.style.display = 'none';
                                checkFormValidity();
                            } else {
                                locationMessage.textContent = 'Failed to get location. Please try again or enter manually.';
                                locationMessage.style.color = 'red';
                            }
                        })
                        .catch(() => {
                            locationMessage.textContent = 'Error connecting to location service. Please try again or enter manually.';
                            locationMessage.style.color = 'red';
                        });
                },
                error => {
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            locationMessage.textContent = 'Location access denied. Enable location in settings or enter manually.';
                            locationMessage.style.color = 'red';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            locationMessage.textContent = 'Location unavailable. Please ensure location services are on or enter manually.';
                            locationMessage.style.color = 'red';
                            break;
                        case error.TIMEOUT:
                            locationMessage.textContent = 'Location request timed out. Try again or enter manually.';
                            locationMessage.style.color = 'red';
                            break;
                        case error.UNKNOWN_ERROR:
                            locationMessage.textContent = 'An error occurred. Please try again or enter manually.';
                            locationMessage.style.color = 'red';
                            break;
                    }
                }
            );
        }
    });

    fileInput.addEventListener('change', function(event) {
        const files = event.target.files;
        const maxFiles = 3;

        if (files.length === 0) {
            scanResult.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }

        if (files.length > maxFiles) {
            scanResult.style.display = 'block';
            scanResult.innerText = 'Maximum of 3 files allowed.';
            scanResult.style.color = 'red';
            submitBtn.disabled = true;
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileType = file.type.split('/')[0]; 
            const fileSize = file.size;

            if (fileType === 'image' && fileSize > maxImageSize) {
                scanResult.style.display = 'block';
                scanResult.innerText = `Image file ${file.name} exceeds 10 MB limit.`;
                scanResult.style.color = 'red';
                submitBtn.disabled = true;
                return;
            } else if (fileType === 'video' && fileSize > maxVideoSize) {
                scanResult.style.display = 'block';
                scanResult.innerText = `Video file ${file.name} exceeds 50 MB limit.`;
                scanResult.style.color = 'red';
                submitBtn.disabled = true;
                return;
            }
        }

        scanResult.style.display = 'block';
        scanResult.innerText = 'Scanning for AI-generated content...';
        scanResult.style.color = 'orange';
        submitBtn.disabled = true;

        let cleanFiles = 0;
        let totalFiles = files.length;

        for (let i = 0; i < totalFiles; i++) {
            const file = files[i];
            const formData = new FormData();
            formData.append('media', file);
            formData.append('models', 'genai');
            formData.append('api_user', '1403739033');
            formData.append('api_secret', 'chirtiFq4wzTxdb2nEff32sGUKqezYwv');
            const endpoint = 'https://api.sightengine.com/1.0/check.json';

            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log(`API Response for ${file.name}:`, data);
                const aiGenerated = data.type && data.type.ai_generated ? data.type.ai_generated : 0;
                const aiThreshold = 0.05; // 5% threshold

                if (aiGenerated > aiThreshold) {
                    scanResult.innerText = `Warning! AI-generated content detected in ${file.name}: ${(aiGenerated * 100).toFixed(2)}%`;
                    scanResult.style.color = 'red';
                    submitBtn.disabled = true;
                } else {
                    cleanFiles++;
                    updateScanResult(cleanFiles, totalFiles);
                }
            })
            .catch(error => {
                scanResult.innerText = `Error scanning file ${file.name}. Please try again.`;
                scanResult.style.color = 'red';
                submitBtn.disabled = true;
                console.error('Error:', error);
            });
        }
    });

    function updateScanResult(cleanFiles, totalFiles) {
        if (cleanFiles === totalFiles) {
            scanResult.innerText = 'All files are clean (not AI-generated). You can submit.';
            scanResult.style.color = 'green';
            checkFormValidity();
        } else {
            scanResult.innerText = `Scanning... (${cleanFiles}/${totalFiles} files clean)`;
            scanResult.style.color = 'orange';
        }
    }

    function checkFormValidity() {
        const location = document.getElementById('locations').value.trim();
        const description = document.getElementById('description').value.trim();
        const fileInput = document.getElementById('docs');
        const scanResult = document.getElementById('scanResult');
        const submitBtn = document.getElementById('submitBtn');

        if (location && description && fileInput.files.length > 0 && fileInput.files.length <= 3 && scanResult.innerText === 'All files are clean (not AI-generated). You can submit.') {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    document.getElementById('complaintForm').addEventListener('input', checkFormValidity);
});
</script>
</html>