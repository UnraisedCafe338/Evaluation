<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = $_POST['student_ID'];
    $columnName = $_POST['column_name'];
    $newValue = $_POST['new_value'];

    include('../connection.php');
    if ($connection) {

        $query = "UPDATE student_info SET $columnName = '$newValue' WHERE student_ID = $studentID";
        $result = mysqli_query($connection, $query);

        if ($result) {
            echo "Database updated successfully.";
        } else {
            echo "Error updating database: " . mysqli_error($connection);
        }
        mysqli_close($connection);
    } else {
        echo "Failed to connect to the database.";
    }
} else {
    echo "Invalid request method.";
}
?>
