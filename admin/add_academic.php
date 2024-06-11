<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $default_select = $_POST['default_select'];
    $status = $_POST['status'];

    
    include('../connection.php');
    if (!$connection) {
        die("Failed to connect to the database: " . mysqli_connect_error());
    }

    
    $query = "INSERT INTO academic_list (year, semester, default_select, status) VALUES ('$year', '$semester', '$default_select', '$status')";
    if (mysqli_query($connection, $query)) {
        
        header("Location: academic_year.php");
        exit(); 
    } else {
        echo "Error adding academic year: " . mysqli_error($connection);
    }

    
    mysqli_close($connection);
}
?>
