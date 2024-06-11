<?php
include('../connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['student_ID'])) {
        $student_ID = $_POST['student_ID'];

        $deleteEnrollmentsQuery = "DELETE FROM enrollments WHERE student_ID = '$student_ID'";
        $deleteEnrollmentsResult = mysqli_query($connection, $deleteEnrollmentsQuery);

        if ($deleteEnrollmentsResult) {
            $deleteStudentQuery = "DELETE FROM student_info WHERE student_ID = '$student_ID'";
            $deleteStudentResult = mysqli_query($connection, $deleteStudentQuery);

            if ($deleteStudentResult) {
                $message = "Student successfully deleted.";
                header("Location: student_list.php?message=" . urlencode($message));
                exit();
            } else {
                echo "Error deleting student: " . mysqli_error($connection);
            }
        } else {
            echo "Error deleting enrollments: " . mysqli_error($connection);
        }
    }
} else {
    echo "Invalid request.";
}
mysqli_close($connection);
?>
