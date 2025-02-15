<?php
// Include database connection file
include('include/config.php');

// Get the search query from the URL
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Check if a query was entered
if ($query) {
    // Sanitize the search query to prevent XSS attacks
    $searchQuery = htmlspecialchars($query);

    // Prepare the SQL statement
    $sql = "SELECT * FROM admin WHERE 
            username LIKE ? OR 
            firstname LIKE ? OR 
            lastname LIKE ? OR 
            email LIKE ?";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$searchQuery%";
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the results
        $result = $stmt->get_result();

        // Check if any results were found
        if ($result->num_rows > 0) {
            echo "<h2>Search Results:</h2>";
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                // Display the results
                echo "<li>" . htmlentities($row['username']) . " - " . htmlentities($row['firstname']) . " " . htmlentities($row['lastname']) . " (" . htmlentities($row['email']) . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No results found for '<strong>" . htmlentities($searchQuery) . "</strong>'.</p>";
        }
    } else {
        echo "<p>Error executing query.</p>";
    }

    // Close connections
    $stmt->close();
    $conn->close();
} else {
    echo "<p>Please enter a search term.</p>";
}
?>
