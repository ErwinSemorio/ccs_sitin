<?php
session_start();
include __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$id_number = trim($_POST['id_number']);
$purpose   = trim($_POST['purpose']);
$lab       = trim($_POST['lab']);

$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id_number='$id_number'"));
if (!$student) { echo json_encode(['success'=>false,'message'=>'Student not found']); exit; }

$stmt = mysqli_prepare($conn, "INSERT INTO sitin_records (id_number, name, purpose, lab, status, date, time_in) VALUES (?,?,?,?,'Active',CURDATE(),NOW())");
$name = $student['first_name'] . ' ' . $student['last_name'];
mysqli_stmt_bind_param($stmt, 'ssss', $id_number, $name, $purpose, $lab);
mysqli_stmt_execute($stmt);
$sit_id = mysqli_insert_id($conn);

echo json_encode([
  'success' => true,
  'record'  => [
    'sit_id'    => $sit_id,
    'id_number' => $id_number,
    'name'      => $name,
    'purpose'   => $purpose,
    'lab'       => $lab,
    'session'   => 30,
    'status'    => 'Active',
    'date'      => date('m/d/Y'),
    'timeIn'    => date('h:i:s A'),
    'timeOut'   => null
  ]
]);