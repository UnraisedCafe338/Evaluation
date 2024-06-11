<?php
// fetch_student_details.php
include('../connection.php');
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['studentId'])) {
        $studentId = $_GET['studentId'];
        
        $stmt = $pdo->prepare("SELECT * FROM student_info WHERE student_ID = :studentId");
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            echo json_encode($student);
        } else {
            echo json_encode(["error" => "Student not found"]);
        }
    } else {
        echo json_encode(["error" => "Student ID not provided"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
