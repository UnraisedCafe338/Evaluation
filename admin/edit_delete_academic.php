<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST["edit_academic"])) {

        $id = $_POST["id"];
        $year = $_POST["year"];
        $semester = $_POST["semester"];
        $default_select = $_POST["default_select"];
        $status = $_POST["status"];

        include('../connection.php');
        if (!$connection) {
            die("Failed to connect to the database: " . mysqli_connect_error());
        }
        $query = "UPDATE academic_list SET year='$year', semester='$semester', default_select='$default_select', status='$status' WHERE id=$id";
        $result = mysqli_query($connection, $query);

        if ($result) {
            echo "Academic year updated successfully.";
        } else {
            echo "Error updating academic year: " . mysqli_error($connection);
        }

        mysqli_close($connection);
    } elseif(isset($_POST["delete_academic"])) {

        $id = $_POST["id"];
        $host = 'localhost';
        $dbname = 'evaluation_quiet';
        $user = 'admin';
        $pass = 'admin';

        $connection = mysqli_connect($host, $user, $pass, $dbname);
        if (!$connection) {
            die("Failed to connect to the database: " . mysqli_connect_error());
        }
        $query = "DELETE FROM academic_list WHERE id=$id";
        $result = mysqli_query($connection, $query);

        if ($result) {
            echo "Academic year deleted successfully.";
        } else {
            echo "Error deleting academic year: " . mysqli_error($connection);
        }
        mysqli_close($connection);
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid request.";
}
?>
