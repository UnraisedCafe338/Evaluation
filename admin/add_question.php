<?php

include('../connection.php');

if (!$connection) {
    echo "Failed to connect to the database.";
    exit(); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $newQuestion = mysqli_real_escape_string($connection, $_POST['new_question']);
    $criteriaID = mysqli_real_escape_string($connection, $_POST['criteria_id']);
    
    $query = "INSERT INTO question_list (question_text, criteria_id) VALUES ('$newQuestion', '$criteriaID')";
    $result = mysqli_query($connection, $query);

    if ($result) {
        
        $resetQuery1 = "SET @new_id := 0";
        $resetResult1 = mysqli_query($connection, $resetQuery1);
    
        if ($resetResult1) {
            $resetQuery2 = "UPDATE question_list SET question_id = @new_id := @new_id + 1 ORDER BY question_id";
            $resetResult2 = mysqli_query($connection, $resetQuery2);
    
            if ($resetResult2) {
                header("Location: manage_questions.php?criteria_id=$criteriaID");
                exit(); 
            } else {
                echo "Error resetting auto-increment value.";
            }
        } else {
            echo "Error resetting auto-increment value.";
        }
    } else {
        error_log("Error adding new question: ". mysqli_error($connection));
        echo "An error occurred while processing your request. Please try again later.";
    }
}
?>