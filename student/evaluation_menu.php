<?php
include('../connection.php');
include('../session_detector.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Evaluation Menu</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .evaluation-button {
            background-color: darkblue;
            min-width: 120px;
            margin-right: 0px;
            margin-left: -10px;
            padding-left: 15px;
            border-radius: 10px;
        }

        .evaluation-button:hover {
            background-color: #0056b3;
        }

        .button-closed {
            background-color: #a1a1a1;
            color: yellow;
            padding: 10px 10px;
            border: none;
            border-radius: 5px;
        }

        .button-pending {
            background-color: #ccc;
            color: blue;
            padding: 10px 10px;
            border: none;
            border-radius: 5px;
        }

        td {
            height: 50px !important;
        }

        .box-body {
            height: 68% !important;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>EVALUATION MENU</h1><br><br><br><br>
        <div class="box-header">
            <h2>Teachers To Evaluate:</h2>
        </div>
        <div class="box-body">
            <?php
            if ($connection) {
                if (isset($_GET['studentId'])) {
                    $student_ID = $_GET['studentId'];

                    $query = "SELECT * FROM student_info WHERE student_ID = '$student_ID'";
                    $result = mysqli_query($connection, $query);

                    if (mysqli_num_rows($result) > 0) {
                        $student = mysqli_fetch_assoc($result);
                        $studentId = $student['id']; 
                    } else {
                        echo "No student found with ID: $student_ID";
                        exit();
                    }

                    $enrolledSubjectsQuery = "SELECT subjects.subject_code as subject_code, subjects.Name as subject_name, facultymembers.Name as faculty_name, facultymembers.FacultyID as faculty_id 
                                              FROM enrollments 
                                              INNER JOIN subjects ON enrollments.subject_code = subjects.subject_code 
                                              INNER JOIN facultymembers ON subjects.FacultyID = facultymembers.FacultyID 
                                              WHERE enrollments.student_id = '$student_ID'";

                    $enrolledSubjectsResult = mysqli_query($connection, $enrolledSubjectsQuery);
                    $enrolledSubjects = [];

                    if (mysqli_num_rows($enrolledSubjectsResult) > 0) {
                        while ($row = mysqli_fetch_assoc($enrolledSubjectsResult)) {
                            $facultyId = $row['faculty_id'];
                            $facultyName = $row['faculty_name'];
                            $subjectName = $row['subject_name'];
                            $subjectCode = $row['subject_code'];

                            $enrolledSubjects[] = [
                                'faculty_id' => $facultyId,
                                'faculty_name' => $facultyName,
                                'subject_name' => $subjectName,
                                'subject_code' => $subjectCode,
                                'status' => 'Pending'
                            ];
                        }
                    }
                } else {
                    echo "Student ID not provided.";
                    exit();
                }

                $academicYearStatusQuery = "SELECT id, year, semester, status FROM academic_list WHERE default_select = 1";
                $academicYearStatusResult = mysqli_query($connection, $academicYearStatusQuery);

                if (mysqli_num_rows($academicYearStatusResult) > 0) {
                    $academicYearStatusRow = mysqli_fetch_assoc($academicYearStatusResult);
                    $academicYearStatus = $academicYearStatusRow['status'];
                    $academicYearId = $academicYearStatusRow['id'];
                    $academicYear = $academicYearStatusRow['year'];
                    $semester = $academicYearStatusRow['semester'];
                } else {
                    echo "<div style='margin: 20px; padding: 20px; border: 1px solid red; background-color: #fdd;'>
                            <strong>No default academic year is selected by the admin.</strong><br>
                            No teachers can be evaluated. Please coordinate with your instructor for more details.
                          </div>";
                    exit();
                }
            } else {
                echo "Failed to connect to the database.";
                exit();
            }
            ?>
            <div class="enrolled-subjects">
                <table border="1">
                    <tr>
                        <th>Faculty Name</th>
                        <th>Subject</th>
                        <th>Evaluation Status</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($enrolledSubjects as $data) : ?>
                        <tr>
                            <td><?php echo $data['faculty_name']; ?></td>
                            <td><?php echo $data['subject_name']; ?></td>
                            <td>
                                <?php
                                $conn = mysqli_connect($host, $user, $pass, $dbname);
                                if (!$conn) {
                                    echo "Failed to connect to the database.";
                                    exit();
                                }

                                $facultyIdEscaped = mysqli_real_escape_string($conn, $data['faculty_id']);
                                $subjectCodeEscaped = mysqli_real_escape_string($conn, $data['subject_code']);
                                $evaluationStatusQuery = "SELECT status FROM evaluation_table 
                                                          WHERE student_id = '$student_ID' 
                                                          AND FacultyID = '$facultyIdEscaped'
                                                          AND subject_code = '$subjectCodeEscaped'
                                                          AND academic_year = '$academicYearId'";
                                $evaluationStatusResult = mysqli_query($conn, $evaluationStatusQuery);

                                if (!$evaluationStatusResult) {
                                    echo "Error fetching evaluation status: " . mysqli_error($conn);
                                    exit();
                                }

                                $evaluationStatusRow = mysqli_fetch_assoc($evaluationStatusResult);
                                $evaluationStatus = $evaluationStatusRow['status'] ?? 'Pending';
                                echo ($evaluationStatus == 'Evaluated') ? 'Evaluated' : 'Pending';

                                mysqli_close($conn);
                                ?>
                            </td>
                            <td>
                                <?php if ($academicYearStatus == 0 || $evaluationStatus == 'Evaluated') : ?>
                                    <button class="button-closed" disabled><?php echo $evaluationStatus == 'Evaluated' ? 'Evaluated' : 'Closed'; ?></button>
                                <?php elseif ($academicYearStatus == 1 && $evaluationStatus == 'Pending') : ?>
                                    <button class="button-pending" disabled>Pending</button>
                                <?php else : ?>
                                    <?php
                                    $conn = mysqli_connect($host, $user, $pass, $dbname);
                                    if (!$conn) {
                                        echo "Failed to connect to the database.";
                                        exit();
                                    }

                                    $facultyId = $data['faculty_id'];
                                    $subjectCode = $data['subject_code'];
                                    ?>
                                    <a href="evaluation.php?studentId=<?php echo $student_ID; ?>&facultyId=<?php echo $facultyId; ?>&subject_code=<?php echo $subjectCode; ?>&subject=<?php echo $subjectName; ?>&faculty_name=<?php echo urlencode($data['faculty_name']); ?>&academic_year_id=<?php echo $academicYearId; ?>" class="button">Evaluate</a>
                                    <?php
                                    mysqli_close($conn);
                                    ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
