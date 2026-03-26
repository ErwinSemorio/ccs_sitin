<?php
include __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_number = $_POST['id_number'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $course = $_POST['course'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 🔥 HASHED
    $address = $_POST['address'];

    // Check if email exists
    $check = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        echo "Email already registered!";
    } else {

        $sql = "INSERT INTO students 
        (id_number, last_name, first_name, middle_name, course, email, password, address)
        VALUES 
        ('$id_number','$last_name','$first_name','$middle_name','$course','$email','$password','$address')";

        if (mysqli_query($conn, $sql)) {
            echo "Registered successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>