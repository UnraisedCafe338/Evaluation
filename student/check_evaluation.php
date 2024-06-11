<?php
if (isset($_GET["studentId"])) {

    $studentId = $_GET["studentId"];

    $query = "SELECT evaluated FROM student_list WHERE student_ID = $student_ID";
    $result = mysqli_query($connection, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $evaluated = $row["evaluated"];
        if ($evaluated == 1) {
            echo "evaluated";
        } else {

            echo "not_evaluated";
        }
    } else {
        echo "error";
    }
} else {
   
    echo "not_found";
}
?>
