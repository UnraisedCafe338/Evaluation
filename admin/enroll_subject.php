<?php
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = $_POST['student_ID'];
    $student_ID = $_POST['studentID'];
    $subjectID = $_POST['subject'];
    echo "$student_ID<br>";
    echo "$subjectID";
    
    $checkQuery = "SELECT * FROM enrollments WHERE student_ID = '$student_ID' AND subject_code = '$subjectID'";
    $checkResult = mysqli_query($connection, $checkQuery);
    if(mysqli_num_rows($checkResult) > 0) {
        $message = "Selected subject is already enrolled to student.";
        header("Location: edit_student.php?student_ID=$student_ID&message=" . urlencode($message));
        exit();
    } else {
        $insertQuery = "INSERT INTO enrollments (student_ID, subject_code) VALUES ('$student_ID', '$subjectID')";
        $insertResult = mysqli_query($connection, $insertQuery);

        if ($insertResult) {
            header("Location: edit_student.php?student_ID=$student_ID&message=" . urlencode("Subject enrolled successfully."));
            exit();
        } else {
            $message = "Error: " . mysqli_error($connection);
            header("Location: edit_student.php?student_ID=$student_ID&message=" . urlencode($message));
            exit();
        }
    }
} else {
    header("Location: enrollment_page.php");
    exit();
}
?>
