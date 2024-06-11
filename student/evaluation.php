<?php
include('../connection.php');
include('../session_detector.php');
function submitEvaluation($connection, $studentId, $facultyId, $facultyName, $subjectName, $year, $semesterText, $criteria)
{
    $evaluationSubmitted = true;

    if ($evaluationSubmitted) {
        echo '<script>alert("Evaluation submitted successfully!");</script>';
    } else {
        echo '<script>alert("Failed to submit evaluation. Please try again.");</script>';
    }
}

if ($connection) {
    $criteriaQuery = "SELECT * FROM criteria ORDER BY criteria_order";
    $criteriaResult = mysqli_query($connection, $criteriaQuery);

    $criteria = [];

    if ($criteriaResult) {
        while ($row = mysqli_fetch_assoc($criteriaResult)) {
            $criteria[] = $row;
        }
    } else {
        echo "Error: " . mysqli_error($connection);
    }

    $facultyName = isset($_GET['faculty_name']) ? urldecode($_GET['faculty_name']) : '';
    $studentId = isset($_GET['studentId']) ? urldecode($_GET['studentId']) : '';
    $subject = isset($_GET['subject']) ? urldecode($_GET['subject']) : '';
    $academicYearId = isset($_GET['academic_year_id']) ? urldecode($_GET['academic_year_id']) : '';
    $subjectCode = isset($_GET['subject_code']) ? urldecode($_GET['subject_code']) : '';

    $academicYearQuery = "SELECT * FROM academic_list WHERE id = '$academicYearId'";
    $academicYearResult = mysqli_query($connection, $academicYearQuery);

    $defaultAcademicYear = mysqli_fetch_assoc($academicYearResult);

    $semesterText = ($defaultAcademicYear['semester'] == 1) ? "1st" : "2nd";

    if ($academicYearResult) {
        while ($row = mysqli_fetch_assoc($academicYearResult)) {
            $defaultAcademicYear = $row;
        }
    } else {
        echo "Error: " . mysqli_error($connection);
    }

    $evaluationCheckQuery = "SELECT * FROM evaluation_table 
                             WHERE student_id = '$studentId' 
                             AND FacultyID = (SELECT FacultyID FROM facultymembers WHERE Name = '$facultyName')
                             AND academic_year = '$academicYearId'
                             AND subject_code = '$subjectCode'";
    $evaluationCheckResult = mysqli_query($connection, $evaluationCheckQuery);
    $isEvaluated = mysqli_num_rows($evaluationCheckResult) > 0;

} else {
    echo "Failed to connect to the database.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Evaluation</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<style>
    .criteria-form {
        margin-top: 20px;
    }

    .criteria-form label {
        display: block;
        margin-bottom: 5px;
    }

    .criteria-form input[type="radio"] {
        margin-right: 10px;
    }

    .prev-button, .next-button, .submit-button {
        background-color: darkblue;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-right: 10px;
    }

    .submit-button:hover {
        background-color: #0056b3;
    }

    .criteria-section {
        display: none;
    }

    .show {
        display: block;
    }

    .box-body {
        height: 500px!important;
        display: block;
        margin-bottom: 50px!important;
    }

    .box-header {
        height: 130px!important;
        text-align: left!important;
        display: flex;
        flex: 1;
    }

    .box-header h3 {
        margin-top: -10px;
    }

    .box-body .button-group {
        margin-bottom: 100%;
    }

    .evaluation-button {
        background-color: darkblue;
        min-width: 120px;
        margin-right: 0px;
        margin-left: -10px;
        padding-left: 15px;
        border-radius: 10px;
    }

    .button-footer {
        background: white;
        width: 300px;
        padding: 10px;
        position: fixed;
        right: 20px;
        bottom: 35px;
        color: black;
        border: 3px solid #a0a0a0;
        border-radius: 10px;
    }

    .overlay2 {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background: rgba(0, 0, 0, 0.3);
        opacity: 0;
        pointer-events: none;
        z-index: 1;
        transition: opacity 0.3s ease;
    }

    .modal-box2 {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9));
        max-width: 380px;
        border-radius: 10px;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .overlay2.active {
        opacity: 1;
        pointer-events: auto;
    }

    .modal-box2.active {
        opacity: 1;
        pointer-events: auto;
    }
    .modal-box2 h2 {
        margin-top: 20px;
        font-size: 25px;
        font-weight: 500;
        color: rgb(255, 255, 255);
    }
    .modal-box2 h3 {
        margin-top: 20px;
        font-size: 15px;
        font-weight: 500;
        color: rgb(255, 255, 255);
    }
    .modal-box2 i {
        font-size: 70px;
        color: #ff0000;
    }

    .popup {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        max-width: 400px;
        z-index: 1000;
    }

    .popup button {
        margin: 10px;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .question {
        width: 50%;
    }
    .unanswered-criteria{
        color: red;
    }
    .content{
        height: 100%;
    }
</style>

<body>
<?php include 'sidebar.php'; ?>
    <div class="content">
        <h1>FACULTY EVALUATION</h1><br><br>
        <br><br>
        <div class="box-header">
            <h3>
                Name of Instructor: <?php echo $facultyName; ?><br>
                Subject: <?php echo $subject; ?><br>
                School Year/Semester: <?php echo $defaultAcademicYear['year'] . ', ' . $semesterText; ?> Semester<br><br>

                Please honestly and objectively evaluate your Instructor to help improve classroom instruction. Press the
                radio button that corresponds to the rating that best describes him/her.
            </h3>

            <?php $counter = 0; ?>
        </div>
        <div class="box-body">
            <div class="criteria-form">
                <?php if ($isEvaluated) : ?>
                    <h3>You have already evaluated this instructor for the selected academic year and semester.</h3>
                <?php else : ?>
                    <form action="process_evaluation.php" method="POST">
                    <input type="hidden" name="faculty_id" value="<?php echo htmlspecialchars($_GET['facultyId']); ?>">
                    <input type="hidden" name="year_id" value="<?php echo htmlspecialchars($defaultAcademicYear['id']); ?>">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($_GET['studentId']); ?>">
                    <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subjectCode); ?>">
                        <?php foreach ($criteria as $criterion) : ?>
                            <div class="criteria-section <?php echo $counter === 0 ? 'show' : ''; ?>">
                                <h3><?php echo $criterion['criteria_name']; ?></h3>
                                <table border="1">
                                    <tr>
                                        <th class="question">Question Text</th>
                                        <th colspan="5">Ratings:</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>5<br>(Excellent)</th>
                                        <th>4<br>(Very Good)</th>
                                        <th>3<br>(Good)</th>
                                        <th>2<br>(Fair)</th>
                                        <th>1<br>(Needs Improvement)</th>
                                    </tr>
                                    <?php
                                    $questionsQuery = "SELECT * FROM question_list WHERE criteria_id = '{$criterion['criteria_id']}'";
                                    $questionsResult = mysqli_query($connection, $questionsQuery);
                                    while ($question = mysqli_fetch_assoc($questionsResult)) :
                                    ?>
                                        <tr>
                                            <td><?php echo $question['question_text']; ?></td>
                                            <td><input type="radio" name="rating[<?php echo $question['question_id']; ?>]" value="5" required></td>
                                            <td><input type="radio" name="rating[<?php echo $question['question_id']; ?>]" value="4"></td>
                                            <td><input type="radio" name="rating[<?php echo $question['question_id']; ?>]" value="3"></td>
                                            <td><input type="radio" name="rating[<?php echo $question['question_id']; ?>]" value="2"></td>
                                            <td><input type="radio" name="rating[<?php echo $question['question_id']; ?>]" value="1"></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                            <?php $counter++; ?>
                        <?php endforeach; ?>
                        <?php if ($counter === count($criteria)) : ?>
                            <div class="button-footer">
                                <button type="button" class="prev-button" id="prevButton" onclick="changeCriteria(-1)">Previous</button>
                                [<a id='criteria-count'>1</a> / <?php echo "$counter" ?>]
                                <button type="button" class="next-button" id="nextButton" onclick="changeCriteria(1)">&nbsp;&nbsp;&nbsp;Next&nbsp;&nbsp;&nbsp;</button>
                                <button type="button" class="submit-button" id="submit-button" onclick="showOverlayAndModal()">
                                    <i class="fa fa-sign-in-alt">&nbsp;&nbsp;&nbsp;Submit</i>
                                </button>
                            </div>
                   
                        <?php endif; ?>
                    
                <?php endif; ?>
            </div>
        </div>
    
    <section class="overlay2">
        <div class="modal-box2">
            <i class="fas fa-exclamation-triangle"></i>
            <h2>Are you sure you want to submit?</h2>
            <h3>You cannot edit your evaluation once submitted.</h3>
            <ul class="unanswered-criteria"id="incomplete-criteria-list"></ul>
            <div class="buttons">
                    <button type="submit" class="yes-btn">Yes</button>
                    <button type="button" class="close-btn" onclick="closeOverlay2()">No</button>
                </form>
            </div>
        </div>
        </section>
        </div>
   
   
        <script>
        var currentCriteria = 0;
        var criteriaSections = document.querySelectorAll('.criteria-section');
        var nextButton = document.getElementById('nextButton');
        var submitButton = document.getElementById('submit-button');
        var displayElement = document.getElementById("criteria-count");

        submitButton.style.display = 'none';

        function changeCriteria(delta) {
            currentCriteria += delta;
            if (currentCriteria < 0) {
                currentCriteria = 0;
            } else if (currentCriteria >= criteriaSections.length) {
                currentCriteria = criteriaSections.length - 1;
            }

            for (var i = 0; i < criteriaSections.length; i++) {
                if (i === currentCriteria) {
                    criteriaSections[i].classList.add('show');
                } else {
                    criteriaSections[i].classList.remove('show');
                }
            }

            displayElement.innerHTML = currentCriteria + 1;

            if (currentCriteria === criteriaSections.length - 1) {
                nextButton.style.display = 'none';
                submitButton.style.display = 'inline-block';
            } else {
                nextButton.style.display = 'inline-block';
                submitButton.style.display = 'none';
            }
        }

        function showOverlayAndModal() {
            var overlay = document.querySelector('.overlay2');
            var modal = document.querySelector('.modal-box2');
            var incompleteList = document.getElementById('incomplete-criteria-list');
            incompleteList.innerHTML = '<h3>Note! Yes button is not functional until you complete all of the ff:</h3><br>Incomplete Criterias:';

            var allQuestions = document.querySelectorAll('.criteria-section');
            var allSelected = true;
            var incompleteCriteria = [];

            allQuestions.forEach(function(section, sectionIndex) {
                var sectionIncomplete = false;
                var questions = section.querySelectorAll('tr');
                questions.forEach(function(question, index) {
                    if (index > 1) { // Skip header rows
                        var radioButtons = question.querySelectorAll('input[type="radio"]');
                        var isChecked = false;
                        radioButtons.forEach(function(radioButton) {
                            if (radioButton.checked) {
                                isChecked = true;
                            }
                        });
                        if (!isChecked) {
                            allSelected = false;
                            sectionIncomplete = true;
                        }
                    }
                });
                if (sectionIncomplete) {
                    incompleteCriteria.push(section.querySelector('h3').textContent);
                }
            });

            if (allSelected) {
                overlay.classList.add('active');
                modal.classList.add('active');
                incompleteList.innerHTML += ' None';
            } else {
                incompleteCriteria.forEach(function(criteria) {
                    var listItem = document.createElement('li');
                    listItem.textContent = criteria;
                    incompleteList.appendChild(listItem);
                });
                alert("SUBMISSION FAILED! Please complete all ratings before submitting. Check the incomplete criteria sections listed in the popup.");
                overlay.classList.add('active');
                modal.classList.add('active');
            }
        }

        function closeOverlay2() {
            var overlay2 = document.querySelector('.overlay2');
            var modal = document.querySelector('.modal-box2');
            overlay2.classList.remove('active');
            modal.classList.remove('active');
        }
    </script>
</body>
</html>

<?php
mysqli_close($connection);
?>
