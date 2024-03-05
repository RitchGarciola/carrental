
<?php
// update-status.php
include('includes/config.php');
// Include your PDO connection file
//require_once 'pdo-connection.php'; // Replace with the actual filename

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the AJAX request
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Prepare the SQL statement
    $sql = "UPDATE `tblvehicles` SET `carStatus` = :status WHERE `id` = :id";

    // Use prepared statement to prevent SQL injection
    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Return a success message if the update was successful
        echo 'Status updated successfully';
    } else {
        // Return an error message if the update failed
        echo 'Error updating status';
    }
} else {
    // Return an error message for non-POST requests
    echo 'Invalid request method';
}
?>

