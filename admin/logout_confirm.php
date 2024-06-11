<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Logout</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>


<style>
  .logout-button {
    background-color: darkblue;
    min-width: 120px; 
    margin-right: 0px; 
    margin-left: -10px;
    padding-left: 15px;
    border-radius: 10px;
}
.yes-button {
    background-color: blue;
    color: black;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
  }
  .no-button {

    color: black;
    padding: 10px 20px;
    border-color: black;
    border-style: line;
    border-width: 100px;
    cursor: pointer;
    border-radius: 5px;
  }
</style>

<body>
<?php include 'sidebar.php'; ?>
  <div class="content">
    <h1>Are you sure you want to log out?</h1>
    <button class="yes-button" onclick="location.href='admin_login.php'">Yes</button>
    <button class="no-button" onclick="location.href='evaluation_dashboard.php'">No</button>
  </div>
</body>
</html>
