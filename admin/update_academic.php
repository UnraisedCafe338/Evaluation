<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["academic_id"])) {
    include('../connection.php');
    if (!$connection) {
        die("Failed to connect to the database: " . mysqli_connect_error());
    }
    $year = mysqli_real_escape_string($connection, $_POST["year"]);
    $semester = mysqli_real_escape_string($connection, $_POST["semester"]);
    $status = mysqli_real_escape_string($connection, $_POST["status"]);

    $update_query = "UPDATE academic_list SET year=?, semester=?, status=? WHERE id=?";
    $statement = mysqli_prepare($connection, $update_query);
   
    mysqli_stmt_bind_param($statement, "sssi", $year, $semester, $status, $id);
    
    if (mysqli_stmt_execute($statement)) {
        echo "Academic year updated successfully.";
    } else {
        echo "Error updating academic year: " . mysqli_error($connection);
    }
    mysqli_stmt_close($statement);
    mysqli_close($connection);
} else {
  
    echo "Invalid request.";
}
?>
