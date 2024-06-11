<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $questionID = $_POST['question_id'];
    $action = $_POST['action'];
    
    include('../connection.php');
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($action == 'edit') {
            $editedQuestion = $_POST['edited_question'];
            $stmt = $pdo->prepare("UPDATE question_list SET question_text = :editedQuestion WHERE question_id = :questionID");
            $stmt->bindParam(':editedQuestion', $editedQuestion);
            $stmt->bindParam(':questionID', $questionID);
            $stmt->execute();
            echo "Question updated successfully.";
        } elseif ($action == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM question_list WHERE question_id = :questionID");
            $stmt->bindParam(':questionID', $questionID);
            $stmt->execute();
            echo "Question deleted successfully.";
        }
        
        $criteriaID = $_POST['criteria_id'];
        header("Location: manage_questions.php?criteria_id=$criteriaID");
        exit();
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage();
    }
}
?>