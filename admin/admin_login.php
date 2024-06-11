<!DOCTYPE html>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<?php
session_start();
include('../connection.php');
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin_list WHERE admin_name = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        if ($password === $user['admin_pass']) {
            $_SESSION['username'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $errorMessage = "Invalid username or password.";
        }
    } else {
        $errorMessage = "Invalid username or password.";
    }
}
?>





<html>
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
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.5); 
    }

    .overlay-heading {
      font-size: 3rem;
      color: white;
      margin-top: 40px;
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

  </style>
<body>
<div class="background"></div>
<div class="overlay-box">
    <div class="evaluation">
        <img src="exact logo.png" alt="Logo" class="logo">
        <div class="overlay-box2">
             <h2 class="overlay-heading">Faculty Evaluation</h2>
             <h1>Admin Login</h1>
        </div>
        <p></p>
        <div class="form-container">
        <form id="loginForm" method="post" action="">

        <label for="username"></label><br>
        <i class="fas fa-user-cog"></i> Username:<input type="text" id="username" name="username" placeholder="Username" maxlength="30" required><br>
        <label for="password"></label>
    
    <div class="password-input-container">
    <i class="fas fa-lock"></i> Password:&nbsp;&nbsp;<input type="password" id="password" name="password" placeholder="Password" maxlength="30" required>
    <i class="fas fa-eye" id="togglePassword"></i>
</div>
        <button type="submit" id="submitForm">Log In</button>
        </form>
        <p id="errorMessage" style="color: red;"><?php echo isset($errorMessage) ? $errorMessage : ""; ?></p>
        <p id="errorMessage" style="color: red;"></p>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    var usernameInput = document.getElementById("username");
    var passwordInput = document.getElementById("password");
    var submitFormButton = document.getElementById("submitForm");
    var errorMessage = document.getElementById("errorMessage");

    submitFormButton.disabled = true;

    usernameInput.addEventListener("input", function(event) {
        toggleSubmitButton();
    });

    passwordInput.addEventListener("input", function(event) {
        toggleSubmitButton();
    });

    function toggleSubmitButton() {
        if (usernameInput.value.trim() !== "" && passwordInput.value.trim() !== "") {
            submitFormButton.disabled = false;
        } else {
            submitFormButton.disabled = true;
        }
    }
    submitFormButton.addEventListener("click", function(event) {
        event.preventDefault(); 
  
        if (usernameInput.value.trim() === "" || passwordInput.value.trim() === "") {
            errorMessage.innerText = "Please enter both username and password.";
            return;
        }
        document.getElementById("loginForm").submit();
    });
});
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
