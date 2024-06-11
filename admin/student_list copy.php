<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Management</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    
    .content {
      margin-left: 20px;
      margin-bottom: 100px;
      padding: 20px;
      z-index: 0;
    }

    .actions-col {
      width: 150px;
      text-align: center;
    }
    .menu-btn {
      cursor: pointer;
      display: inline-block;
      padding: 5px;
    }
    .edit-delete-btns {
      display: inline-block;
    }
    .edit-delete-btns button {
      background-color: transparent;
      border: none;
      cursor: pointer;
      font-size: 14px;
       
    }
    .delete-button{
      margin-left: 100px;
      margin-right: -42px;
      margin-top: -80px;
      margin-bottom: 5px;
    }
    .edit-delete-btns button.delete-btn {
      color: red;
    }
    .edit-delete-btns button.delete-btn:hover {
      color: darkred; 
    }
    .students-button {
      background-color: darkblue;
      min-width: 120px; 
      margin-right: 0px;
      margin-left: -10px;
      padding-left: 15px;
      border-radius: 10px;
    }
    .content-addstud {
      background: linear-gradient(to bottom, rgb(66, 78, 255), rgb(49, 0, 208));
      width: 3000px;
      padding: 10px;
      position: fixed;
      margin-top: -100px;
      margin-left: -19px;
      color: white;
      bottom: 0px;
      border-top: 2px solid rgb(23, 0, 116);
      
    }
    .content-addstud::before {
      content: "";
      position: fixed;
      left: 50%;
      transform: translateX(-50%) translateY(-50%);
      width: 3000px;
      height: 3px;
      background-color: #ffee00;
      margin-bottom: 300px;
    }
    .search-container {
      background: linear-gradient(to top, rgb(66, 78, 255), rgb(49, 0, 208));
      width: 3000px;
      padding: 20px;
      position: fixed;
      margin-top: -30px;
      margin-left: -19px;
      color: white;
      top: 100px;
      border-bottom: 2px solid rgb(23, 0, 116);
    }
    .search-container::after {
      content: "";
      position: fixed;
      left: 50%;
      top: 130px;
      transform: translateX(-50%) translateY(-50%);
      width: 3000px;
      height: 5px;
      background-color: #ffdd00;
      margin-bottom: 100px;
    }
    .add-student-popup {
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
    .add-student-popup .addstudtable{
      margin-top: -90px;
    }
     .addstudtable td{
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
    .overlay2 h2{
      left: 100px;
    }
.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
  font-size: 20px;
  color: #aaa;
}

.close-btn:hover {
  color: #000; 
}
.overlay-box {
  top: 55%!important;
 
}
.overlay-box .addstudbutton {
  left: 100%;
}
.box-body {
  width: 140%!important;
}
.box-header {
  width: 140%!important;
  height: 40px!important;
}
table {
  width: 100%!important;
}
.id-column {
  width: 10%;
}
.name-column {
  width: 30%; 
}
.dep-column {
  width: 10%;
}
.pass-column {
  width: 10%; 
}

.box-header .addstudbutton {
  margin-left: 85%;
  
}
.box-header h3{
  margin-top: -30px;
  font-size: 30px;
  text-align: left!important;
}
.action{
  width: 10%;
}
.manage-button{
  margin-left: 0px;
}
  </style>
</head>
<body>
  
<?php include 'sidebar.php'; ?>
<div class="content">
  <h1>Student Management</h1><br><br><br><br><br><br><br>
  <div class="search-container">
  
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for student...">
    <select id="courseFilter" onchange="filterByCourse()">
      <option value="">All Courses</option>
      <option value="BSIS">BSIS</option>
      <option value="BSTM">BSTM</option>
      <option value="BSMA">BSMA</option>
      
      <!-- Add more options for other courses if needed -->
    </select>
    
  </div>

  

<div class="box-header">
<button class="addstudbutton" onclick="toggleAddStudentPopup()"><i class="fas fa-plus">&nbsp;&nbsp;New Student</i></button>
<h3>Student List</h3></div>
<div class="box-body">
<?php
$spaces = str_repeat('&nbsp;',120); 
echo $spaces; 
?>

  
  
  <table id="studentTable">
    <thead>
      <tr>
        <th class="id-column">Student ID</th>
        <th class="name-column">Name</th>
        <th class="department">Department</th>
        <th class="section">Section</th>
        <th class="pass-column">Password</th>
        <th class="action">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $host = 'localhost';  
        $dbname = 'evaluation_quiet';
        $user = 'admin'; 
        $pass = 'admin'; 
        $connection = mysqli_connect($host, $user, $pass, $dbname);
        if ($connection) {
          $query = "SELECT * FROM student_info";
          $result = mysqli_query($connection, $query);
          if (mysqli_num_rows($result) > 0) {
            echo "<form method='post' action=''>"; // Open the form tag here
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr>";
              echo "<td>{$row['student_ID']}</td>";
              echo "<td>{$row['student_NAME']}</td>";
              echo "<td>{$row['student_COURSE']}</td>";
              echo "<td>{$row['student_SECTION']}</td>";
              echo "<td>{$row['student_PASS']}</td>";
              echo "<td class='actions-col'>";
              echo "<a href='edit_student.php?student_ID={$row['student_ID']}' class='manage-button'><i class='fas fa-tasks'></i> MANAGE</a>&nbsp;<br><br>";

              
              echo "</td>";
              echo "</tr>";
            }
            echo "</form>"; // Close the form tag here
          } else {
            echo "<tr><td colspan='5'>No students found.</td></tr>";
          }
          mysqli_close($connection);
        } else {
          echo "<tr><td colspan='5'>Failed to connect to the database.</td></tr>";
        }
      ?>
    </tbody>
  </table>
  </div>
      </div>
      


  <div class="overlay2" id="overlay2"></div>
  <div class="add-student-popup" id="addStudentPopup">
  <span class="close-btn" onclick="toggleAddStudentPopup()">&times;</span>
  
  
  <table  class="addstudtable">
  <form action="add_student.php" method="POST">
  <h2>Add New Student</h2>
    <tr><td><label for="student_id">Student ID:</label></td>
    <td><input type="text" id="student_id" name="student_id" required class="expand-input"></td></tr><br>
    <tr><td><label for="new_name">Name:</label></td>
    <td><input type="text" id="new_name" name="new_name" required class="expand-input"></td></tr><br>
    <tr><td><label for="new_course">Course:</label></td>
    <td><input type="text" id="new_course" name="new_course" required class="expand-input"></td></tr><br>
    <tr><td><label for="new_section">Section:</label></td>
    <td><input type="text" id="new_section" name="new_section" required class="expand-input"></td></tr><br><br><br>

    <td><button type="submit">Add Student</button></td>
  </form>
  </table>
