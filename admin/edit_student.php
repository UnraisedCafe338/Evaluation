<?php
    ob_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<style>
        .below-title {
        background: linear-gradient(to top, rgb(66, 78, 255), rgb(49, 0, 208));
        width: 3000px;
        padding: 20px;
        position: fixed;
        margin-top: 35px;
        margin-left: -19px;
        color: white;
        top: 30px;
        border-bottom: 3px solid #002594; 
    }

    .students-button {
        background-color: darkblue;
        min-width: 120px; 
        margin-right: 0px;
        margin-left: -10px;
        padding-left: 15px;
        border-radius: 10px;
    }

    .success {
        color: goldenrod;
    }
    .enrolled_subjects td {
    font-weight: normal; 
    font-size: 15px;
    text-align: center;
}
.enrolled_table{
    width: 90%;
}
th {
    font-size: 15px;
}
.popup {
    display: none; 
    color: goldenrod;
    font-size:20px;
}
table {
    width: 100%!important;
   
}

.id-column {
    width: 10%!important;
}
.box-body{
    width: 172%!important;
}
.box-header{
    
    width: 172%!important;
}
.table-header td {

border: 0px solid black;
font-size: 15px;
}
.table-header{
margin-top: -100px;
margin-bottom: -20px;
} 
.update{
    text-align: right;
}
.back{
    text-align:end;
}
.title{
    text-align: right;
}
.bar{
    text-align: left ;
}
</style>

<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <h1>Student Management</h1><br><br><br><h2 class="below-title">⇛ Manage Student<h2><br><br><br>
    <div id="updateMessage" class="popup" style="display: none;"></div>
    <?php
  
    if(isset($_GET['student_ID'])) {
        $student_ID = $_GET['student_ID'];
        
        include('../connection.php');
        if(isset($_GET['message'])) {
            echo "<p>" . htmlspecialchars($_GET['message']) . "</p>";
        }
        if ($connection) {
            $query = "SELECT * FROM student_info WHERE student_ID = '$student_ID'";
    
            $result = mysqli_query($connection, $query);
            
            if(mysqli_num_rows($result) > 0) {
                $student = mysqli_fetch_assoc($result);
                $studentId = $student['student_ID']; 
                
            } else {
                echo "No student found with ID: $student_ID";
                exit();
            }
    
            if(isset($_POST['submit'])) {
                $newStudentId = $_POST['new_id'];
                $newName = $_POST['new_name'];
                $newCourse = $_POST['new_course'];
                $newSection = $_POST['new_section'];
                $student_ID = $_POST['studentID'];
            
                
                $updateEnrollmentsQuery = "UPDATE enrollments SET student_ID = '$newStudentId' WHERE student_ID = '$student_ID'";
                $updateEnrollmentsResult = mysqli_query($connection, $updateEnrollmentsQuery);
            
                if($updateEnrollmentsResult) {
                   
                    $updateQuery = "UPDATE student_info SET student_ID = '$newStudentId', student_NAME = '$newName', student_COURSE = '$newCourse', student_SECTION = '$newSection' WHERE student_ID = '$student_ID'";
                    $updateResult = mysqli_query($connection, $updateQuery);
            
                    if ($updateResult) {
                       
                        $message = "Student information updated successfully.";
                    
                        
                        header("Location: edit_student.php?student_ID=$newStudentId&message=" . urlencode($message));
                        exit();
                    } else {
                        echo "Error updating student information: " . mysqli_error($connection);
                    }
                } else {
                    echo "Error updating enrollments: " . mysqli_error($connection);
                }
            }
            
            $enrolledSubjectsQuery = "SELECT subjects.subject_code as subject_code, subjects.Name as subject_name, facultymembers.Name as faculty_name, enrollments.EnrollmentID 
                                      FROM enrollments 
                                      INNER JOIN subjects ON enrollments.subject_code = subjects.subject_code
                                      INNER JOIN facultymembers ON subjects.FacultyID = facultymembers.FacultyID 
                                      WHERE enrollments.student_ID = '$studentId'";
            $enrolledSubjectsResult = mysqli_query($connection, $enrolledSubjectsQuery);
    
            $enrolledSubjects = [];
            if(mysqli_num_rows($enrolledSubjectsResult) > 0) {
                while($row = mysqli_fetch_assoc($enrolledSubjectsResult)) {
                    $enrolledSubjects[] = $row;
                }
            }
            
            mysqli_close($connection);
        } else {
            echo "Failed to connect to the database.";
            exit();
        }
    } else {
        echo "Student ID not provided.";
        exit();
    }
    ?>
    <div class="box-header">
    <div class="edit-student-form">
    
    <table class="table-header">
        <form action="" method="POST">
            <input type="hidden" name="studentID" value="<?php echo $student['student_ID']; ?>">
            <tr><td class="title"><label for="new_id">Student ID:</label></td>
            <td class="bar"><input type="text" id="new_id" name="new_id" value="<?php echo $student['student_ID']; ?>" required></td>
            <br>
            <td class="title"><label for="new_course">Course:</label></td>
            <td class="bar"><input type="text" id="new_course" name="new_course" value="<?php echo $student['student_COURSE']; ?>" required></td>
            <td class="update"><button type="submit" name="submit"><i class='fa fa-refresh'>Update</i></button><br></td></tr>
            <br>
            <tr><td class="title"><label for="new_name">Name:</label></td>
            <td class="bar"><input type="text" id="new_name" name="new_name" value="<?php echo $student['student_NAME']; ?>" required></td>
            <br>
            
            <td class="title"><label for="new_section">Section:</label></td>
            <td class="bar"><input type="text" id="new_section" name="new_section" value="<?php echo $student['student_SECTION']; ?>" required></td>
