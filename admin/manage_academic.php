<?php
include('../connection.php');


if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$academic_year_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($academic_year_id === null) {
    echo "Invalid academic year ID.";
    exit;
}

$query = "SELECT * FROM academic_list WHERE id = $academic_year_id";
$result = mysqli_query($connection, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "Academic year not found.";
    exit;
}
$academic_year = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_academic_year'])) {
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $status = $_POST['status'];

    $update_query = "UPDATE academic_list SET year='$year', semester='$semester', status='$status' WHERE id = $academic_year_id";
    if (mysqli_query($connection, $update_query)) {
        echo "<p class='below-title'>Updating academic year details...</p>";
        header("Refresh:1");
    } else {
        echo "<p class='below-title'>Error updating academic year details: " . mysqli_error($connection) . "</p>";
    }
}


$order_by = "";
$selected_filter = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_filter'])) {
    $score_filter = $_POST['score_filter'];
    $selected_filter = $score_filter;
    if ($score_filter == 'highest') {
        $order_by = "ORDER BY overall_rating DESC";
    } elseif ($score_filter == 'lowest') {
        $order_by = "ORDER BY overall_rating ASC";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Academic Year</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .below-title {
        background: linear-gradient(to top, rgb(66, 78, 255), rgb(49, 0, 208));
        width: 1245px;
        padding: 10px;
        position: fixed;
        margin-top: 50px;
        margin-left: 260px;
        color: white;
        top: 30px;
        border-bottom: 3px solid #002594; 
    }

    .academic-button {
        background-color: darkblue;
        min-width: 120px;
        margin-right: 0px;
        margin-left: -10px;
        padding-left: 15px;
        border-radius: 10px;
    }
    .box-header{
        width: 1220px!important;
        text-align: left!important;
    }
    .box-body{
        width: 1220px!important;
    }
    .table-row-name{
        width: 40%;
    }
    table{
        width: 100%!important;
    }
    .table-row-student{
        width: 30%;
    }
    .table-row-count{
        width: 30%;
    }
    .back-academic{
        margin-left: 1100px;
        background-color: #0055ff;
        color: rgb(255, 255, 255);
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        text-decoration: none;
    }
</style>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <h1>Manage Academic Year</h1><br><br><br>
        
        <div class="box-header">
            <h2>Edit Academic Year: <?php echo $academic_year['year']; ?></h2><a class="back-academic" href="academic_year.php"><i class='fas fa-arrow-left'></i>Back</a>
            <form method="post">
                <label for="year">Year:</label>
                <input type="text" id="year" name="year" value="<?php echo $academic_year['year']; ?>"><br>
                <label for="semester">Semester:</label>
                <select id="semester" name="semester">
                    <option value="1" <?php if ($academic_year['semester'] == 1) echo "selected"; ?>>1st</option>
                    <option value="2" <?php if ($academic_year['semester'] == 2) echo "selected"; ?>>2nd</option>
                    <option value="3" <?php if ($academic_year['semester'] == 3) echo "selected"; ?>>3rd</option>
                </select><br>
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="0" <?php if ($academic_year['status'] == 0) echo "selected"; ?>>Finished</option>
                    <option value="2" <?php if ($academic_year['status'] == 2) echo "selected"; ?>>Start</option>
                </select><br><br>
                <input type="hidden" name="update_academic_year" value="1">
                <input type="submit" value="Update">
            </form>
        </div>

        <div class="box-body">
            <h2>Faculty List</h2>
            <form method="post">
                <label for="score_filter">Filter by Score:</label>
                <select id="score_filter" name="score_filter">
                    <option value="highest" <?php if ($selected_filter == 'highest') echo "selected"; ?>>Highest Score</option>
                    <option value="lowest" <?php if ($selected_filter == 'lowest') echo "selected"; ?>>Lowest Score</option>
                </select>
                <input type="hidden" name="apply_filter" value="1">
                <input type="submit" value="Apply Filter">
            </form>

            <table border="1">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>No. of Students Evaluated / Total Enrolled</th>
                        <th>Overall Rating Score</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $faculty_query = "
                        SELECT 
                            facultymembers.*, 
                            (SELECT AVG(question_score) FROM evaluation_table WHERE FacultyID = facultymembers.FacultyID AND academic_year = $academic_year_id) AS overall_rating,
                            (SELECT COUNT(DISTINCT student_ID) FROM evaluation_table WHERE FacultyID = facultymembers.FacultyID AND academic_year = $academic_year_id) AS student_count,
                            (SELECT COUNT(DISTINCT enrollments.student_ID) 
                                FROM enrollments 
                                JOIN subjects ON enrollments.subject_code = subjects.subject_code
                                WHERE subjects.FacultyID = facultymembers.FacultyID) AS total_enrolled
                        FROM facultymembers
                        $order_by
                    ";
                    $faculty_result = mysqli_query($connection, $faculty_query);
                    if ($faculty_result && mysqli_num_rows($faculty_result) > 0) {
                        while ($row = mysqli_fetch_assoc($faculty_result)) {
                            $overall_rating = $row['overall_rating'] / 5;
                            $percentage_score = ($overall_rating) * 100;

                            $performance = "";
                            if ($percentage_score >= 92) {
                                $performance = "Excellent";
                            } elseif ($percentage_score >= 74) {
                                $performance = "Very Good";
                            } elseif ($percentage_score >= 56) {
                                $performance = "Good";
                            } elseif ($percentage_score >= 38) {
                                $performance = "Fair";
                            } else {
                                $performance = "Needs Improvement";
                            }

                            
                            $faculty_id = $row['FacultyID'];
                            $check_query = "SELECT COUNT(*) AS num_rows FROM evaluation_summary WHERE faculty_id = ? AND academic_id = ?";
                            $stmt_check = mysqli_prepare($connection, $check_query);
                            mysqli_stmt_bind_param($stmt_check, "ss", $faculty_id, $academic_year_id);
                            mysqli_stmt_execute($stmt_check);
                            $result_check = mysqli_stmt_get_result($stmt_check);
                            $row_check = mysqli_fetch_assoc($result_check);
                            $num_rows = $row_check['num_rows'];

                            if ($num_rows > 0) {
                                
                                $update_query = "UPDATE evaluation_summary SET avg_rating = ? WHERE faculty_id = ? AND academic_id = ?";
                                $stmt_update = mysqli_prepare($connection, $update_query);
                                mysqli_stmt_bind_param($stmt_update, "dss", $percentage_score, $faculty_id, $academic_year_id);
                                mysqli_stmt_execute($stmt_update);
                            } else {
                          
                                $insert_query = "INSERT INTO evaluation_summary (faculty_id, academic_id, avg_rating) VALUES (?, ?, ?)";
                                $stmt_insert = mysqli_prepare($connection, $insert_query);
                                mysqli_stmt_bind_param($stmt_insert, "ssd", $faculty_id, $academic_year_id, $percentage_score);
                                mysqli_stmt_execute($stmt_insert);
                            }

                            echo "<tr>";
                            echo "<td class='table-row-name'><a href='teacher_summary.php?FacultyID=" . $row['FacultyID'] . "&academic_year_id=" . $academic_year_id .
                                "&academic_year=" . urlencode($academic_year['year']) ."&semester=" . urlencode($academic_year['semester']) . "'>" . $row['Name'] . "</a></td>";
                            echo "<td class='table-row-student'>" . $row['student_count'] . " / " . $row['total_enrolled'] . "</td>";
                            echo "<td class='table-row-ratings'>" . round($overall_rating * 100, 2) . "</td>";
                            echo "<td class='table-row-ratings'>" . $performance . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No faculty members found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($connection);
?>
