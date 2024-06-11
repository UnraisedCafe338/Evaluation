<?php


include('../connection.php');
include('../session_detector.php');
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_SESSION['student_id'])) {
    $studentId = $_SESSION['student_id'];

    
    $query = "SELECT * FROM student_info WHERE student_ID = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $studentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        
        $student = mysqli_fetch_assoc($result);
    } else {
        
        die("Error: Student information not found. Student ID: $studentId");
    }
} else {
   
    die("Error: Student ID not found in session.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_POST['current_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        $currentPassword = $_POST['current_password'];

   
        if ($newPassword === $confirmPassword && $currentPassword === $student['student_PASS']) {
     
            $query = "UPDATE student_info SET student_PASS = ? WHERE student_ID = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "ss", $newPassword, $studentId);
            if (mysqli_stmt_execute($stmt)) {
                echo "Password updated successfully.";
            } else {
                echo "Error updating password: " . mysqli_error($connection);
            }
        } else {
            echo "Passwords do not match or current password is incorrect.";
        }
    } else {
        echo "New password, confirm password, or current password not provided.";
    }
} else {
    echo "Invalid request method.";
}


mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Password</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    .content {
  margin-left: 100px!important;
    }
    .password-feedback {
        color: green; 
    }
    .password-button {
        background-color: darkblue;
        min-width: 120px; 
        margin-right: 0px;
        margin-left: -10px;
        padding-left: 15px;
        border-radius: 10px;
    }
    #toggleCurrentPassword {
        position: absolute;
        right: 230px;
        top: 225px;
        transform: translateY(-50%);
        cursor: pointer;
        color: black;
    }
    #toggleNewPassword {
        position: absolute;
        right: 230px;
        top: 300px;
        transform: translateY(-50%);
        cursor: pointer;
        color: black;
    }

    #toggleConfirmPassword {
        position: absolute;
        right: 230px;
        top: 375px;
        transform: translateY(-50%);
        cursor: pointer;
        color: black;
    }
    .box-header h3{
        margin-top: -10px;
    }
   
</style>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <h1>Manage Password</h1>
        <br><br><br><br>
        
        <?php if (!empty($student)): ?>
            <div class="box-header">
                <h3>Tip: Type your current password first and make sure its correct.<br> Then proceed to type your new password(minimum of 8 characters length) and then retype it again and update your password.</h3>
            </div>
            <div class="box-body">
                <table class="pass-table">
                    <form action="update_password.php" method="post" id="passwordForm">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($studentId); ?>">
                        <tr>
                            <td><label for="current_password">Current Password:</label></td>
                            <td>
                                <input type="password" id="current_password" name="current_password" required>
                                <br><span id="current_password_feedback" class="password-feedback"></span> 
                                <br><br>
                                <i class="fas fa-eye" id="toggleCurrentPassword"></i>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="new_password">Type New Password:</label></td>
                            <td>
                                <input type="password" id="new_password" name="new_password" minlength="8" required>
                                <br><span id="new_password_feedback" class="password-feedback"></span> 
                                <br><br>
                                <i class="fas fa-eye" id="toggleNewPassword"></i>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="confirm_password">Re-Type New Password:</label></td>
                            <td><input type="password" id="confirm_password" name="confirm_password" maxlength="255" required>
                            <i class="fas fa-eye" id="toggleConfirmPassword"></i></td>
                        </tr>
                        <tr>
                            <td colspan="2"><button type="submit" class="password-button" id="update_button">Update Password</button></td>
                        </tr>
                    </form>
                </table>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <h2><?php echo $_SESSION['error_message']; ?></h2>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Error: Student information not found. Student ID: <?php echo htmlspecialchars($studentId); ?></p>
        <?php endif; ?>
    </div>
</body>

<script>
    const currentPasswordInput = document.getElementById("current_password");
    const newPasswordInput = document.getElementById("new_password");
    const confirmPasswordInput = document.getElementById("confirm_password");

   
    const currentPasswordFeedback = document.getElementById("current_password_feedback");
    const newPasswordFeedback = document.getElementById("new_password_feedback");

  
    currentPasswordInput.addEventListener("blur", function() {
        checkPassword();
    });


    newPasswordInput.addEventListener("blur", function() {
        checkNewPassword();
    });


    confirmPasswordInput.addEventListener("blur", function() {
        checkConfirmPassword();
    });

    function checkPassword() {
        const currentPassword = currentPasswordInput.value;
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "check_password.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = xhr.responseText;
                if (response === "success") {
                    currentPasswordFeedback.textContent = "Password is correct.";
                    currentPasswordFeedback.style.color = "green";
                } else {
                    currentPasswordFeedback.textContent = "Password is incorrect.";
                    currentPasswordFeedback.style.color = "red";
                }
            }
        };
        xhr.send("current_password=" + encodeURIComponent(currentPassword));
    }

    function checkNewPassword() {
        const newPassword = newPasswordInput.value;
        if (newPassword.length >= 8) {
            newPasswordFeedback.textContent = "Password is strong.";
            newPasswordFeedback.style.color = "green";
        } else {
            newPasswordFeedback.textContent = "Password is too short.";
            newPasswordFeedback.style.color = "red";
        }
    }

    function checkConfirmPassword() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const confirmPasswordFeedback = document.getElementById("confirm_password_feedback");

        if (newPassword === confirmPassword) {
            confirmPasswordFeedback.textContent = "Passwords match.";
            confirmPasswordFeedback.style.color = "green";
        } else {
            confirmPasswordFeedback.textContent = "Passwords do not match.";
            confirmPasswordFeedback.style.color = "red";
        }
    }

    const toggleCurrentPassword = document.getElementById("toggleCurrentPassword");
    toggleCurrentPassword.addEventListener("click", function() {
        if (currentPasswordInput.type === "password") {
            currentPasswordInput.type = "text";
            toggleCurrentPassword.classList.remove("fa-eye");
            toggleCurrentPassword.classList.add("fa-eye-slash");
        } else {
            currentPasswordInput.type = "password";
            toggleCurrentPassword.classList.remove("fa-eye-slash");
            toggleCurrentPassword.classList.add("fa-eye");
        }
    });

    const toggleNewPassword = document.getElementById("toggleNewPassword");
    toggleNewPassword.addEventListener("click", function() {
        if (newPasswordInput.type === "password") {
            newPasswordInput.type = "text";
            toggleNewPassword.classList.remove("fa-eye");
            toggleNewPassword.classList.add("fa-eye-slash");
        } else {
            newPasswordInput.type = "password";
            toggleNewPassword.classList.remove("fa-eye-slash");
            toggleNewPassword.classList.add("fa-eye");
        }
    });
    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    toggleConfirmPassword.addEventListener("click", function() {
        if (confirmPasswordInput.type === "password") {
            confirmPasswordInput.type = "text";
            toggleConfirmPassword.classList.remove("fa-eye");
            toggleConfirmPassword.classList.add("fa-eye-slash");
        } else {
            confirmPasswordInput.type = "password";
            toggleConfirmPassword.classList.remove("fa-eye-slash");
            toggleConfirmPassword.classList.add("fa-eye");
        }
    });
</script>
</html>
