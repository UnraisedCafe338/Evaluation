<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Evaluation Question Management</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    tr > td.id-column { width: 10% !important; }
    .edit-delete-btns { text-align: right; }
    .expand-input { width: auto; height: auto; box-sizing: border-box; word-wrap: break-word; overflow: hidden; resize: vertical; padding: 8px; }
    .question-button { background-color: darkblue; min-width: 120px; margin-right: 0px; margin-left: -10px; padding-left: 15px; border-radius: 10px; }
    .content { margin-left: 240px; margin-bottom: 100px; padding: 20px; z-index: -1; }
    .back-button { background-color: #0055ff; color: rgb(255, 255, 255); padding: 9px 5px; border: none; cursor: pointer; border-radius: 5px; text-decoration: none; position: relative; top: 2px; z-index: 0; }
    .back-button:hover { background-color: #0026d0f8; }
    table { width: 100% !important; table-layout: fixed !important; }
    .num-column { width: 10%; }
    .name-column { width: 70%; }
    .action-column { 
      
      width: 30%; 
    }
    .box-header { width: 100% !important; height: 100px !important; margin-top: 30px !important; }
    .add-question-popup { display: none; position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); background-color: white; padding: 10px; border-radius: 5px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); width: 30%; }
    .add-question-popup .addquesttable { margin-top: -90px; }
    .addstudtable td { border: 0px; }
    .overlay2 { display: none; position: fixed; top: 0; left: 0; width: 200%; height: 100%; background-color: rgba(0, 0, 0, 0.5); }
    .close-btn { position: absolute; top: 10px; right: 10px; cursor: pointer; font-size: 20px; color: #aaa; }
    .close-btn:hover { color: #000; }
    .box-header {
       width: 1210px !important; 
      display: flex;
      flex-direction: column;
      margin-right: -60px;
    }
    .box-body { width: 1210px !important;
       height: auto !important;
      margin-right: -60px;
      }
    .question-input {
  box-sizing: border-box;
  padding: 20px 25px;
  font-size: small;
    }
  .back-button{
    margin: -110px 5px 0px 1000px;
    padding: 10px 20px 10px 20px!important;}
  .addquestbutton{
    padding: 11px 20px 11px 20px!important;
  }
  .criteria-name h2{
    border-color: black!important;
    border: 5px!important;
    margin-right: 0px;
    text-align: left!important;
   
  }

  </style>
  
</head>
<body>
  <?php include 'sidebar.php'; ?>
  <div class="content">
    <h1>Evaluation Question Management</h1><br><br><br>

    <?php
      if (isset($_GET['criteria_id'])) {
        $criteria_id = $_GET['criteria_id'];
        include('../connection.php');
        if ($connection) {
          $query = "SELECT * FROM criteria WHERE criteria_id = $criteria_id";
          $result = mysqli_query($connection, $query); 
          if ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='box-header'>";
            echo "<div class='criteria-name'>";
            echo "<h2 id=\"criteria_name\">Managing Questions for Criteria: <span id=\"editable_criteria_name\">{$row['criteria_name']}</span> <button onclick=\"toggleEditCriteriaName()\"><i class=\"fa fa-edit\"></i></button></h2>";
            echo "<a href='criteria_list.php' class='back-button'><i class='fas fa-arrow-left'>&nbsp;&nbsp;back</i></a>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='addquestbutton' onclick='toggleAddQuestionPopup()'><i class='fas fa-plus'>&nbsp;&nbsp;New Question</i></button>";
            echo "</div>";
            echo "</div>";
            echo "<div class='box-body'>";
            echo "<h3>Question List</h3>";

            $query_questions = "SELECT question_id, question_text FROM question_list WHERE criteria_id = $criteria_id";
            $result_questions = mysqli_query($connection, $query_questions);
            if (mysqli_num_rows($result_questions) > 0) {
              echo "<table class='question-list'>";
              echo "<thead><tr><th class='num-column'>Question no.</th><th class='name-column'>Questions</th><th class='action-column'>Actions</th></tr></thead>";
              echo "<tbody>";
              $rowCount = 1;
              while ($row_question = mysqli_fetch_assoc($result_questions)) {
                echo "<tr>";
                echo "<td>{$rowCount}</td>";
                echo "<td id='question_text_{$row_question['question_id']}'>{$row_question['question_text']}</td>";
                echo "<td class='edit-delete-btns'>";
                echo "<button id='edit_button_{$row_question['question_id']}' onclick='toggleEditQuestion({$row_question['question_id']})'><i class='fa fa-edit'>&nbsp;</i></button>";
                echo "<form action='edit_delete_question.php' method='POST' style='display: inline;'>";
                echo "<input type='hidden' name='question_id' value='{$row_question['question_id']}'>";
                echo "<button type='submit' name='action' value='delete' onclick='reloadPage()'><i class='fa fa-trash'>&nbsp;</i></button>";
                echo "<input type='hidden' name='criteria_id' value='{$criteria_id}'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
                $rowCount++;
              }
              echo "</tbody>";
              echo "</table>";
            } else {
              echo "<h3>No questions found for this criteria.</h3>";
            }
            echo "</div>";
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

  <div class="overlay2" id="overlay2"></div>
  <div class="add-question-popup" id="addQuestionPopup">
    <span class="close-btn" onclick="toggleAddQuestionPopup()">&times;</span>
    <table class="addstudtable">
      <div class="addQuestion">
        <h3>Add New Question</h3>
        <form action="add_question.php" method="POST">
          <label for="new_question">New Question:</label>
          <input type="text" id="new_question" name="new_question" required class="expand-input"><br><br>
          <button type="submit">Add Question</button>
          <input type="hidden" name="criteria_id" value="<?php echo $criteria_id; ?>">&nbsp;&nbsp;&nbsp;
        </form>
      </div>
    </table>
  </div>
</body>
</html>
<script>
    function toggleEditCriteriaName() {
      var editableCriteriaName = document.getElementById('editable_criteria_name');
      var criteriaNameInput = document.createElement('input');
      criteriaNameInput.setAttribute('type', 'text');
      criteriaNameInput.setAttribute('id', 'criteria_name_input');
      criteriaNameInput.setAttribute('value', editableCriteriaName.textContent);
      criteriaNameInput.classList.add('expand-input');
      editableCriteriaName.parentNode.replaceChild(criteriaNameInput, editableCriteriaName);
      var editButton = document.querySelector('button[onclick="toggleEditCriteriaName()"]');
      editButton.innerHTML = '<i class="fa fa-check"></i>';
      editButton.setAttribute('onclick', 'updateCriteriaName()');
      criteriaNameInput.focus();
    }

    function updateCriteriaName() {
      var criteriaNameInput = document.getElementById('criteria_name_input');
      var newCriteriaName = criteriaNameInput.value;
      var criteriaId = <?php echo $criteria_id; ?>; // Assuming $criteria_id is available in the PHP context
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "update_criteria_name.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
          var editableCriteriaName = document.createElement('span');
          editableCriteriaName.setAttribute('id', 'editable_criteria_name');
          editableCriteriaName.textContent = newCriteriaName;
          criteriaNameInput.parentNode.replaceChild(editableCriteriaName, criteriaNameInput);
          var editButton = document.querySelector('button[onclick="updateCriteriaName()"]');
          editButton.innerHTML = '<i class="fa fa-edit"></i>';
          editButton.setAttribute('onclick', 'toggleEditCriteriaName()');
        }
      };
      xhr.send("edited_criteria_name=" + newCriteriaName + "&criteria_id=" + criteriaId);
    }

    function toggleEditQuestion(questionID) {
  const questionText = document.getElementById('question_text_' + questionID);
  const questionTextWidth = questionText.offsetWidth;
  const questionInput = document.createElement('input');
  questionInput.setAttribute('type', 'text');
  questionInput.setAttribute('id', 'question_input_' + questionID);
  questionInput.setAttribute('value', questionText.textContent);
  questionInput.classList.add('question-input');
  questionInput.style.width = questionTextWidth + 'px';
  questionText.parentNode.replaceChild(questionInput, questionText);
  const editButton = document.getElementById('edit_button_' + questionID);
  editButton.innerHTML = '<i class="fa fa-check"></i>';
  editButton.setAttribute('onclick', 'updateQuestion(' + questionID + ')');
  questionInput.focus();
}

function updateQuestion(questionID) {
  const questionInput = document.getElementById('question_input_' + questionID);
  const newQuestionText = questionInput.value;
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "update_question.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      if (xhr.responseText === "success") {
        const questionText = document.createElement('td');
        questionText.setAttribute('id', 'question_text_' + questionID);
        questionText.textContent = newQuestionText;
        questionInput.parentNode.replaceChild(questionText, questionInput);
        const editButton = document.getElementById('edit_button_' + questionID);
        editButton.innerHTML = '<i class="fa fa-edit"></i>';
        editButton.setAttribute('onclick', 'toggleEditQuestion(' + questionID + ')');
      } else {
        alert("Failed to update question");
      }
    }
  };
  xhr.send("edited_question=" + newQuestionText + "&question_id=" + questionID);
}

    function toggleAddQuestionPopup() {
      var popup = document.getElementById("addQuestionPopup");
      var overlay = document.getElementById("overlay2");
      if (popup.style.display === "none" || popup.style.display === "") {
        popup.style.display = "block";
        overlay.style.display = "block";
      } else {
        popup.style.display = "none";
        overlay.style.display = "none";
      }
    }
  </script>
