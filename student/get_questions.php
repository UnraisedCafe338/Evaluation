<?php
include('../connection.php');
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT question_text, question_id FROM question_list");
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($questions);
} catch (PDOException $e) {
 
    echo "Connection failed: " . $e->getMessage();
}
?>
