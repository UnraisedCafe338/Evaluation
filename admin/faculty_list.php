<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Faculty Management</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    /* Add or modify styles as needed */
    .content {
    margin-left: 240px;
    margin-bottom:1001px;
    padding: 20px;
    z-index: 0;
    }
    table {
      width: 200%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
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
    }
    .edit-delete-btns button:hover {
      color: red;
    }
    .edit-delete-form input[type="text"], 
    .edit-delete-form input[type="checkbox"] {
      margin-right: 5px;
    }
    .faculty-button {
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
  margin-top: -30px;
  margin-left: -19px;
  color: white;
  bottom: 10px;
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
  </style>
</head>
<body>
  
<?php include 'sidebar.php'; ?>
<div class="content">
  <h1>Faculty Management</h1><br><br><br><br>
  <div class="search-container">
  
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for teacher...">
<select id="courseFilter" onchange="filterByCourse()">
  <option value="">All Department</option>
  <option value="BSIS">BSIS</option>
  <option value="BSTM">BSTM</option>
  <option value="BSMA">BSMA</option>

</select>

  </div>

  
  <h3>Faculty List</h3>
  <table id="studentTable">
    <thead>
      <tr>
        
        <th>Faculty ID</th>
        <th>Name</th>
        <th>Department</th>
        <th>Subjects</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>

 
  
</form>
  </div>
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
        reloadPage(); // Reload the page after successful editing
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

      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'edit_delete_student.php', true);
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        if (xhr.status === 200) {
          reloadPage(); // Reload the page after successful deleting the questionnaire
        } else {
          console.error('Error deleting student:', xhr.responseText); 
        }
      };
      xhr.onerror = function() {
        console.error('Network error occurred while deleting student'); 
      };
      xhr.send('action=delete&student_ID=' + studentID);
    }
  }
  // Function to toggle the visibility of edit and delete buttons
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
    td = tr[i].getElementsByTagName("td")[2]; // Assuming course is in the third column
    if (td) {
      if (selectedCourse === "" || td.textContent.toUpperCase() === selectedCourse) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

</script>
</body>
</html>
