<?php
include('../connection.php');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$academicYearId = $_GET['id'];


$delete_evaluation_query = "DELETE FROM evaluation_table WHERE academic_year = $academicYearId";
if (!mysqli_query($connection, $delete_evaluation_query)) {
    echo "Error deleting evaluations associated with academic year: " . mysqli_error($connection);
    exit(); 
}

$delete_academic_year_query = "DELETE FROM academic_list WHERE id = $academicYearId";
if (mysqli_query($connection, $delete_academic_year_query)) {
    echo "Academic year and associated evaluations deleted successfully.";
} else {
    echo "Error deleting academic year: " . mysqli_error($connection);
}

mysqli_close($connection);
?>
