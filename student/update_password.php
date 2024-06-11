<?php
session_start();

$host = 'localhost';
$dbname = 'evaluation_quiet';
$user = 'admin';
$pass = 'admin';

$connection = mysqli_connect($host, $user, $pass, $dbname);
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_SESSION['student_id'])) {
    $studentId = $_SESSION['student_id'];

    $query = "SELECT * FROM student_info WHERE student_ID = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $studentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
    } else {
        die("Error: Student information not found. Student ID: $studentId");
    }
} else {
    die("Error: Student ID not found in session.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_POST['current_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        $currentPassword = $_POST['current_password'];

    
        if ($currentPassword === $student['student_PASS']) {
            if ($newPassword === $confirmPassword) {
                
                $updateQuery = "UPDATE student_info SET student_PASS = ? WHERE student_ID = ?";
                $updateStmt = mysqli_prepare($connection, $updateQuery);
                mysqli_stmt_bind_param($updateStmt, "ss", $newPassword, $studentId);

                if (mysqli_stmt_execute($updateStmt)) {
                    $_SESSION['error_message'] = "Password updated successfully.";
                    header("Location: manage_password.php?studentId=" . $studentId);
                    exit();
                } else {
                    echo "Error updating password: " . mysqli_error($connection);
                }
            } else {
                $_SESSION['error_message'] = "Passwords do not match.";
                header("Location: manage_password.php?studentId=" . $studentId);
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Current password is incorrect.";
            header("Location: manage_password.php?studentId=" . $studentId);
            exit();
        }
    } else {
        $_SESSION['error_message'] = "New password, confirm password, or current password not provided.";
        header("Location: manage_password.php?studentId=" . $studentId);
        exit();
    }
} else {
    echo "Invalid request method.";
}

mysqli_close($connection);
?>
