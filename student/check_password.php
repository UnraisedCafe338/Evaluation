<?php
session_start();
error_log("check_password.php accessed."); 

include('../connection.php');
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_SESSION['student_id'])) {
    $studentId = $_SESSION['student_id'];

    $query = "SELECT student_PASS FROM student_info WHERE student_ID = '$studentId'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);
        $studentPassword = $row['student_PASS'];

       
        $currentPassword = $_POST['current_password'];
        error_log("Current Password from AJAX: " . $currentPassword);

        if ($currentPassword === $studentPassword) {
            echo "success";
        } else {
            echo "failure";
        }
    } else {
        
        echo "failure";
    }
} else {
   
    echo "failure";
}

mysqli_close($connection);
?>
