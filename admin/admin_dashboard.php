<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>ECA ADMIN DASHBOARD</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .dashboard-button {
      background-color: darkblue;
      min-width: 120px;
      margin-right: 0px;
      margin-left: -10px;
      padding-left: 15px;
      border-radius: 10px;
    }
    .info-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #f1f1f1;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      margin: 10px 0;
      flex: 1;
      width: 300px;
      margin-left: 10px;
    }
    .info-box i {
      font-size: 40px;
      margin-right: 20px;
    }
    .info-box .info-text {
      flex: 1;
    }
    .info-box .info-text h2 {
      margin: 0;
      font-size: 24px;
    }
    .info-box .info-text p {
      margin: 5px 0 0;
      font-size: 18px;
    }
    .box-header {
      margin-top: 20px!important;
      width: 300px!important;
      border-radius: 10px;
      margin-bottom: 20px!important;
    }
    .chart-container {
      width: 800px;
      height: 800px;
      margin-top: -100px;
      margin-right: 0px;
     
    }
    .dashboard-content {
      display: flex;
      flex-wrap: wrap;
    }
    .left-section {
      flex: 1;
      padding-right: 20px;
    }
    .right-section {
      flex: 2;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'sidebar.php'; ?>
  <div class="content">
    <h1>Welcome to Dashboard</h1><br><br><br>
    <?php
    include('../connection.php');


    if (!$connection) {
      die("Connection failed: " . mysqli_connect_error());
    }

    echo "<div class='box-header'>";

    $query = "SELECT * FROM academic_list WHERE default_select = 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
      $academic_year = mysqli_fetch_assoc($result);
      $academic_year_id = $academic_year['id'];
      $semester_text = "";
      switch ($academic_year['semester']) {
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
      switch ($academic_year['status']) {
        case 0:
          $evaluation_status = "Finished";
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

      $total_evaluations_query = "
        SELECT COUNT(DISTINCT enrollments.student_ID, subjects.FacultyID) AS total_evaluations
        FROM enrollments
        JOIN subjects ON enrollments.subject_code = subjects.subject_code
        WHERE subjects.FacultyID IN (
            SELECT FacultyID FROM facultymembers
        )
      ";
      $total_evaluations_result = mysqli_query($connection, $total_evaluations_query);
      $total_evaluations = mysqli_fetch_assoc($total_evaluations_result)['total_evaluations'];


      $completed_evaluations_query = "
        SELECT COUNT(DISTINCT student_ID, FacultyID) AS completed_evaluations 
        FROM evaluation_table
        WHERE academic_year = $academic_year_id
      ";
      $completed_evaluations_result = mysqli_query($connection, $completed_evaluations_query);
      $completed_evaluations = mysqli_fetch_assoc($completed_evaluations_result)['completed_evaluations'];


      $completion_percentage = ($total_evaluations > 0) ? ($completed_evaluations / $total_evaluations) * 100 : 0;
      $completion_percentage = round($completion_percentage, 2);

      echo "<h3>Academic Year: {$academic_year['year']} $semester_text Semester</h3>";
      echo "<h3>Evaluation Status: $evaluation_status</h3>";
      echo "<h3>Completion Percentage: $completion_percentage%</h3>";
    } else {
      echo "<h3>No current academic year found.</h3>";
    }
    echo "</div>";

    echo "<div class='dashboard-content'>";
    echo "<div class='left-section'>";
    $student_query = "SELECT COUNT(*) AS total_students FROM student_info";
    $student_result = mysqli_query($connection, $student_query);
    $total_students = mysqli_fetch_assoc($student_result)['total_students'];

    $faculty_query = "SELECT COUNT(*) AS total_faculty FROM facultymembers";
    $faculty_result = mysqli_query($connection, $faculty_query);
    $total_faculty = mysqli_fetch_assoc($faculty_result)['total_faculty'];
    echo "
    <div class='info-box'>
      <i class='fas fa-user-graduate'></i>
      <div class='info-text'>
        <h2>Total Students</h2>
        <h3>$total_students</h3>
      </div>
    </div>
    <div class='info-box'>
      <i class='fas fa-chalkboard-teacher'></i>
      <div class='info-text'>
        <h2>Total Faculty</h2>
        <h3>$total_faculty</h3>
      </div>
    </div>";
    echo "</div>";
    ?>
    <div class="right-section">
      <div class="chart-container">
        <canvas id="facultyAverageScoreChart"></canvas>
      </div>
    </div>
    </div>
  </div>
</body>
</html>

<?php

$chart_data_query = "
  SELECT fm.Name, es.avg_rating
  FROM evaluation_summary es
  JOIN facultymembers fm ON es.faculty_id = fm.FacultyID
  WHERE es.academic_id = $academic_year_id
";
$chart_data_result = mysqli_query($connection, $chart_data_query);
$faculty_names = [];
$average_ratings = [];

while ($row = mysqli_fetch_assoc($chart_data_result)) {
  $faculty_names[] = $row['Name'];
  $average_ratings[] = $row['avg_rating'];
}

echo "<script>console.log('Faculty Names: " . json_encode($faculty_names) . "');</script>";
echo "<script>console.log('Average Ratings: " . json_encode($average_ratings) . "');</script>";

mysqli_close($connection);
?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('facultyAverageScoreChart').getContext('2d');
    var facultyNames = <?php echo json_encode($faculty_names); ?>;
    var averageRatings = <?php echo json_encode($average_ratings); ?>;

    console.log('Faculty Names:', facultyNames);
    console.log('Average Ratings:', averageRatings);

    var chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: facultyNames,
        datasets: [{
          label: 'Average Rating',
          data: averageRatings,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: 'y',
        scales: {
          x: {
            beginAtZero: true,
            max: 100,
            title: {
              display: true,
              text: 'Average Rating (%)'
            }
          },
          y: {
            title: {
              display: true,
              text: 'Faculty'
            }
          }
        }
      }
    });
  });
</script>
