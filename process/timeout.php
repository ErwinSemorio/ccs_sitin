<?php

include("../config/database.php");

$record_id = $_GET['id'];

$sql = "UPDATE sitin_records 
SET time_out = NOW()
WHERE id='$record_id'";

mysqli_query($conn,$sql);

header("Location: ../student/dashboard.php");

?>