<?php
// kukunin yung admin details sa db
include('../connection.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['username'])) {
        $username = $_GET['username'];
        
        $stmt = $pdo->prepare("SELECT * FROM admin_list WHERE admin_name = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            echo json_encode($admin);
        } else {
            echo json_encode(["error" => "Admin not found"]);
        }
    } else {
        echo json_encode(["error" => "Username not provided"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
