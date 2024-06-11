<?php
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $enrollmentID = $_POST['enrollment_id'];


    $deleteQuery = "DELETE FROM enrollments WHERE EnrollmentID = '$enrollmentID'";
    $deleteResult = mysqli_query($connection, $deleteQuery);

    if ($deleteResult) {
        if(isset($_POST['studentID'])){
            $student_ID = $_POST['studentID'];

            header("Location: edit_student.php?student_ID=$student_ID");
            exit();
        }else{
            header("Location: edit_student.php");
            exit();
        }
    } else {
        echo "Error deleting enrolled subject: " . mysqli_error($connection);
    }
} else {
    header("Location: edit_student.php");
    exit();
}
?>
