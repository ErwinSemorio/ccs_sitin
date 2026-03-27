<?php
session_start();
include __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_number = $_POST['id_number'];
    $password   = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM students WHERE id_number='$id_number'");
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        if (password_verify($password, $row['password'])) {

            $_SESSION['user_id'] = $row['id_number'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['role'] = $row['role']; // Store role

            // Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: /ccs_sitin/admin/dashboard.php");
            } else {
                header("Location: /ccs_sitin/student/dashboard.php");
            }
            exit();

        } else {
            header("Location: /ccs_sitin/login.php?error=invalid_password");
            exit();
        }
    } else {
        header("Location: /ccs_sitin/login.php?error=not_found");
        exit();
    }
}
?>