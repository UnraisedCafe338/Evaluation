<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Academic Year</title>
  
</head>
<body>

<?php

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];

    include('../connection.php');
    if (!$connection) {
        die("Failed to connect to the database: " . mysqli_connect_error());
    }
    $query = "SELECT * FROM academic_list WHERE id=$id";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
?>
<h1>Edit Academic Year</h1>
<form method="POST" action="update_academic.php">
  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
  <label for="year">Year:</label>
  <input type="text" id="year" name="year" value="<?php echo $row['year']; ?>" required><br>
  <label for="semester">Semester</label>
  <select name="semester" id="semester">
    <option value="1">1st</option>
    <option value="2">2nd</option>
  </select><br>
  <label for="default_select">Default Select:</label>
  <select name="default_select" id="default_select">
    <option value="1" <?php if ($row['default_select'] == 1) echo 'selected'; ?>>Yes</option>
    <option value="0" <?php if ($row['default_select'] == 0) echo 'selected'; ?>>No</option>
  </select><br>
  <label for="status">Status:</label>
  <select name="status" id="status">
    <option value="Active" <?php if ($row['status'] == 'Active') echo 'selected'; ?>>Active</option>
    <option value="Inactive" <?php if ($row['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
  </select><br>
  <button type="submit">Update Academic Year</button>
</form>
<?php
    } else {
        echo "Academic year not found.";
    }

    mysqli_close($connection);
} else {
    echo "Invalid request.";
}
?>

</body>
</html>
