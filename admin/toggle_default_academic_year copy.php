<?php

include('../connection.php');

if (isset($_GET['id'])) {
    $academicYearId = $_GET['id'];
    $updateAllQuery = "UPDATE academic_list SET default_select = 0 WHERE id != $academicYearId";
    $updateAllResult = mysqli_query($connection, $updateAllQuery);

    if ($updateAllResult) {

        $toggleQuery = "UPDATE academic_list SET default_select = NOT default_select WHERE id = $academicYearId";
        $toggleResult = mysqli_query($connection, $toggleQuery);

        if ($toggleResult) {
            echo "Successfully toggled default status.";
        } else {
            echo "Error toggling default status: " . mysqli_error($connection);
        }
    } else {
        echo "Error updating default status of academic years: " . mysqli_error($connection);
    }
} else {
    echo "Academic year ID not provided.";
}
?>
