<?php
include('../connection.php');

function handlePostRequest($connection) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            isset($_POST['student_id']) && !empty($_POST['student_id']) && 
            isset($_POST['faculty_id']) && !empty($_POST['faculty_id']) &&
            isset($_POST['rating']) && !empty($_POST['rating']) &&
            isset($_POST['year_id']) && !empty($_POST['year_id']) &&
            isset($_POST['subject_code']) && !empty($_POST['subject_code'])
        ) {
            $studentId = mysqli_real_escape_string($connection, $_POST['student_id']);
            $facultyId = mysqli_real_escape_string($connection, $_POST['faculty_id']);
            $ratings = $_POST['rating']; // Assuming ratings is an array
            $academicYear = mysqli_real_escape_string($connection, $_POST['year_id']);
            $subjectCode = mysqli_real_escape_string($connection, $_POST['subject_code']);

            foreach ($ratings as $questionId => $rating) {
                $questionId = mysqli_real_escape_string($connection, $questionId);
                $rating = mysqli_real_escape_string($connection, $rating);
                $insertQuery = "INSERT INTO evaluation_table 
                                (student_ID, FacultyID, question_id, question_score, status, academic_year, subject_code) 
                                VALUES ('$studentId', '$facultyId', '$questionId', '$rating', 'Evaluated', '$academicYear', '$subjectCode')";
                $insertResult = mysqli_query($connection, $insertQuery);
                if (!$insertResult) {
                    // Log the error
                    error_log("Error inserting rating: " . mysqli_error($connection));
                    return false;
                }
            }
            return true;
        } else {
            // Log the error
            error_log("Error: Missing form data.");
            return false;
        }
    } else {
        // Log the error
        error_log("Error: Invalid request method.");
        return false;
    }
}

$isSubmitted = handlePostRequest($connection);

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluation Submission</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9));
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            text-align: center;
            z-index: 1;
        }

        .success-message {
            color: rgb(0, 247, 0);
            font-size: 18px;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: darkblue;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('exact_school_front.png');
            background-size: cover;
            background-position: center;
            filter: blur(3px); 
            z-index: 0;
        }
    </style>
</head>
<body>
<div class="background"></div>
<div class="container">
        <?php if ($isSubmitted) : 
            
            ?>
            <p class="success-message">Evaluation submitted successfully! <br>Thank your evaluating the teacher!<br><br>by Team Quiet :)</p>
            <a href="evaluation_menu.php?studentId=<?php echo urlencode($_POST['student_id']); ?>" class="btn">Go to Evaluation Menu</a>
        <?php else : 
            echo $_POST['student_id']?>
            <p class="error-message">There was an error submitting your evaluation. Please try again.</p>
            <a href="evaluation_menu.php?studentId=<?php echo urlencode($_POST['student_id']); ?>" class="btn">Back to Evaluation Menu</a>
        <?php endif; ?>
    </div>
</body>
</html>
