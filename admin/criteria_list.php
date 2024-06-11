<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Evaluation Question Management</title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* Button styles */
.button {
  background-color: #0055ff;
  color: rgb(255, 255, 255);
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}
.button-order-up {
  background-color: blue;
  color: rgb(255, 255, 255);
  padding: 1px 2px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}
.button-order-down {
  background-color: red;
  color: rgb(255, 255, 255);
  padding: 1px 2px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}
.button:hover {
  background-color: navy;
}
.button-order-up:hover {
  background-color: navy;
}
.button-order-down:hover {
  background-color: darkred;
}
.question-button {
  background-color: darkblue;
  min-width: 120px;
  margin-right: 0px;
  margin-left: -10px;
  padding-left: 15px;
  border-radius: 10px;
}

table {
  width: 100%!important;
}
.num-column {
  width: 10%;
}
.name-column {
  width: 70%;
}
.action-column {
  width: 20%;
  text-align: center;
}
.box-body {
  margin-right: -70px!important;
  width: 1210px!important;
  height: 63%!important;
  padding-bottom: 50px!important;
}
.box-header {
  margin-right: -70px!important;
  margin-top: -20px!important;
  width: 1210px!important;
  height: 30px!important;
}
.add-criteria-popup {
  display: none;
  position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  padding: 10px;
  border-radius: 5px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
  width: 30%;
}
.add-criteria-popup .addstudtable {
  margin-top: -90px;
}
.addcriteriatable td {
  border: 0px;
}
.overlay2 {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 200%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
  font-size: 20px;
  color: #aaa;
}
.box-header .addcriteriabutton {
  margin-left: 85%;
  margin-top: -20px;
}
.box-header h2 {
  margin-top: -30px;
}
.close-btn:hover {
  color: #000;
}
.row{
  text-align: center;
}
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
  <h1>Criteria Management</h1><br><br><br><br><br>
  
  <div class="box-header">
    <button class='addcriteriabutton' onclick='toggleAddCriteriaPopup()'><i class='fas fa-plus'>&nbsp;&nbsp;New Criteria</i></button>
    <h2>Criteria List</h2>
  </div>

  <div class="box-body">
    <table class="criteria">
      <thead>
        <tr>
          <th class="orderbuttons"></th>
          <th class="num-column">Criteria No.</th>
          <th class="name-column">Criteria Name</th>
          <th class="action-column">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include('../connection.php');
        if ($connection) {
          $query = "SELECT * FROM criteria ORDER BY criteria_order";
          $result = mysqli_query($connection, $query);
          $rowCount = 1;
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>";
            if ($rowCount > 1) {
              echo "<a href='reorder_criteria.php?action=move_up&criteria_id={$row['criteria_id']}' class='button-order-up'><i class='fas fa-arrow-up'></i></a>&nbsp;&nbsp;";
          }
          if ($rowCount < mysqli_num_rows($result)) {
              echo "<a href='reorder_criteria.php?action=move_down&criteria_id={$row['criteria_id']}' class='button-order-down'><i class='fas fa-arrow-down'></i></a></td>";
          }
            echo "<td>{$rowCount}";
            echo "</td>"; 
            echo "<td>&nbsp;<br>{$row['criteria_name']}<br>&nbsp;</td>";
            echo "<td>";
            echo "<a href='manage_questions.php?criteria_id={$row['criteria_id']}' class='button'><i class='fas fa-tasks'></i></a>&nbsp;&nbsp;";
            echo "<a href='#' onclick='confirmDelete({$row['criteria_id']})' class='button'><i class='fas fa-trash'></i></a>&nbsp;&nbsp;";

            echo "</td>";
            echo "</tr>";
            $rowCount++;
          }
          mysqli_close($connection);
        } else {
          echo "<tr><td colspan='3'>Failed to connect to the database.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  <div class="overlay2" id="overlay2"></div>
  <div class="add-criteria-popup" id="addCriteriaPopup">
    <span class="close-btn" onclick="toggleAddCriteriaPopup()">&times;</span>
    <h3 class="">Create New Criteria</h3>
    <form action="create_criteria.php" method="POST">
      <label for="new_criteria_name">Criteria Name:</label>
      <input type="text" id="new_criteria_name" name="new_criteria_name" required>
      <button type="submit">Create Criteria</button>
    </form>
  </div>
</div>
</body>
</html>

<script>
function toggleAddCriteriaPopup() {
  var popup = document.getElementById("addCriteriaPopup");
  var overlay = document.getElementById("overlay2");
  if (popup.style.display === "none" || popup.style.display === "") {
    popup.style.display = "block";
    overlay.style.display = "block";
  } else {
    popup.style.display = "none";
    overlay.style.display = "none";
  }
}

function confirmDelete(criteriaId) {
  if (confirm("Are you sure you want to delete this criteria?")) {
    window.location.href = 'delete_criteria.php?action=delete&criteria_id=' + criteriaId;
  }
}
</script>
