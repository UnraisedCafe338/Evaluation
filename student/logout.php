<?php
echo $_GET['studentId'];
session_start();

if (isset($_GET['studentId'])) {
    $studentId = $_GET['studentId'];
    
    include('../connection.php');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Use a prepared statement to delete the session records for the logged-in student
        $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE user_id = :studentId");
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Debug: Confirm the session deletion
            error_log("Successfully logged out student with ID: " . $studentId);
        } else {
            // Debug: Log an error if the deletion did not succeed
            error_log("Failed to log out student with ID: " . $studentId);
        }

        // Unset all session variables and destroy the session
        session_unset();
        session_destroy();

    } catch (PDOException $e) {
        // Debug: Log any exceptions thrown
        error_log("PDOException: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
        die();
    }
} else {
    // Debug: Log if there is no student ID in the session
    error_log("No student ID found in session.");
}

// Redirect to the login page after logout
header("Location: student_login.php");
exit();
?>
