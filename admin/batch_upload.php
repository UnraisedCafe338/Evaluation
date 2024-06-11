<?php

include('../connection.php');

$uploaded_student_ids = [];

if (isset($_POST['upload'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            fgetcsv($handle, 1000, ','); // Skip the header row

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Prepare insert statement for student_info
                $stmt = $pdo->prepare("INSERT INTO student_info (student_ID, student_NAME, student_COURSE, student_SECTION, student_PASS) 
                                      VALUES (:student_ID, :student_NAME, 'bsis', 'prova', :student_PASS)");

                // Prepare insert statement for enrollments
                $enrollStmt = $pdo->prepare("INSERT INTO enrollments (student_ID, subject_code) VALUES (:student_ID, :subject_code)");

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $student_ID = $data[0];
                    $student_NAME = $data[1] . ' ' . $data[2] . ' ' . $data[3];
                    $student_PASS = generateRandomPassword();

                    // Check if student already exists
                    $check_query = "SELECT student_ID FROM student_info WHERE student_ID = :student_ID";
                    $check_stmt = $pdo->prepare($check_query);
                    $check_stmt->bindParam(':student_ID', $student_ID);
                    $check_stmt->execute();

                    if ($check_stmt->rowCount() > 0) {
                        $uploaded_student_ids[] = $student_ID;
                        continue;
                    }

                    // Insert into student_info
                    $stmt->bindParam(':student_ID', $student_ID);
                    $stmt->bindParam(':student_NAME', $student_NAME);
                    $stmt->bindParam(':student_PASS', $student_PASS);

                    if ($stmt->execute()) {
                        $uploaded_student_ids[] = $student_ID;

                        // Insert subjects into enrollments
                        for ($i = 4; $i <= 10; $i++) {
                            if (!empty($data[$i])) {
                                $enrollStmt->bindParam(':student_ID', $student_ID);
                                $enrollStmt->bindParam(':subject_code', $data[$i]);
                                $enrollStmt->execute();
                            }
                        }
                    }
                }

                echo "Batch upload successful.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            fclose($handle);
        } else {
            echo "Error opening the file.";
        }
    } else {
        echo "Error uploading the file.";
    }
}

if (!empty($uploaded_student_ids)) {
    echo "<h2>Uploaded Students</h2>";
    echo "<table border='1'>";
    echo "<thead><tr><th>Student ID</th><th>Name</th><th>Course</th><th>Section</th><th>Password</th></tr></thead>";
    echo "<tbody>";

    foreach ($uploaded_student_ids as $student_id) {
        $query = "SELECT * FROM student_info WHERE student_ID = :student_ID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':student_ID', $student_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<tr>";
            echo "<td>{$row['student_ID']}</td>";
            echo "<td>{$row['student_NAME']}</td>";
            echo "<td>{$row['student_COURSE']}</td>";
            echo "<td>{$row['student_SECTION']}</td>";
            echo "<td>{$row['student_PASS']}</td>";
            echo "</tr>";
        }
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "No students uploaded.";
}

function generateRandomPassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    $maxIndex = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[mt_rand(0, $maxIndex)];
    }
    return $password;
}
?>

<button onclick="window.location.href='student_list.php';">Go to Student List</button>
