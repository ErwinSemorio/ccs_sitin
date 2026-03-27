<?php
include __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_number = $_POST['id_number'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $course = $_POST['course'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // HASH PASSWORD
    $address = $_POST['address'];
    $role = 'student'; // All new registrations are students

    // Check if email exists
    $check = mysqli_query($conn, "SELECT * FROM students WHERE email='$email' OR id_number='$id_number'");
    if (mysqli_num_rows($check) > 0) {
        echo "Email or ID number already registered!";
        exit();
    }

    $sql = "INSERT INTO students 
        (id_number, last_name, first_name, middle_name, course, email, password, address, role)
        VALUES 
        ('$id_number','$last_name','$first_name','$middle_name','$course','$email','$password','$address','$role')";

    if (mysqli_query($conn, $sql)) {
        echo "Registered successfully! <a href='/ccs_sitin/login.php'>Login here</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>