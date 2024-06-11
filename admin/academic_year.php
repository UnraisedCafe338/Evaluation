<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Academic Year</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .box-body {
      
      width: 240%!important;
      height: 55%!important;
    }
    .academic-button {
      background-color: darkblue;
      min-width: 120px;
      margin-right: 0px;
      margin-left: -10px;
      padding-left: 15px;
      border-radius: 10px;
    }
    table {
      width: 100%!important;
      border-collapse: collapse;
    }
    th, td {
      padding: 3px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #f2f2f2;
    }
    tr:hover {
      background-color: #f2f2f2;
    }
    .green { color: green; }
    .red { color: red; }
    .yellow { color: rgb(1, 6, 255); }
    .default-button {
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .default-yes {
      background-color: blue;
      color: white;
    }
    .default-no {
      background-color: red;
      color: white;
    }
    .box-header {
      padding-top: 5px!important;
      padding-bottom: 5px!important;
      margin-right: -70px;
      width: 1200px!important;
    }
    .box-body {
      height: auto!important;
      width: 1200px!important;
      margin-right: -70px;
      margin-bottom: 20px;
    }
    .popup-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    .popup-content {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .popup-content h2 {
      margin-top: 0;
    }
    .popup-content form {
      display: flex;
      flex-direction: column;
    }
    .popup-content label {
      margin: 10px 0 5px;
    }
    .popup-content input, .popup-content select {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .popup-content button {
      margin-top: 10px;
      padding: 10px;
      background-color: rgb(43, 0, 255);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .popup-content button.close-btn {
      background-color: red;
    }
    .view {
      background-color: #0055ff;
      color: rgb(255, 255, 255);
      padding: 7px 20px;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }
    .filter-button{
      margin-left: 10px;
      height: 30px;
      padding: 7px 20px;
    }
    .view:hover {
      background-color: navy;
    }
    .actions {
      width: 15%;
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>
  <div class="content">
    <h1>Academic Year</h1><br><br><br><br>

    <?php
    include('../connection.php');
   

    if (!$connection) {
      die("Connection failed: " . mysqli_connect_error());
    }

    $selected_academic_year_query = "SELECT * FROM academic_list WHERE default_select = 1";
    $selected_academic_year_result = mysqli_query($connection, $selected_academic_year_query);

    if ($selected_academic_year_result && mysqli_num_rows($selected_academic_year_result) > 0) {
      $selected_academic_year_row = mysqli_fetch_assoc($selected_academic_year_result);
      $semester_text = "";
      switch ($selected_academic_year_row['semester']) {
        case 1:
          $semester_text = "1st";
          break;
        case 2:
          $semester_text = "2nd";
          break;
        case 3:
          $semester_text = "3rd";
          break;
        default:
          $semester_text = "Unknown";
      }

      $evaluation_status = "";
      switch ($selected_academic_year_row['status']) {
        case 0:
          $evaluation_status = "Closed";
          break;
        case 1:
          $evaluation_status = "Pending";
          break;
        case 2:
          $evaluation_status = "On-going";
          break;
        default:
          $evaluation_status = "Unknown";
      }

      echo "<div class='box-header'>";
      echo "<div>";
      echo "<h2>Current Academic Year</h2>";
      echo "<h3>Academic Year: {$selected_academic_year_row['year']} $semester_text Semester</h3>";
      echo "<h3>Evaluation Status: $evaluation_status</h3>";
      echo "</div>";
      echo "</div>";
    } else {
      echo "<div class='box-header'>";
      echo "<h2>No current academic year selected.<br></h2><h3>Please select anything from the list</h3>";
      echo "</div>";
    }

    $academic_year_query = "SELECT * FROM academic_list";

    if(isset($_GET['academic_year']) && $_GET['academic_year'] != '') {
      $academic_year_query .= " WHERE year = '{$_GET['academic_year']}'";
    }

    if(isset($_GET['semester']) && $_GET['semester'] != '') {
      if(strpos($academic_year_query, 'WHERE') !== false) {
        $academic_year_query .= " AND semester = '{$_GET['semester']}'";
      } else {
        $academic_year_query .= " WHERE semester = '{$_GET['semester']}'";
      }
    }

    // Sorting the academic lsits
    $sort_order = "ASC";
    if (isset($_GET['sort']) && $_GET['sort'] == 'desc') {
      $sort_order = "DESC";
    }
    $academic_year_query .= " ORDER BY year $sort_order";

    $academic_year_result = mysqli_query($connection, $academic_year_query);

    if ($academic_year_result) {
      echo "<div class='box-body'>";
      echo "<button class='button' onclick='openPopup()'>Create New Academic Year</button><br><br>";

      echo "<div>";
      echo "<h2>Academic Year Lists</h2>";
      echo "<label for='academicYear'>Select Academic Year:</label>";
      echo "<select id='academicYear' onchange='filterAcademicYear()'>";
      echo "<option value=''>All</option>";

      $distinct_years_query = "SELECT DISTINCT year FROM academic_list";
      $distinct_years_result = mysqli_query($connection, $distinct_years_query);

      if ($distinct_years_result && mysqli_num_rows($distinct_years_result) > 0) {
        while ($row = mysqli_fetch_assoc($distinct_years_result)) {
          $selected = ($row['year'] == $_GET['academic_year']) ? 'selected' : '';
          echo "<option value='{$row['year']}' $selected>{$row['year']}</option>";
        }
      }
      echo "</select>";

      echo "<label for='semester'>&nbsp;&nbsp;&nbsp;Select Semester:</label>";
      echo "<select id='semester' onchange='filterSemester()'>";
      echo "<option value=''>All</option>";
      echo "<option value='1' " . ($_GET['semester'] == '1' ? 'selected' : '') . ">1st</option>";
      echo "<option value='2' " . ($_GET['semester'] == '2' ? 'selected' : '') . ">2nd</option>";
      echo "<option value='3' " . ($_GET['semester'] == '3' ? 'selected' : '') . ">3rd</option>";
      echo "</select>";
      echo "<button onclick='applyFilter()' class='filter-button'>Apply</button>";
      
      echo "<label for='sortOrder'>&nbsp;&nbsp;&nbsp;Arrange list by Year:</label>";
      echo "<select id='sortOrder' onchange='sortAcademicYears()'>";
      echo "<option value='asc' " . ($_GET['sort'] == 'asc' ? 'selected' : '') . ">Oldest to Newest</option>";
      echo "<option value='desc' " . ($_GET['sort'] == 'desc' ? 'selected' : '') . ">Newest to Oldest</option>";
      echo "</select>";

      
      echo "</div><br>";

      echo "<table>";
      echo "<thead>";
      echo "<tr>";
      echo "<th>No.</th>";
      echo "<th>Academic Year</th>";
      echo "<th>Semester</th>";
      echo "<th>Default</th>";
      echo "<th>Status</th>";
      echo "<th>Actions</th>";
      echo "</tr>";
      echo "</thead>";
      echo "<tbody>";
      $rowCount = 1;
      while ($row = mysqli_fetch_assoc($academic_year_result)) {
        echo "<tr>";
        echo "<td>{$rowCount}</td>";
        echo "<td>{$row['year']}</td>";
        echo "<td>{$row['semester']}</td>";
        $rowCount++;
        echo "<td>";
        if ($row['default_select'] == 1) {
          echo "<button class='default-button default-yes' id='default-{$row['id']}' onclick='toggleDefaultAcademicYear({$row['id']})'>Starting</button>";
        } else {
          echo "<button class='default-button default-no' id='default-{$row['id']}' onclick='toggleDefaultAcademicYear({$row['id']})'>Close</button>";
        }
        echo "</td>";

        $status = '';
        switch ($row['status']) {
          case 0:
            $status = '<span class="red">Finished</span>';
            break;
          case 1:
            $status = '<span class="yellow">Not Yet Started</span>';
            break;
          case 2:
            $status = '<span class="green">On-going</span>';
            break;
        }
        echo "<td>{$status}</td>";
        echo "<td>";
        echo "<a href='manage_academic.php?id={$row['id']}' class='view'><i class='fas fa-chart-bar'></i></a>&nbsp;&nbsp;";
        echo "<button class='delete-button' onclick='deleteAcademicYear({$row['id']})'><i class='fas fa-trash'></i></button>";
        echo "</td>";
        echo "</tr>";
      }
      echo "</tbody>";
      echo "</table>";
      echo "</div>";
    } else {
      echo "<p>No academic years found</p>";
    }
    ?>
  </div>

  <div class="popup-overlay" id="popupOverlay">
    <div class="popup-content">
      <h2>Create New Academic Year</h2>
      <form method="post" action="create_academic_year.php">
        <label for="year">Academic Year:</label>
        <input type="text" id="year" name="year" required>
        <label for="semester">Semester:</label>
        <select id="semester" name="semester" required>
          <option value="1">1st</option>
          <option value="2">2nd</option>
          <option value="3">3rd</option>
        </select>
        <button type="submit">Create</button>
        <button type="button" class="close-btn" onclick="closePopup()">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    function openPopup() {
      document.getElementById('popupOverlay').style.display = 'flex';
    }

    function closePopup() {
      document.getElementById('popupOverlay').style.display = 'none';
    }

    function deleteAcademicYear(academicYearId) {
      if (confirm("Are you sure you want to delete this academic year?")) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
              location.reload();
            } else {
              alert("Error deleting academic year.");
            }
          }
        };
        xhr.open("GET", "delete_academic_year.php?id=" + academicYearId, true);
        xhr.send();
      }
    }

    function toggleDefaultAcademicYear(academicYearId) {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            location.reload();
          } else {
            alert("Error toggling default academic year.");
          }
        }
      };
      xhr.open("GET", "update_academic_year_status.php?id=" + academicYearId + "&default=1", true);
      xhr.send();
    }

    function applyFilter() {
      var selectedYear = document.getElementById('academicYear').value;
      var selectedSemester = document.getElementById('semester').value;
      var url = 'academic_year.php';

      if (selectedYear !== '' || selectedSemester !== '') {
        url += '?';

        if (selectedYear !== '') {
          url += 'academic_year=' + selectedYear;
        }

        if (selectedSemester !== '') {
          url += (selectedYear !== '' ? '&' : '') + 'semester=' + selectedSemester;
        }
      }

      window.location.href = url;
    }

    function sortAcademicYears() {
      var selectedSortOrder = document.getElementById('sortOrder').value;
      var selectedYear = document.getElementById('academicYear').value;
      var selectedSemester = document.getElementById('semester').value;
      var url = 'academic_year.php?sort=' + selectedSortOrder;

      if (selectedYear !== '') {
        url += '&academic_year=' + selectedYear;
      }

      if (selectedSemester !== '') {
        url += '&semester=' + selectedSemester;
      }

      window.location.href = url;
    }
  </script>
</body>
</html>
