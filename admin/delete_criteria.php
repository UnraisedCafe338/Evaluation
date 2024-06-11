<?php

include('../connection.php');
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
if (isset($_GET['action']) && isset($_GET['criteria_id'])) {
    $action = $_GET['action'];
    $criteria_id = $_GET['criteria_id'];

    if ($action === 'delete') {
        
        $delete_questions_query = "DELETE FROM question_list WHERE criteria_id = $criteria_id";
        $result_delete_questions = $connection->query($delete_questions_query);

        $delete_criteria_query = "DELETE FROM criteria WHERE criteria_id = $criteria_id";
        $result_delete_criteria = $connection->query($delete_criteria_query);

        if ($result_delete_criteria) {
           
            $reset_query = "ALTER TABLE criteria AUTO_INCREMENT = 1";
            $connection->query($reset_query);
            
            echo "Criteria deleted successfully.";
            header("Location: criteria_list.php");
        } else {
            echo "Error deleting criteria: " . $connection->error;
        }
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Invalid action or criteria ID.";
}

$connection->close();

?>
