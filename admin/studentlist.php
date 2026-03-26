<?php
include("../config/database.php");

$result = mysqli_query($conn,"SELECT * FROM students");

while($row = mysqli_fetch_assoc($result)){
    
    echo $row['id_number']." - ".$row['first_name']."<br>";

}
?>