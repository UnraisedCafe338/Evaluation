<?php
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['teacher'], $data['totalScore'], $data['ratings'])) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid data provided."));
    exit();
}
$teacher = $data['teacher'];
$totalScore = $data['totalScore'];
$ratings = $data['ratings'];

include('../connection.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO evaluation_table (teacher, total_score, ratings) VALUES (:teacher, :totalScore, :ratings)");

    $stmt->bindParam(':teacher', $teacher);
    $stmt->bindParam(':totalScore', $totalScore);
    $stmt->bindParam(':ratings', json_encode($ratings)); // Store ratings as JSON

    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message" => "Evaluation data stored successfully."));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Error: " . $e->getMessage()));
}
?>
