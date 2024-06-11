<?php
include('../connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $question_id = $_POST['question_id'];
  $edited_question = $_POST['edited_question'];

  $query = "UPDATE question_list SET question_text = ? WHERE question_id = ?";
  if ($stmt = $connection->prepare($query)) {
    $stmt->bind_param("si", $edited_question, $question_id);
    if ($stmt->execute()) {
      echo "success";
    } else {
      echo "error";
    }
    $stmt->close();
  } else {
    echo "error";
  }

  $connection->close();
}
?>
