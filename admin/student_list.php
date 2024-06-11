<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Management</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    /* Add your existing CSS styles here */
    .content {
      margin-left: 0px;
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
    .content h1 {
      background: linear-gradient(to bottom, rgb(66, 78, 255), rgb(49, 0, 208));
      width: 3000px;
      height: 30px;
      padding: 20px;
      position: fixed;
      margin-top: -30px;
      margin-left: -10px;
      color: white;
      top: 30px;
      border-bottom: 0px solid #ffdd00!important; 
      z-index: 3;
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
    .add-student-popup .addstudtable {
      margin-top: -90px;
    }
    .addstudtable td {
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
    .overlay2 h2 {
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
      margin-right: -70px;

      width: 1200px!important;
    }
    .box-header {
      margin-right: -70px;
      width: 1200px!important;
      height: auto!important;
    }
    table {
      width: 100%!important;
    }
    .id-column {
      width: 20%;
    }
    .name-column {
      width: 40%; 
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
    .box-header h3 {
      margin-top: -30px;
      font-size: 30px;
      text-align: left!important;
    }
    .action {
      width: 10%;
    }
    .manage-button {
      margin-top: 15px;
    }
    .manage-button {
      background-color: #0055ff;
      color: rgb(255, 255, 255);
      padding: 7px 20px;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }
    .manage-button:hover {
      background-color: navy;
    }
    .batch-upload-container {
      text-align: right;
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
      <?php
        // Fetch distinct courses from the database
        $connection = mysqli_connect('localhost', 'admin', 'admin', 'evaluation_quiet');
        if ($connection) {
          $course_query = "SELECT DISTINCT student_COURSE FROM student_info";
          $course_result = mysqli_query($connection, $course_query);
          while ($course_row = mysqli_fetch_assoc($course_result)) {
            echo '<option value="' . htmlspecialchars($course_row['student_COURSE']) . '">' . htmlspecialchars($course_row['student_COURSE']) . '</option>';
          }
        }
      ?>
    </select>
    <select id="sectionFilter" onchange="filterBySection()">
      <option value="">All Sections</option>
      <?php
        // Fetch distinct sections from the database
        if ($connection) {
          $section_query = "SELECT DISTINCT student_SECTION FROM student_info";
          $section_result = mysqli_query($connection, $section_query);
          while ($section_row = mysqli_fetch_assoc($section_result)) {
            echo '<option value="' . htmlspecialchars($section_row['student_SECTION']) . '">' . htmlspecialchars($section_row['student_SECTION']) . '</option>';
          }
          mysqli_close($connection);
        }
      ?>
    </select>
  </div>

  <div class="box-header">
    <button class="addstudbutton" onclick="toggleAddStudentPopup()"><i class="fas fa-plus">&nbsp;&nbsp;New Student</i></button>
    <h3>Student List</h3>
    <div class="batch-upload-container">
      <h2></h2>
      <form action="batch_upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Upload CSV file:</label>
        <input type="file" name="file" id="file" accept=".csv" required>
        <button  type="submit" name="upload">Upload</button>
      </form>
    </div>
  </div>
  <div class="box-body">
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
          $connection = mysqli_connect('localhost', 'admin', 'admin', 'evaluation_quiet');
          if ($connection) {
            $query = "SELECT * FROM student_info";
            $result = mysqli_query($connection, $query);
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['student_ID']}</td>";
                echo "<td>{$row['student_NAME']}</td>";
                echo "<td>{$row['student_COURSE']}</td>";
                echo "<td>{$row['student_SECTION']}</td>";
                echo "<td>{$row['student_PASS']}</td>";
                echo "<td class='actions-col'>";
                echo "<a href='edit_student.php?student_ID={$row['student_ID']}' class='manage-button'><i class='fas fa-user-cog'></i></a>";
                echo "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='6'>No students found.</td></tr>";
            }
            mysqli_close($connection);
          } else {
            echo "<tr><td colspan='6'>Failed to connect to the database.</td></tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

<div class="overlay2" id="overlay2"></div>
<div class="add-student-popup" id="addStudentPopup">
  <span class="close-btn" onclick="toggleAddStudentPopup()">&times;</span>
  <form action="add_student.php" method="POST">
    <h2>Add New Student</h2>
    <table class="addstudtable">
      <tr>
        <td><label for="student_id">Student ID:</label></td>
        <td><input type="text" id="student_id" name="student_id" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_name">Name:</label></td>
        <td><input type="text" id="new_name" name="new_name" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_course">Course:</label></td>
        <td><input type="text" id="new_course" name="new_course" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_section">Section:</label></td>
        <td><input type="text" id="new_section" name="new_section" required class="expand-input"></td>
      </tr>
      <tr>
        <td colspan="2"><button type="submit">Add Student</button></td>
      </tr>
    </table>
  </form>
</div>

<script>
  // Function to search table by name
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

  // Function to filter table by course
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

  // Function to filter table by section
  function filterBySection() {
    var sectionSelect, selectedSection, table, tr, td, i;
    sectionSelect = document.getElementById("sectionFilter");
    selectedSection = sectionSelect.value.toUpperCase();
    table = document.getElementById("studentTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[3];
      if (td) {
        if (selectedSection === "" || td.textContent.toUpperCase() === selectedSection) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  // Function to toggle the add student popup
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
