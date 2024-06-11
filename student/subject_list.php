<?php

include('../connection.php');
include('../session_detector.php');
if ($connection) {
    if(isset($_GET['studentId'])) {
        $student_ID = $_GET['studentId'];
        
        $query = "SELECT * FROM student_info WHERE student_ID = '$student_ID'";
    
        $result = mysqli_query($connection, $query);
            
        if(mysqli_num_rows($result) > 0) {
            $student = mysqli_fetch_assoc($result);
            $studentId = $student['id']; // Assuming 'id' is the foreign key in the student_info table
        } else {
            echo "No student found with ID: $student_ID";
            exit();
        }
    
        // Query enrolled subjects before closing the connection
        $enrolledSubjectsQuery = "SELECT subjects.subject_code as subject_code, subjects.Name as subject_name, facultymembers.Name as faculty_name 
                          FROM enrollments 
                          INNER JOIN subjects ON enrollments.subject_code = subjects.subject_code 
                          INNER JOIN facultymembers ON subjects.FacultyID = facultymembers.FacultyID 
                          WHERE enrollments.student_id = '$student_ID'";

        $enrolledSubjectsResult = mysqli_query($connection, $enrolledSubjectsQuery);

        $enrolledSubjects = [];
        if(mysqli_num_rows($enrolledSubjectsResult) > 0) {
            while($row = mysqli_fetch_assoc($enrolledSubjectsResult)) {
                $enrolledSubjects[] = $row;
            }
        }
    } else {
        echo "Student ID not provided.";
        
        exit();
    }
} else {
    echo "Failed to connect to the database.";
    exit();
}

// Close the database connection
mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  
  <meta charset="UTF-8" />
  <title>Dashboard Design</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>


<style>
  .subject-button {
    background-color: darkblue;
    min-width: 120px; 
    margin-right: 0px;
    margin-left: -10px;
    padding-left: 15px;
    border-radius: 10px;
}
.background-box{
    border-style: 10px;
    height: 80%;
    width: 103%;
    border-radius: 10px;
    text-align: center;
    padding: 20px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    
}

</style>

<body>
<?php include 'sidebar.php'; ?>
  <div class="content">
  <br>
  <br>
    <h1>SUBJECTS MANAGEMENT</h1><br><br>
    <div class="box-header"><h2>Enrolled Subjects:</h2></div>
    <div class="box-body">
    
    <table border="1">
        <tr>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Faculty Name</th>
        </tr>
        <?php foreach($enrolledSubjects as $subject): ?>
            <tr>
                <td><?php echo $subject['subject_code']; ?></td>
                <td><?php echo $subject['subject_name']; ?></td>
                <td><?php echo $subject['faculty_name']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
   
</div>
        

  </div>
  
</body>
</html>
