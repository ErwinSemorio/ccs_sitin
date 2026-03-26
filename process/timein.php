<?php
include("../config/database.php");

$student_id = $_GET['id'];

$sql = "INSERT INTO sitin_records(student_id,time_in)
VALUES('$student_id',NOW())";

mysqli_query($conn,$sql);

header("Location: ../student/dashboard.php");
?>