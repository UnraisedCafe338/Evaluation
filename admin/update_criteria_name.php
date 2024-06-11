<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edited_criteria_name'], $_POST['criteria_id'])) {

    include('../connection.php');

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $edited_criteria_name = mysqli_real_escape_string($connection, $_POST['edited_criteria_name']);
    $criteria_id = $_POST['criteria_id'];
    
    $update_query = "UPDATE criteria SET criteria_name = '$edited_criteria_name' WHERE criteria_id = $criteria_id";
    
    if ($connection->query($update_query) === TRUE) {
        echo "Criteria name updated successfully.";
    } else {
        echo "Error updating criteria name: " . $connection->error;
    }

    $connection->close();
} else {
    echo "Invalid request.";
}
?>
