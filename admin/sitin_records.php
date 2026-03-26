<?php
include("../config/database.php");

$sql = "SELECT * FROM sitin_records
JOIN students
ON sitin_records.student_id = students.id";

$result = mysqli_query($conn,$sql);

while($row = mysqli_fetch_assoc($result)){

echo $row['first_name']." | ";
echo $row['time_in']." | ";
echo $row['time_out']."<br>";

}
?>