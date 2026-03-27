<?php
include __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$id_number  = trim($_POST['id_number']);
$first_name = trim($_POST['first_name']);
$last_name  = trim($_POST['last_name']);
$course     = trim($_POST['course']);
$email      = trim($_POST['email']);
$address    = trim($_POST['address']);

$stmt = mysqli_prepare($conn, "UPDATE students SET first_name=?, last_name=?, course=?, email=?, address=? WHERE id_number=?");
mysqli_stmt_bind_param($stmt, 'ssssss', $first_name, $last_name, $course, $email, $address, $id_number);
$result = mysqli_stmt_execute($stmt);
echo json_encode(['success' => $result]);