<?php
session_start();
include __DIR__ . '/../config/database.php';

$id_number = trim($_POST['id_number']);
$purpose   = trim($_POST['purpose']);
$lab       = trim($_POST['lab']);

$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id_number='$id_number'"));
if (!$student) {
    header("Location: /ccs_sitin/admin/sitin.php?error=Student+not+found");
    exit;
}

// Check if student already has an active sit-in
$existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM sitin_records WHERE id_number='$id_number' AND status='Active'"));
if ($existing) {
    header("Location: /ccs_sitin/admin/sitin.php?error=Student+already+has+an+active+sit-in");
    exit;
}

$name = $student['first_name'] . ' ' . $student['last_name'];
$stmt = mysqli_prepare($conn, "INSERT INTO sitin_records (id_number, name, purpose, lab, status, date, time_in) VALUES (?,?,?,?,'Active',CURDATE(),NOW())");
mysqli_stmt_bind_param($stmt, 'ssss', $id_number, $name, $purpose, $lab);

if (mysqli_stmt_execute($stmt)) {
    header("Location: /ccs_sitin/admin/sitin.php?success=Sit-in+registered+successfully");
} else {
    header("Location: /ccs_sitin/admin/sitin.php?error=Failed+to+register+sit-in");
}
exit;