<?php
include('../connection.php');
if (!$connection) {
    die("Failed to connect to the database: " . mysqli_connect_error());
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["toggle_default_select"])) {
    $id = $_POST["academic_id"];

 
    $query = "SELECT default_select FROM academic_list WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

   
    $new_default_select = $row["default_select"] == 1 ? 0 : 1;

    
    $update_query = "UPDATE academic_list SET default_select=$new_default_select WHERE id=$id";
    mysqli_query($connection, $update_query);

    
    header("Location: academic_year.php");
    exit();
}


mysqli_close($connection);
?>
