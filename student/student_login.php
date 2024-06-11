<?php

session_start();

include('../connection.php');
$errorMessage = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}

function studentExists($pdo, $studentId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM student_info WHERE student_ID = :studentId");
    $stmt->bindParam(':studentId', $studentId);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count > 0;
}

function hasActiveSession($pdo, $studentId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM active_sessions WHERE user_id = :studentId");
    $stmt->bindParam(':studentId', $studentId);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count > 0;
}

function createSessionRecord($pdo, $studentId, $sessionId, $course) {
    $stmt = $pdo->prepare("INSERT INTO active_sessions (user_id, session_id, course) VALUES (:studentId, :sessionId, :course)");
    $stmt->bindParam(':studentId', $studentId);
    $stmt->bindParam(':sessionId', $sessionId);
    $stmt->bindParam(':course', $course);
    $stmt->execute();
}

if (isset($_GET['studentId']) && isset($_GET['password'])) {
    $studentId = $_GET['studentId'];
    $password = $_GET['password'];

    if (studentExists($pdo, $studentId)) {
        if (hasActiveSession($pdo, $studentId)) {
            $errorMessage = "This student is already logged in.";
        } else {
            $stmt = $pdo->prepare("SELECT student_PASS FROM student_info WHERE student_ID = :studentId");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $storedPassword = $row['student_PASS'];
                if ($password === $storedPassword) {
                    $_SESSION['student_id'] = $studentId;
                    $sessionId = bin2hex(random_bytes(16)); // Use PHP session ID as unique identifier

                    $stmtCourse = $pdo->prepare("SELECT student_COURSE FROM student_info WHERE student_ID = :studentId");
                    $stmtCourse->bindParam(':studentId', $studentId);
                    $stmtCourse->execute();
                    $rowCourse = $stmtCourse->fetch(PDO::FETCH_ASSOC);
                    $course = ($rowCourse) ? $rowCourse['student_COURSE'] : null;

                    createSessionRecord($pdo, $studentId, $sessionId, $course);

                    header("Location: student_dashboard.php?studentId=$studentId");
                    exit();
                } else {
                    $errorMessage = "Invalid Student ID or Password.";
                }
            } else {
                $errorMessage = "No user found with the provided student ID.";
            }
        }
    } else {
        $errorMessage = "Invalid Student ID or Password.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=0.5">
<style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            position: relative;
            font-family: Arial, sans-serif;
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

        .overlay-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9));
            padding: 20px;
            color: white;
            border-radius: 10px;
            text-align: center;
            z-index: 1;
            width: 500px;
            max-width: 50%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5); /* Add a subtle box shadow */
        }

        .overlay-heading {
            font-size: 3rem;
            color: white;
            margin-top: 40px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .form-container {
            margin-top: 40px;
        }

        .form-container label,
        .form-container input {
            margin-bottom: 20px;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #002afc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #001f80;
        }

        .logo {
            position: absolute;
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 150px;
            z-index: 2;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .disabled-button {
            box-shadow: none;
        }
        .secretbutton .secret-button{
            margin-right:300%;
            z-index: 1;
            color: rgba(0, 0, 0, 0.0);
        }
        .secretbutton .secret-button:hover{
            cursor: default;
            
        }
        .password-input-container {
    position: relative;
}
        #togglePassword {
    position: absolute;
    right: 120px;
    top: 27%;
    transform: translateY(-50%);
    cursor: pointer;
    color: black;
}
.background a{
    position: absolute;
    bottom: 30%;
    left:13%;
    opacity: 0;
    cursor: default;
}
    </style>
</head>
<body>
<div class="background">
<a href="../admin/admin_login.php" class="admin-page">hello</a>
</div>
<div class="overlay-box">
    <div class="evaluation">
        <img src="exact logo.png" alt="Logo" class="logo">
        <div class="overlay-box2">
            <h2 class="overlay-heading">Faculty Evaluation</h2>
            <h1>Student Login</a>  </h1>

        </div>
        <p>Please input your student ID and password to proceed to evaluation.</p>
        <div class="form-container">
            <form method="get" action="">
                <label for="studentId"><i class="fas fa-user-cog"></i> Student ID:</label>
                <input type="text" id="studentId" name="studentId" placeholder="Enter your student ID" maxlength="11" required><br>
                
                <div class="password-input-container">
                &nbsp; &nbsp;&nbsp;<i class="fas fa-lock"></i> &nbsp;Password:&nbsp;<input type="password" id="password" name="password" placeholder="Password" maxlength="30" required>
                &nbsp;  <i class="fas fa-eye" id="togglePassword"></i>
</div>
                <button type="submit" id="submitForm">Log In</button>
                
                <?php if (!empty($errorMessage)): ?>
                <p style="color: red;"><?php echo $errorMessage; ?></p>
                <?php endif; ?>
            </form>
            <p id="errorMessage" style="color: red;"></p>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var studentIdInput = document.getElementById("studentId");
        var studentCourseInput = document.getElementById("course"); 
        var submitFormButton = document.getElementById("submitForm");
        var errorMessage = document.getElementById("errorMessage");

       
        submitFormButton.disabled = true;
        submitFormButton.classList.add("disabled-button");

        studentIdInput.addEventListener("blur", function(event) {
    var studentId = studentIdInput.value.trim();
    var format = /^([0-9]{2}[0-9]{2})-(\d{6})$/; 

    if (format.test(studentId)) {
        errorMessage.innerText = ""; 
        submitFormButton.disabled = false; 
        submitFormButton.classList.remove("disabled-button"); 
    
    } else {
        errorMessage.innerText = "Invalid student ID format. Please enter in the format YYYY-NNNNNN";
        submitFormButton.disabled = true; 
        submitFormButton.classList.add("disabled-button"); 
        studentCourseInput.value = "";
    }
});
        studentIdInput.addEventListener("input", function(event) {
            if (studentIdInput.value.trim() === "") {
                studentCourseInput.value = "";
                errorMessage.innerText = ""; 
                submitFormButton.disabled = true; 
                submitFormButton.classList.add("disabled-button");
            }
        });
    });

    function fetchStudentCourse(studentId) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText); 
                var studentCourse = response.student_COURSE; 
                
                document.getElementById("course").value = studentCourse; 
            } else {
                console.error('Error fetching student course:', xhr.status);
            }
        }
    };
    xhr.open('GET', 'fetch_student_details.php?studentId=' + encodeURIComponent(studentId), true);
    xhr.send();
}
const passwordInput = document.getElementById("password");
const toggleButton = document.getElementById("togglePassword");
toggleButton.addEventListener("click", function() {
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleButton.classList.remove("fa-eye");
        toggleButton.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleButton.classList.remove("fa-eye-slash");
        toggleButton.classList.add("fa-eye");
    }
});

</script>
</body>
</html>
