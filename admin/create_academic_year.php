<?php
include('../connection.php');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $year = $_POST['year'] ?? '';
    $semester = $_POST['semester'] ?? '';

    if (empty($year) || empty($semester)) {
        echo "Please fill in all fields.";
        exit;
    }

   
    $insert_query = "INSERT INTO academic_list (year, semester, status) VALUES (?, ?, 1)";
    $stmt = mysqli_prepare($connection, $insert_query);
    mysqli_stmt_bind_param($stmt, "ss", $year, $semester);
    
    if (mysqli_stmt_execute($stmt)) {
       
        header("Location: academic_year.php");
        exit;
    } else {
        echo "Error creating academic year: " . mysqli_error($connection);
    }
}


mysqli_close($connection);
?>
