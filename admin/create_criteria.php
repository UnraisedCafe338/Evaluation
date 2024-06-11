<?php
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_criteria_name = mysqli_real_escape_string($connection, $_POST['new_criteria_name']);
    $insert_query = "INSERT INTO criteria (criteria_name, 'status') VALUES ('$new_criteria_name', '1')";

    if ($connection->query($insert_query) === TRUE) {
        $reset_query = "SET @new_id := 0; UPDATE criteria SET criteria_id = @new_id := @new_id + 1";
        if ($connection->multi_query($reset_query) === TRUE) {
            echo "New criteria created successfully.";
            header("Location: criteria_list.php");
        } else {
            echo "Error resetting auto-increment value: ". $connection->error;
        }
    } else {
        echo "Error creating criteria: ". $connection->error;
    }
}

$connection->close();
?>