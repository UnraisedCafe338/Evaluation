<?php
include('../connection.php');
try {
    $data = json_decode(file_get_contents('php://input'), true);
    echo "Received data: " . print_r($data, true) . "<br>";

    $teacher = $data['teacher'];
    $total_score = $data['total_score'];
    $ratings = json_encode($data['ratings']);
    $studentId = $_GET['studentId'];
    $evaluationSql = "INSERT INTO evaluation_table (teacher, total_score, ratings, student_ID) VALUES (:teacher, :total_score, :ratings, :studentId)";
    echo "Evaluation SQL query: $evaluationSql <br>";

    $updateStudentSql = "UPDATE student_info SET evaluated = 1 WHERE student_ID = :studentId";
    echo "Update student info SQL query: $updateStudentSql <br>";

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    $evaluationStmt = $pdo->prepare($evaluationSql);
    $evaluationStmt->bindParam(':teacher', $teacher);
    $evaluationStmt->bindParam(':total_score', $total_score);
    $evaluationStmt->bindParam(':ratings', $ratings);
    $evaluationStmt->bindParam(':studentId', $studentId);
    $evaluationStmt->execute();

    $updateStudentStmt = $pdo->prepare($updateStudentSql);
    $updateStudentStmt->bindParam(':studentId', $studentId);
    $updateStudentStmt->execute();

    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
