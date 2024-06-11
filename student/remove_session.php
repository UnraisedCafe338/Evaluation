<?php
include('../connection.php');
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(isset($_SESSION['student_id'])) {
        
        $stmt_remove_session = $pdo->prepare("DELETE FROM `active_sessions` WHERE `user_id` = :student_id");
        $stmt_remove_session->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_STR);
        $stmt_remove_session->execute();

        echo 'Session removed from active_sessions table.';
    } else {
        echo 'Error: User not logged in.';
    }
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
