<?php
$conn = mysqli_connect("localhost", "root", "", "ccs_sitin_db"); // updated DB name

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>