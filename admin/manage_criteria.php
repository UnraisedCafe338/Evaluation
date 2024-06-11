<!DOCTYPE html>
<html lang="en">
<head>

</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
  <h1>Manage Criteria</h1><br><br>
  <?php
 
  if (isset($_GET['criteria_id'])) {
    $criteria_id = $_GET['criteria_id'];

    include('../connection.php');
 
   
    if ($connection) {
      $query = "SELECT * FROM criteria WHERE criteria_id = $criteria_id";
      $result = mysqli_query($connection, $query);
      if ($row = mysqli_fetch_assoc($result)) {
        echo "<h2>Managing Questions for: {$row['criteria_name']}</h2>";
        

        echo "<h3>Question List</h3>";
        $query_questions = "SELECT * FROM question_list WHERE criteria_id = $criteria_id";
        $result_questions = mysqli_query($connection, $query_questions);
        if (mysqli_num_rows($result_questions) > 0) {
          echo "<ul>";
          while ($row_question = mysqli_fetch_assoc($result_questions)) {
            echo "<li>{$row_question['question_text']}</li>";
          }
          echo "</ul>";
        } else {
          echo "<p>No questions found for this criteria.</p>";
        }

      } else {
        echo "<p>Criteria not found.</p>";
      }
      mysqli_close($connection);
    } else {
      echo "<p>Failed to connect to the database.</p>";
    }
  } else {
    echo "<p>No criteria selected.</p>";
  }
  ?>
</div>
</body>
</html>
