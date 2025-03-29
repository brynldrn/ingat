<?php
include('include/config.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT badge_number, firstname, middlename, lastname, status FROM police WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        ?>
        <form method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-group">
                <label style="color:black">Badge Number</label>
                <input type="text" class="form-control" name="badge_number" value="<?= htmlentities($row['badge_number']) ?>" required>
            </div>
            <div class="form-group">
                <label style="color:black">First Name</label>
                <input type="text" class="form-control" name="firstname" value="<?= htmlentities($row['firstname']) ?>" required>
            </div>
            <div class="form-group">
                <label style="color:black">Middle Name (Optional)</label>
                <input type="text" class="form-control" name="middlename" value="<?= htmlentities($row['middlename']) ?>">
            </div>
            <div class="form-group">
                <label style="color:black">Last Name</label>
                <input type="text" class="form-control" name="lastname" value="<?= htmlentities($row['lastname']) ?>" required>
            </div>
            <div class="form-group">
                <label style="color:black">Password (Leave blank to keep unchanged)</label>
                <div class="password-container">
                    <input type="password" class="form-control" name="password" id="edit_password">
                    <i class="fa fa-eye" id="edit_togglePassword"></i>
                </div>
            </div>
            <div class="form-group">
                <input type="checkbox" name="status" id="edit_status" <?= $row['status'] ? 'checked' : '' ?>>
                <label for="edit_status" style="color:black">Active</label>
            </div>
            <button type="submit" class="btn btn-primary" name="update">Update Details</button>
        </form>
        <?php
    } else {
        echo '<div class="alert alert-danger">Police officer not found.</div>';
    }
    $stmt->close();
}
?>