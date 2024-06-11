<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Design</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .settings-button {
      background-color: darkblue;
      min-width: 120px; 
      margin-right: 0px;
      margin-left: -10px;
      padding-left: 15px;
      border-radius: 10px;
    }
    .admin-box {
      background-color: #f0f0f0;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 300px;
      margin: 50px auto;
      text-align: center;
    }
    .admin-form input {
      margin-bottom: 10px;
    }
  </style>
</head>

<body>
  <?php include 'sidebar.php'; ?>
  <div class="content">
    <h1>SETTINGS</h1>
    <div class="admin-box">
      <?php
      include('../connection.php');

      // Establish connection
      $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Fetch admin credentials
      $stmt = $pdo->query("SELECT * FROM admin_list");
      $admin = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($admin) {
        echo "<h2>Admin Credentials</h2>";
        echo "<h3>Username: {$admin['admin_name']}</h3>";

        // Edit username form
        echo "<form class='admin-form' method='post' action='edit_admin.php'>";
        echo "<input type='text' name='username' placeholder='New Username' required><br>";
        echo "<button type='submit' name='edit_username'>Edit Username</button>";
        echo "</form>";

        echo "<h3>Password: {$admin['admin_pass']}</h3>"; // For security reasons, don't show the password

        // Edit password form
        echo "<form class='admin-form' method='post' action='edit_admin.php'>";
        echo "<input type='text' name='password' placeholder='New Password' required><br>";
        echo "<button type='submit' name='edit_password'>Edit Password</button>";
        echo "</form>";
      } else {
        echo "<p>No admin found.</p>";
      }
      ?>
    </div>
  </div>
</body>
</html>
