<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subject Summary</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .performance-table {
            width: 300px;
            height: 1%;
            margin-left: 45%;
        }
        .academic-button {
            background-color: darkblue;
            min-width: 120px;
            margin-right: 0px;
            margin-left: -10px;
            padding-left: 15px;
            border-radius: 10px;
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
        table {
            text-align: left;
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
            height: 210px!important;
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
        .horizontal-nav .subject-button{
           
           border-bottom: 5px solid white!important;
        }
        .question-list{
            width: 75%;
            
        }
        .questions{
            text-align: left!important;
        }
        .criteria-head {
            height: 50px;
            background-color: #a4a4a4;
        }
        .subject-head {
            height: 30px;
            background-color: gray;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <h1>Subject List Summary</h1><br><br><br>
        
        <?php
        include('../connection.php');
        if (!$connection) {
            die("Connection failed: " . mysqli_connect_error());
        }

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

        $subjects_query = "SELECT DISTINCT s.Name AS subject_name FROM evaluation_table e JOIN subjects s ON e.subject_code = s.subject_code WHERE e.FacultyID = ?";
        $stmt = mysqli_prepare($connection, $subjects_query);
        mysqli_stmt_bind_param($stmt, "s", $facultyID);
        mysqli_stmt_execute($stmt);
        $subjects_result = mysqli_stmt_get_result($stmt);

        $selected_subject = $_GET['subject'] ?? 'all';
        ?>

        <?php
        $student_count_query = "
        SELECT COUNT(DISTINCT student_id) AS student_count
        FROM evaluation_table
        WHERE FacultyID = ? AND academic_year = ?
    ";
    $stmt = mysqli_prepare($connection, $student_count_query);
    mysqli_stmt_bind_param($stmt, "ss", $facultyID, $academic_year_id);
    mysqli_stmt_execute($stmt);
    $student_count_result = mysqli_stmt_get_result($stmt);
    $student_count = mysqli_fetch_assoc($student_count_result)['student_count'];

        $overall_avg_score = 0;
        $total_scores_sum = 0;
        $total_responses_sum = 0;

        if ($selected_subject !== 'all') {
           
            $query = "
                SELECT 
                    s.Name AS subject_name,
                    c.criteria_name AS criteria,
                    q.question_text,
                    AVG(e.question_score) AS avg_score,
                    SUM(CASE WHEN e.question_score = 5 THEN 1 ELSE 0 END) AS count_5,
                    SUM(CASE WHEN e.question_score = 4 THEN 1 ELSE 0 END) AS count_4,
                    SUM(CASE WHEN e.question_score = 3 THEN 1 ELSE 0 END) AS count_3,
                    SUM(CASE WHEN e.question_score = 2 THEN 1 ELSE 0 END) AS count_2,
                    SUM(CASE WHEN e.question_score = 1 THEN 1 ELSE 0 END) AS count_1,
                    COUNT(e.question_score) AS total_responses
                FROM evaluation_table e
                JOIN question_list q ON e.question_id = q.question_id
                JOIN criteria c ON q.criteria_id = c.criteria_id
                JOIN subjects s ON e.subject_code = s.subject_code
                WHERE e.FacultyID = ? AND e.academic_year = ? AND s.Name = ?
                GROUP BY s.Name, c.criteria_name, q.question_text
            ";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "sss", $facultyID, $academic_year_id, $selected_subject);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (!$result || mysqli_num_rows($result) === 0) {
                echo "No evaluation data found for this teacher in the selected academic year and subject.";
                echo "<a href='manage_academic.php?id=" . $academic_year_id . "' class='back'><i class='fas fa-arrow-left'></i>Back</a><br><br>";
                exit;
            }
        } else {
            
            $query = "
                SELECT 
                    s.Name AS subject_name,
                    c.criteria_name AS criteria,
                    q.question_text,
                    AVG(e.question_score) AS avg_score,
                    SUM(CASE WHEN e.question_score = 5 THEN 1 ELSE 0 END) AS count_5,
                    SUM(CASE WHEN e.question_score = 4 THEN 1 ELSE 0 END) AS count_4,
                    SUM(CASE WHEN e.question_score = 3 THEN 1 ELSE 0 END) AS count_3,
                    SUM(CASE WHEN e.question_score = 2 THEN 1 ELSE 0 END) AS count_2,
                    SUM(CASE WHEN e.question_score = 1 THEN 1 ELSE 0 END) AS count_1,
                    COUNT(e.question_score) AS total_responses
                FROM evaluation_table e
                JOIN question_list q ON e.question_id = q.question_id
                JOIN criteria c ON q.criteria_id = c.criteria_id
                JOIN subjects s ON e.subject_code = s.subject_code
                WHERE e.FacultyID = ? AND e.academic_year = ?
                GROUP BY s.Name, c.criteria_name, q.question_text
            ";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "ss", $facultyID, $academic_year_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (!$result || mysqli_num_rows($result) === 0) {
                echo "No evaluation data found for this teacher in the selected academic year.";
                echo "<a href='manage_academic.php?id=" . $academic_year_id . "' class='back'><i class='fas fa-arrow-left'></i>Back</a><br><br>";
                exit;
            }
            
                    
                        while ($row = mysqli_fetch_assoc($result)) {
                            $total_scores_sum += $row['avg_score'] * $row['total_responses'];
                            $total_responses_sum += $row['total_responses'];
                        }
                        $overall_avg_score = $total_scores_sum / $total_responses_sum / 5 * 100;
                    }
            
          
                    $performance = '';
                    if ($overall_avg_score >= 92) {
                        $performance = 'Excellent';
                    } elseif ($overall_avg_score >= 74) {
                        $performance = 'Very Good';
                    } elseif ($overall_avg_score >= 56) {
                        $performance = 'Good';
                    } elseif ($overall_avg_score >= 38) {
                        $performance = 'Fair';
                    } else {
                        $performance = 'Needs Improvement';
                    }
                    $total_avg_rating_query = "
                    SELECT avg_rating 
                    FROM evaluation_summary 
                    WHERE faculty_id = ? AND academic_id = ?
                ";
                $stmt = mysqli_prepare($connection, $total_avg_rating_query);
                mysqli_stmt_bind_param($stmt, "ss", $facultyID, $academic_year_id);
                mysqli_stmt_execute($stmt);
                $total_avg_rating_result = mysqli_stmt_get_result($stmt);
                $total_avg_rating = mysqli_fetch_assoc($total_avg_rating_result)['avg_rating'];
                    ?>
                    
                    <?php include 'summary_topbar.php'; ?>
                    
                    <div class='box-header'>
                    
        <div class='text-box'>
        <h3> Academic Year: &nbsp;<?php echo htmlspecialchars($academicYear);?>, <?php echo htmlspecialchars($semester2); ?> Semester<a href='manage_academic.php?id=<?php echo $academic_year_id; ?>' class='button'><i class='fas fa-arrow-left'></i>Back</a><br>
         <h3>Number of Evaluators:&nbsp;&nbsp; <?php echo $student_count; ?></h3><button class="print-button" onclick="printBoxBody()">Print</button>
            <h3>Evaluation Results for: <?php echo ($selected_subject === 'all') ? 'All Subjects' : htmlspecialchars($selected_subject); ?> Taught by <?php echo htmlspecialchars($faculty['Name']); ?></h3>
                        
            </div>
                        
           
            <form action="" method="GET" id="subjectForm">
            <label for="subject">Select a Subject:</label>
            <select name="subject" id="subject" onchange="document.getElementById('subjectForm').submit()">
                <option value="all" <?php if ($selected_subject === 'all') echo 'selected'; ?>>All</option>
                <?php
                while ($row = mysqli_fetch_assoc($subjects_result)) {
                    $subject_name = htmlspecialchars($row['subject_name']);
                    $selected = ($selected_subject === $subject_name) ? 'selected' : '';
                    echo "<option value='$subject_name' $selected>$subject_name</option>";
                }
                ?>
                 
            </select>
            <input type="hidden" name="FacultyID" value="<?php echo htmlspecialchars($facultyID); ?>">
            <input type="hidden" name="academic_year_id" value="<?php echo htmlspecialchars($academic_year_id); ?>">
            <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($academicYear); ?>">
            <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
        </form><br><br>
        <?php if ($selected_subject === 'all'): ?>
            <div class="overall-rating">
             <h3>Overall Subject Ratings: <?php echo round($total_avg_rating, 2); ?>% (<?php echo $performance; ?>)</h3>
            </div>
            <?php endif; ?>

            <?php if ($selected_subject !== 'all'): ?>
             <?php
    
            $subject_query = "
            SELECT AVG(avg_score) AS overall_avg
            FROM (
            SELECT AVG(e.question_score) AS avg_score
            FROM evaluation_table e
            JOIN subjects s ON e.subject_code = s.subject_code
            WHERE e.FacultyID = ? AND e.academic_year = ? AND s.Name = ?
            GROUP BY e.subject_code
            ) AS subject_avg
            ";
            $stmt = mysqli_prepare($connection, $subject_query);
            mysqli_stmt_bind_param($stmt, "sss", $facultyID, $academic_year_id, $selected_subject);
            mysqli_stmt_execute($stmt);
            $subject_result = mysqli_stmt_get_result($stmt);
            $subject_row = mysqli_fetch_assoc($subject_result);
            $subject_avg_score = $subject_row['overall_avg'] / 5 * 100;
        
            $subject_performance = '';
            if ($subject_avg_score >= 92) {
             $subject_performance = 'Excellent';
            } elseif ($subject_avg_score >= 74) {
            $subject_performance = 'Very Good';
            } elseif ($subject_avg_score >= 56) {
            $subject_performance = 'Good';
            } elseif ($subject_avg_score >= 38) {
            $subject_performance = 'Fair';
            } else {
            $subject_performance = 'Needs Improvement';
            }
            ?>

            <div class="overall-rating">
                <h3><?php echo htmlspecialchars($selected_subject); ?> Subject Ratings: <?php echo round($subject_avg_score, 2); ?>% (<?php echo $subject_performance; ?>)</h3>
            </div>
            <br>
            <?php endif; ?>
        </div>
        <div class='box-body'>
            <table border='1'>
                <thead>
                    <tr>
                    <th class="question-list">Question</th>
                    <th>Rating 5 (%)</th>
                    <th>Rating 4 (%)</th>
                    <th>Rating 3 (%)</th>
                    <th>Rating 2 (%)</th>
                    <th>Rating 1 (%)</th>
                    <th>Average Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
$current_subject = null;
$current_criteria = null;
$current_criteria_avg_score = 0;
$current_criteria_total_responses = 0;
$current_questions = array();


function scoreToPercentage($score) {
    return ($score / 5) * 100;
}

mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
    if ($current_subject !== $row['subject_name']) {
        echo "<tr>";
        echo "<th class='subject-head' colspan='8' class='subject-header'>" . htmlspecialchars($row['subject_name']) . "</th>";
        echo "</tr>";
        
        $current_subject = $row['subject_name'];
        $current_criteria = null;
        $current_criteria_avg_score = 0;
        $current_criteria_total_responses = 0;
        $current_questions = array();
    }

    if ($current_criteria !== $row['criteria']) {
        if ($current_criteria !== null) {
            $criteria_avg_score = $current_criteria_avg_score / $current_criteria_total_responses;
            $criteria_performance = getPerformance2($criteria_avg_score);
            echo "<tr>";
            echo "<th class='criteria-head' colspan='8'>" . htmlspecialchars($current_criteria) . " - " . round(scoreToPercentage($criteria_avg_score), 2) . "% (" . $criteria_performance . ")</th>";
            echo "</tr>";
        
            foreach ($current_questions as $question) {
                echo "<tr>";
                echo "<td class='questions'>" . htmlspecialchars($question['question_text']) . "</td>";
                echo "<td>" . round(scoreToPercentage($question['count_5'] / $question['total_responses']) * 5, 2) . "%</td>";
                echo "<td>" . round(scoreToPercentage($question['count_4'] / $question['total_responses']) * 5, 2) . "%</td>";
                echo "<td>" . round(scoreToPercentage($question['count_3'] / $question['total_responses']) * 5, 2) . "%</td>";
                echo "<td>" . round(scoreToPercentage($question['count_2'] / $question['total_responses']) * 5, 2) . "%</td>";
                echo "<td>" . round(scoreToPercentage($question['count_1'] / $question['total_responses']) * 5, 2) . "%</td>";
                echo "<td>" . round($question['avg_score'], 2) . "</td>";
                echo "</tr>";
            }
        }
        
        $current_criteria = $row['criteria'];
        $current_criteria_avg_score = 0;
        $current_criteria_total_responses = 0;
        $current_questions = array();
    }

    $current_criteria_avg_score += $row['avg_score'] * $row['total_responses'];
    $current_criteria_total_responses += $row['total_responses'];

    $current_questions[] = $row;
}
if ($current_criteria !== null) {
    $criteria_avg_score = $current_criteria_avg_score / $current_criteria_total_responses;
    $criteria_performance = getPerformance2($criteria_avg_score);
    echo "<tr>";
    echo "<th class='criteria-head'colspan='8'>" . htmlspecialchars($current_criteria) . " - ". round(scoreToPercentage($criteria_avg_score), 2) . "% (" . $criteria_performance . ")</th>";
    echo "</tr>";

    foreach ($current_questions as $question) {
        echo "<tr>";
        echo "<td class='questions'>" . htmlspecialchars($question['question_text']) . "</td>";
        echo "<td>" . round(scoreToPercentage($question['count_5'] / $question['total_responses']) * 5, 2) . "%</td>";
        echo "<td>" . round(scoreToPercentage($question['count_4'] / $question['total_responses']) * 5, 2) . "%</td>";
        echo "<td>" . round(scoreToPercentage($question['count_3'] / $question['total_responses']) * 5, 2) . "%</td>";
        echo "<td>" . round(scoreToPercentage($question['count_2'] / $question['total_responses']) * 5, 2) . "%</td>";
        echo "<td>" . round(scoreToPercentage($question['count_1'] / $question['total_responses']) * 5, 2) . "%</td>";
        echo "<td>" . round($question['avg_score'], 2) . "</td>";
        echo "</tr>";
    }
}
function getPerformance($avg_score) {
    if ($avg_score >= 92) {
        return 'Excellent';
    } elseif ($avg_score >= 74) {
        return 'Very Good';
    } elseif ($avg_score >= 56) {
        return 'Good';
    } elseif ($avg_score >= 38) {
        return 'Fair';
    } else {
        return 'Needs Improvement';
    }
}
function getPerformance2($criteria_avg_score) {
    if ($criteria_avg_score >= 92) {
        return 'Excellent';
    } elseif ($criteria_avg_score >= 74) {
        return 'Very Good';
    } elseif ($criteria_avg_score >= 56) {
        return 'Good';
    } elseif ($criteria_avg_score >= 38) {
        return 'Fair';
    } else {
        return 'Needs Improvement';
    }
}



                    ?>
                </tbody>
            </table>
            <br>
        </div>

        <?php mysqli_close($connection); ?>
    </div>
</body>
</html>
<script>
function printBoxBody() {
    window.print();
}

document.getElementById('subject').addEventListener('change', function() {
    document.getElementById('subjectForm').submit();
});
</script>