</div>


<script>
  // Function to edit a student
  function editStudent(studentID) {
    var newName = document.querySelector('td[data-student-id="' + studentID + '"][data-column-name="student_NAME"]').textContent.trim();
    var newCourse = document.querySelector('td[data-student-id="' + studentID + '"][data-column-name="student_COURSE"]').textContent.trim();
    var newSection = document.querySelector('td[data-student-id="' + studentID + '"][data-column-name="student_SECTION"]').textContent.trim();

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'edit_delete_student.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if (xhr.status === 200) {
        reloadPage(); 
      } else {
        console.error('Error editing student:', xhr.responseText);
      }
    };
    xhr.onerror = function() {
      console.error('Network error occurred while editing student');
    };
    xhr.send('action=edit&student_ID=' + studentID + '&edited_name=' + newName + '&edited_course=' + newCourse + '&edited_section=' + newSection);
  }


  function deleteStudent(studentID) {
    if (confirm("Are you sure you want to delete this student?")) {
        document.getElementById("studentID").value = studentID + ""; // Concatenate an empty string to ensure it's treated as a string
        document.getElementById("deleteForm").submit();
    }
}

  function toggleMenu(button) {
    var menu = button.nextElementSibling;
    menu.style.display = menu.style.display === "none" ? "inline-block" : "none";
  }

  function reloadPage() {
    window.location.reload();
  }

  function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("studentTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[1];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  function filterByCourse() {
    var courseSelect, selectedCourse, table, tr, td, i;
    courseSelect = document.getElementById("courseFilter");
    selectedCourse = courseSelect.value.toUpperCase();
    table = document.getElementById("studentTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[2]; 
      if (td) {
        if (selectedCourse === "" || td.textContent.toUpperCase() === selectedCourse) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }


  function toggleAddStudentPopup() {
    var popup = document.getElementById("addStudentPopup");
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
</body>
</html>
