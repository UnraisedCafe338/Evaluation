<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Summary</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        .horizontal-nav .overall-button{
           border-bottom: 5px solid white;
        }
        .question-list{
            width: 75%;
        }
        .questions{
            text-align: left!important;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        
        <h1>Teacher Overall Summary</h1><br><br><br>
       
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

        $query = "
            SELECT 
                c.criteria_name AS criteria,
                c.criteria_order,
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
            WHERE e.FacultyID = ? AND e.academic_year = ?
            GROUP BY c.criteria_name, c.criteria_order, q.question_text
            ORDER BY c.criteria_order
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

        $criteria_data = [];
        $total_avg_score_sum = 0;
        $total_questions = 0;

        $total_count_5 = 0;
        $total_count_4 = 0;
        $total_count_3 = 0;
        $total_count_2 = 0;
        $total_count_1 = 0;
        $total_responses_sum = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $criteria = $row['criteria'];
            $criteria_order = $row['criteria_order'];
            if (!isset($criteria_data[$criteria_order])) {
                $criteria_data[$criteria_order] = [
                    'criteria' => $criteria,
                    'questions' => [],
                    'total_avg_score' => 0,
                    'total_responses' => 0
                ];
            }
            $criteria_data[$criteria_order]['questions'][] = $row;
            $criteria_data[$criteria_order]['total_avg_score'] += $row['avg_score'] * $row['total_responses'];
            $criteria_data[$criteria_order]['total_responses'] += $row['total_responses'];

            $total_count_5 += $row['count_5'];
            $total_count_4 += $row['count_4'];
            $total_count_3 += $row['count_3'];
            $total_count_2 += $row['count_2'];
            $total_count_1 += $row['count_1'];
            $total_responses_sum += $row['total_responses'];

            $total_avg_score_sum += $row['avg_score'];
            $total_questions++;
        }

        function classify_score($score) {
            if ($score >= 92) return "Excellent";
            if ($score >= 74) return "Very Good";
            if ($score >= 56) return "Good";
            if ($score >= 38) return "Fair";
            return "Needs Improvement";
        }

        // Fetch total average rating from evaluation_summary table
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
           <div class="box-header">
             <div class="top-bar">
        
    </div>
            <div class="text-box">
                 
               
                <h3> Academic Year: &nbsp;<?php echo htmlspecialchars($academicYear);?>, <?php echo htmlspecialchars($semester2); ?> Semester <a href='manage_academic.php?id=<?php echo $academic_year_id; ?>' class='button'><i class='fas fa-arrow-left'></i>Back</a>
                <h3>Evaluation Results: &nbsp;<?php echo htmlspecialchars($faculty['Name']); ?></h3><button class="print-button" onclick="printBoxBody()">Print</button>
                <h3>Number of Evaluators:&nbsp;&nbsp; <?php echo $student_count; ?></h3>
            </div>
            <form action="manage_academic.php" method="GET">
                <input type="hidden" name="academic_year_id" value="<?php echo $academic_year_id; ?>">
            </form>
        </div>

        <div class="box-body">
            <table border="1">
                <thead>
                    <tr>
                        <th colspan="8">Overall Ratings</th>
                    </tr>
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
                    
                    ksort($criteria_data);
                    foreach ($criteria_data as $criteria_order => $data) {
                        $criteria_avg_score = round(($data['total_avg_score'] / $data['total_responses'] - 1) / 4 * 100, 2);
                        $classification = classify_score($criteria_avg_score);
                        
                        echo "<tr>";
                        echo "<th colspan='8' class='criteria-head'><strong>Criteria: " . htmlspecialchars($data['criteria']) . " - Average Score: " . $criteria_avg_score . "% (" . $classification . ")</strong></th>";
                        echo "</tr>";

                        foreach ($data['questions'] as $row) {
                            $total_responses = $row['total_responses'];
                            $percent_5 = ($row['count_5'] / $total_responses) * 100;
                            $percent_4 = ($row['count_4'] / $total_responses) * 100;
                            $percent_3 = ($row['count_3'] / $total_responses) * 100;
                            $percent_2 = ($row['count_2'] / $total_responses) * 100;
                            $percent_1 = ($row['count_1'] / $total_responses) * 100;

                            echo "<tr>";
                            echo "<td class='questions'>" . htmlspecialchars($row['question_text']) . "</td>";
                            echo "<td>" . round($percent_5, 2) . "%</td>";
                            echo "<td>" . round($percent_4, 2) . "%</td>";
                            echo "<td>" . round($percent_3, 2) . "%</td>";
                            echo "<td>" . round($percent_2, 2) . "%</td>";
                            echo "<td>" . round($percent_1, 2) . "%</td>";
                            echo "<td>" . round($row['avg_score'], 2) . "</td>";
                            echo "</tr>";
                        }
                    }

                    $total_percent_5 = ($total_count_5 / $total_responses_sum) * 100;
                    $total_percent_4 = ($total_count_4 / $total_responses_sum) * 100;
                    $total_percent_3 = ($total_count_3 / $total_responses_sum) * 100;
                    $total_percent_2 = ($total_count_2 / $total_responses_sum) * 100;
                    $total_percent_1 = ($total_count_1 / $total_responses_sum) * 100;
                    ?>
                    <tr>
                        <td class="total-score-text"><strong>Total Average Score</strong></td>
                        <td class="total-score-num"><strong><?php echo round($total_percent_5, 2); ?>%</strong></td>
                        <td class="total-score-num"><strong><?php echo round($total_percent_4, 2); ?>%</strong></td>
                        <td class="total-score-num"><strong><?php echo round($total_percent_3, 2); ?>%</strong></td>
                        <td class="total-score-num"><strong><?php echo round($total_percent_2, 2); ?>%</strong></td>
                        <td class="total-score-num"><strong><?php echo round($total_percent_1, 2); ?>%</strong></td>
                        <td class="total-score-num"><strong><?php echo round($total_avg_rating, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <br>
        </div>
    </div>

    <script>
        function printBoxBody() {
            window.print();
        }
    </script>
</body>
</html>

<?php
mysqli_close($connection);
?>
