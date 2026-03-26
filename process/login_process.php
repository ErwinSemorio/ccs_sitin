<?php
session_start();
include __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id = $_POST['id_number'];
    $password   = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM students WHERE id_number='$student_id'");

    if (mysqli_num_rows($query) > 0) {

        $row = mysqli_fetch_assoc($query);

        if (password_verify($password, $row['password'])) {

            // ✅ correct session values
            $_SESSION['user_id'] = $row['id_number'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name']  = $row['last_name'];

            header("Location: /ccs_sitin/admin/dashboard.php");
            exit();

        } else {
            echo "Invalid password!";
        }

    } else {
        echo "Student ID not found!";
    }
}
?>