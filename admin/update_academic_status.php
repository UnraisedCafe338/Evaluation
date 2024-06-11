<?php

include('../connection.php');
if (!$connection) {
    die("Failed to connect to the database: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])) {
    $id = $_POST["academic_id"];
    $status = $_POST["status"];
    $update_query = "UPDATE academic_list SET status='$status' WHERE id=$id";
    if (mysqli_query($connection, $update_query)) {
        
        header("Location: academic_year.php");
        exit(); 
    } else {
        echo "Error updating status: " . mysqli_error($connection);
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])) {
    $id = $_POST["academic_id"];
    $status = $_POST["status"];
 
    echo "Received status: " . $status;
 
}


mysqli_close($connection);
?>
