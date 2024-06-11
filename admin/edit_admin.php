<?php
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['edit_username'])) {
      
            $newUsername = $_POST['username'];
            $stmt = $pdo->prepare("UPDATE admin_list SET admin_name = :username");
            $stmt->bindParam(':username', $newUsername);
            $stmt->execute();
        }

        if (isset($_POST['edit_password'])) {

            $newPassword = $_POST['password'];
            $stmt = $pdo->prepare("UPDATE admin_list SET admin_pass = :password");
            $stmt->bindParam(':password', $newPassword);
            $stmt->execute();
        }

        header("Location: settings.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
