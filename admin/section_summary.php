<?php
include('../connection.php');
$facultyID = $_GET['FacultyID'] ?? null;
$academicYear = $_GET['academic_year'] ?? null;
$academic_year_id = $_GET['academic_year_id'] ?? null;
$semester = $_GET['semester'] ?? null;
$semester2 = ($_GET['semester'] == "1") ? "1st" : (($_GET['semester'] == "2") ? "2nd" : "3rd");

if ($facultyID === null || $academicYear === null || $academic_year_id === null || $semester === null) {
    echo "Invalid Faculty ID, Academic Year, or Academic Year ID.";
    exit;
}

$faculty_query = "SELECT Name FROM facultymembers WHERE FacultyID = ?";
$stmt = mysqli_prepare($connection, $faculty_query);
mysqli_stmt_bind_param($stmt, "s", $facultyID);
mysqli_stmt_execute($stmt);
$faculty_result = mysqli_stmt_get_result($stmt);

if (!$faculty_result || mysqli_num_rows($faculty_result) === 0) {
    echo "Faculty member not found.";
    exit;
}
$faculty = mysqli_fetch_assoc($faculty_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SECTION SUMMARY</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    .academic-button {
    background-color: darkblue;
    min-width: 120px;
    margin-right: 0px;
    margin-left: -10px;
    padding-left: 15px;
    border-radius: 10px;
        }
    .horizontal-nav .section-button{
        border-bottom: 5px solid white!important;
    }
    table {
        width: 100%!important;
        border-collapse: collapse;
    }
    .box-header {
        width: 1220px!important;
        padding-top: 10px!important;
        text-align: left!important;
        margin-top: 15px;
        padding-bottom: 0px!important;
        padding-right: 0px!important;
        height: 155px!important;
        margin-right: 0px!important;
    }
    .box-header h3 {
        margin-top: -20px;
        padding-bottom: -10px!important;
    }
    .box-body {
        width: 1200px!important;
        margin-bottom: 50px;
        margin-right: 0px!important;
    }
    .button {
            margin-left: 1080px!important;
            padding: 10px 25px!important;
            font-size: medium;
            text-decoration: none!important;
        }
    .text-box {
        margin-top: 20px;
    }
    .criteria-head {
        height: 50px;
        background-color: #a4a4a4;
    }
    .total-score-text {
        background-color: #a4a4a4;
    }
    .total-score-num {
        background-color: #c2c1c1;
    }
    .print-button {
        margin-left: 1080px;
        padding: 11px 34px;
        font-size: medium;
        cursor: pointer;
    }
    .content{
        margin-right: 2px!important;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .box-body, .box-body * {
            visibility: visible;
        }
        .box-body {
            position: absolute;
            left: 50%;
            top: 0;
            transform: translateX(-50%);
            width: 80%;
            margin: 0 auto;
        }
    }

    .question-list{
        width: 75%;
    }
    .questions{
        text-align: left!important;
    }
</style>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
  <h1>SECTION SUMMARY</h1><br><br><br>
  <?php include 'summary_topbar.php'; ?>
  <div class="box-header">
                <div class="text-box">
                 
               
                <h3> Academic Year: &nbsp;<?php echo htmlspecialchars($academicYear);?>, <?php echo htmlspecialchars($semester2); ?> Semester <a href='manage_academic.php?id=<?php echo $academic_year_id; ?>' class='button'><i class='fas fa-arrow-left'></i>Back</a>
                <h3>Evaluation Results: &nbsp;<?php echo htmlspecialchars($faculty['Name']); ?></h3><button class="print-button" onclick="printBoxBody()">Print</button>
                
            </div>
  <form action="" method="GET" id="sectionForm">
    <label for="section">Select a Section:</label>
    <select name="section" id="section" onchange="document.getElementById('sectionForm').submit()">
      <option value="all">All</option>
      <?php
      if (!$connection) {
          die("Connection failed: " . mysqli_connect_error());
      }

      $section_query = "SELECT DISTINCT CONCAT(student_COURSE, '-', student_SECTION) AS course_section FROM student_info";
      $section_result = mysqli_query($connection, $section_query);

      if ($section_result && mysqli_num_rows($section_result) > 0) {
          while ($row = mysqli_fetch_assoc($section_result)) {
              $course_section = htmlspecialchars($row['course_section']);
              $section_name = explode('-', $course_section)[1];
              $selected = ($_GET['section'] ?? 'all') === $section_name ? 'selected' : '';
              echo "<option value='$section_name' $selected>$course_section</option>";
          }
      } else {
          echo "<option value=''>No sections found</option>";
      }
      ?>
      
    </select>
    <input type="hidden" name="academic_year_id" value="<?php echo htmlspecialchars($academic_year_id); ?>">
    <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($academicYear); ?>">
    <input type="hidden" name="semester" value="<?php echo htmlspecialchars($_GET['semester'] ?? ''); ?>">
    <input type="hidden" name="FacultyID" value="<?php echo htmlspecialchars($_GET['FacultyID'] ?? ''); ?>">
  </form>
  </div>
  <div class="box-body">
  <div class="section-summary">
 
    <?php
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$selected_section = $_GET['section'] ?? 'all';
$selected_academic_year = $academic_year_id;
$semester = $_GET['semester'] ?? null;
$facultyID = $_GET['FacultyID'] ?? null;

if ($selected_section === 'all') {
    $section_summary_query = "
    SELECT student_SECTION, COUNT(DISTINCT evaluation_table.student_ID) AS total_students_evaluated, AVG(question_score) AS average_score
    FROM evaluation_table
    INNER JOIN student_info ON evaluation_table.student_ID = student_info.student_ID
    WHERE evaluation_table.academic_year = ?
    GROUP BY student_SECTION
    ";
    $stmt = mysqli_prepare($connection, $section_summary_query);
    mysqli_stmt_bind_param($stmt, "s", $academic_year_id);
    mysqli_stmt_execute($stmt);
    $section_summary_result = mysqli_stmt_get_result($stmt);
    
    if ($section_summary_result && mysqli_num_rows($section_summary_result) > 0) {
        echo "<h2>Section Summary</h2>";
        echo "<table border='1'>";
        echo "<thead><tr><th>Section</th><th>Total Students Evaluated</th><th>Total Enrolled</th><th>Average Score</th></tr></thead>";
        echo "<tbody>";
    
        
            $total_students_query = "
SELECT 
    student_info.student_SECTION AS section,
    COUNT(DISTINCT CASE WHEN evaluation_table.academic_year = ? THEN evaluation_table.student_ID END) AS total_students_evaluated,
    COUNT(DISTINCT enrollments.student_ID) AS total_enrolled,
    AVG(CASE WHEN evaluation_table.academic_year = ? AND evaluation_table.FacultyID = ? THEN evaluation_table.question_score ELSE NULL END) AS average_score
FROM 
    student_info
LEFT JOIN 
    evaluation_table ON student_info.student_ID = evaluation_table.student_ID
LEFT JOIN 
    enrollments ON student_info.student_ID = enrollments.student_ID
LEFT JOIN 
    subjects ON enrollments.subject_code = subjects.subject_code
WHERE
    subjects.FacultyID = ? 
GROUP BY 
    student_info.student_SECTION;
";
    
            $stmt_total = mysqli_prepare($connection, $total_students_query);
            mysqli_stmt_bind_param($stmt_total, "ssss", $academic_year_id,$academic_year_id,$facultyID,$facultyID);
            mysqli_stmt_execute($stmt_total);
            $total_students_result = mysqli_stmt_get_result($stmt_total);
    
 
        
            while ($row = mysqli_fetch_assoc($total_students_result)) {
                $section = $row['section'];
                $total_students_evaluated = $row['total_students_evaluated'];
                $total_enrolled = $row['total_enrolled'];
                $average_score = round($row['average_score'] / 5 * 100, 2);
    
                echo "<tr><td>$section</td><td>$total_students_evaluated</td><td>$total_enrolled</td><td>$average_score%</td></tr>";
            }
    
            echo "</tbody></table>";
        
    } else {
        echo "<h2>No data available for the selected academic year.</h2>";
    }
    } else {
        $section_query = "
            SELECT COUNT(DISTINCT e.student_ID) AS total_evaluations, AVG(e.question_score) AS average_score
            FROM evaluation_table e
            INNER JOIN student_info si ON e.student_ID = si.student_ID
            WHERE e.academic_year = ? AND si.student_SECTION = ?
        ";
        $stmt = mysqli_prepare($connection, $section_query);
        mysqli_stmt_bind_param($stmt, "ss", $selected_academic_year, $selected_section);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $total_evaluations = $row['total_evaluations'];
            $average_score = round($row['average_score'] / 5 * 100, 2);
    
            $total_students_query = "
                SELECT COUNT(DISTINCT student_info.student_ID) AS total_students
                FROM enrollments
                INNER JOIN subjects ON enrollments.subject_code = subjects.subject_code
                INNER JOIN student_info ON enrollments.student_ID = student_info.student_ID
                WHERE subjects.FacultyID = ? AND student_info.student_SECTION = ?
            ";
    
            $stmt_total = mysqli_prepare($connection, $total_students_query);
    
            $subject_code = get_subject_code_for_section($connection, $selected_section);
    
            mysqli_stmt_bind_param($stmt_total, "ss", $facultyID, $selected_section);
            mysqli_stmt_execute($stmt_total);
            $total_students_result = mysqli_stmt_get_result($stmt_total);
            $total_students_row = mysqli_fetch_assoc($total_students_result);
            $total_students = $total_students_row['total_students'];
    
            echo "<h3>Total Evaluated Students : $total_evaluations <br> Total Enrolled Students: $total_students <br> For Section: $selected_section</h3>";
        }
    
        $criteria_query = "
            SELECT DISTINCT c.criteria_name
            FROM evaluation_table e
            INNER JOIN question_list q ON e.question_id = q.question_id
            INNER JOIN criteria c ON q.criteria_id = c.criteria_id
            WHERE e.academic_year = ? AND e.student_ID IN (
                SELECT student_ID FROM student_info WHERE student_SECTION = ?
            )
            ORDER BY c.criteria_order
        ";
        $stmt = mysqli_prepare($connection, $criteria_query);
        mysqli_stmt_bind_param($stmt, "ss", $selected_academic_year, $selected_section);
        mysqli_stmt_execute($stmt);
        $criteria_result = mysqli_stmt_get_result($stmt);
    
        while ($criteria_row = mysqli_fetch_assoc($criteria_result)) {
            $criteria_name = htmlspecialchars($criteria_row['criteria_name']);
            echo "<h4>$criteria_name</h4>";
    
            $questions_query = "
                SELECT 
                    q.question_text,
                    SUM(CASE WHEN e.question_score = 5 THEN 1 ELSE 0 END) AS score_5,
                    SUM(CASE WHEN e.question_score = 4 THEN 1 ELSE 0 END) AS score_4,
                SUM(CASE WHEN e.question_score = 3 THEN 1 ELSE 0 END) AS score_3,
                SUM(CASE WHEN e.question_score = 2 THEN 1 ELSE 0 END) AS score_2,
                SUM(CASE WHEN e.question_score = 1 THEN 1 ELSE 0 END) AS score_1,
                AVG(e.question_score) AS average_score
            FROM evaluation_table e
            INNER JOIN question_list q ON e.question_id = q.question_id
            INNER JOIN criteria c ON q.criteria_id = c.criteria_id
            WHERE e.academic_year = ? AND e.student_ID IN (
                SELECT student_ID FROM student_info WHERE student_SECTION = ?
            ) AND c.criteria_name = ?
            GROUP BY q.question_text
        ";
        $stmt = mysqli_prepare($connection, $questions_query);
        mysqli_stmt_bind_param($stmt, "sss", $selected_academic_year, $selected_section, $criteria_name);
        mysqli_stmt_execute($stmt);
        $questions_result = mysqli_stmt_get_result($stmt);

        echo "<table border='1'>";
        echo "<thead><tr><th class='question-list'>Question</th><th>Rating 5 (%)</th><th>Rating 4 (%)</th><th>Rating 3 (%)</th><th>Rating 2 (%)</th><th>Rating 1 (%)</th><th>Average Score</th></tr></thead>";
        echo "<tbody>";
        while ($question_row = mysqli_fetch_assoc($questions_result)) {
            $question_text = htmlspecialchars($question_row['question_text']);
            $score_5 = $question_row['score_5'];
            $score_4 = $question_row['score_4'];
            $score_3 = $question_row['score_3'];
            $score_2 = $question_row['score_2'];
            $score_1 = $question_row['score_1'];
            $average_score = $question_row['average_score'];

            $total_responses = $score_5 + $score_4 + $score_3 + $score_2 + $score_1;
            $percent_5 = ($total_responses > 0) ? round(($score_5 / $total_responses) * 100, 2) : 0;
            $percent_4 = ($total_responses > 0) ? round(($score_4 / $total_responses) * 100, 2) : 0;
            $percent_3 = ($total_responses > 0) ? round(($score_3 / $total_responses) * 100, 2) : 0;
            $percent_2 = ($total_responses > 0) ? round(($score_2 / $total_responses) * 100, 2) : 0;
            $percent_1 = ($total_responses > 0) ? round(($score_1 / $total_responses) * 100, 2) : 0;

            echo "<tr><td>$question_text</td><td>$percent_5%</td><td>$percent_4%</td><td>$percent_3%</td><td>$percent_2%</td><td>$percent_1%</td><td>$average_score</td></tr>";
        }
        echo "</tbody></table>";
    }
}
mysqli_close($connection);

function get_subject_code_for_section($connection, $section) {
    
    $query = "SELECT DISTINCT subject_code FROM subjects WHERE subject_code LIKE CONCAT('%', ?, '%')";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $section);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['subject_code'] : null;
}
?>
</div>
  </div>
</div>
<script>
function printBoxBody() {
    window.print();
}
</script>
</body>
</html>

    