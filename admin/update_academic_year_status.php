<?php
include('../connection.php');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id']) && isset($_GET['default'])) {
    $academicYearId = $_GET['id'];
    $default = $_GET['default'];

    // Update default status for the academic year being toggled
    $updateDefaultQuery = "UPDATE academic_list SET default_select = $default WHERE id = $academicYearId";
    $updateDefaultResult = mysqli_query($connection, $updateDefaultQuery);

    if (!$updateDefaultResult) {
        die("Error updating default status: " . mysqli_error($connection));
    }

    if ($default == 1) {
        // Set status to 2 (On-going) for the academic year being toggled to Yes
        $updateStatusQuery = "UPDATE academic_list SET status = 2 WHERE id = $academicYearId";
        $updateStatusResult = mysqli_query($connection, $updateStatusQuery);

        if (!$updateStatusResult) {
            die("Error updating status: " . mysqli_error($connection));
        }

        // Set default_select to 0 (No) for all other academic years
        $updateOtherDefaultsQuery = "UPDATE academic_list SET default_select = 0 WHERE id != $academicYearId";
        $updateOtherDefaultsResult = mysqli_query($connection, $updateOtherDefaultsQuery);

        if (!$updateOtherDefaultsResult) {
            die("Error updating other default statuses: " . mysqli_error($connection));
        }

        // Set status to 0 (Finished) for the previously selected default academic year
        $updatePreviousStatusQuery = "UPDATE academic_list SET status = 0 WHERE default_select = 0 AND id != $academicYearId";
        $updatePreviousStatusResult = mysqli_query($connection, $updatePreviousStatusQuery);

        if (!$updatePreviousStatusResult) {
            die("Error updating previous status: " . mysqli_error($connection));
        }
    }

    echo "Status updated successfully";
} else {
    echo "Invalid parameters";
}
?>