</form>
            <td class="back"><form action="student_list.php">
            <br><button type="submit" name="back"><i class='fas fa-arrow-left'>&nbsp;Back</i></button></td></tr>
        </form>
            
        </form>
    </table>

    <form id="deleteForm" action="delete_student.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');">
        <input type="hidden" name="student_ID" value="<?php echo $student['student_ID']; ?>">
        <button type="submit" name="action" value="delete" class="delete-button"><i class="fas fa-trash"></i> Delete Student</button>
    </form>

    </div>
    </div>

    <div class="box-body">
    

<form action="enroll_subject.php" method="post">

        <label for="subject">Add Subject:</label>
        <input type="hidden" name="student_ID" id="student_ID" value="<?php echo $student['id']; ?>">
        <input type="hidden" name="studentID" id="studentID" value="<?php echo $student['student_ID']; ?>">
        <select name="subject" id="subject">
            <?php

            include('../connection.php');
            $subjectQuery = "SELECT subjects.Name AS subject_name, subjects.subject_code AS subject_code 
                             FROM subjects";
            $subjectResult = mysqli_query($connection, $subjectQuery);
            if(mysqli_num_rows($subjectResult) > 0) {
                while($row = mysqli_fetch_assoc($subjectResult)) {
                
                    echo "<option value='" . $row['subject_code'] . "'>" . $row['subject_name'] . "</option>";
                }
            }
            ?>
        </select>
        <input type="submit" value="Enroll Subject">
    </form>


<div class="enrolled_subjects">
    <h3>Enrolled Subjects:</h3>
    <table border="1" class="enrolled_table">
        <tr> 
            <th class="code-column">Subject Code</th>
            <th class="name-column">Subject Name</th>
            <th class="faculty-column">Faculty Name</th>
            <th class="action-column">Action</th>
        </tr>
        <?php $rowCount = 1; ?>
        <?php foreach($enrolledSubjects as $subject): ?>
            <tr>
            <td><?php echo $subject['subject_code']; ?></td>
                <td><?php echo $subject['subject_name']; ?></td>
                <td><?php echo $subject['faculty_name']; ?></td>
                <td>
                    <form action="delete_enrolled_subject.php" method="POST">
                        <input type="hidden" name="studentID" id="student_ID" value="<?php echo $student['student_ID']; ?>">
                        <input type="hidden" name="enrollment_id" value="<?php echo $subject['EnrollmentID']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php $rowCount++; ?>
        <?php endforeach; ?>
    </table>
</div>
</div>


<script>

       function showUpdateMessage(message) {
        var updateMessageDiv = document.getElementById("updateMessage");
        updateMessageDiv.innerHTML = "<h3>" + message + "</h3>";
        updateMessageDiv.style.display = "block";
        setTimeout(function(){ updateMessageDiv.style.display = "none"; }, 3000);
    }
    var message = "<?php echo isset($message) ? $message : ''; ?>";
    if (message !== "") {
        showUpdateMessage(message);
    }
</script>

</body>
</html>
