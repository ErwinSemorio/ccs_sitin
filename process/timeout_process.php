<?php
session_start();
include __DIR__ . '/../config/database.php';

$sit_id = intval($_POST['sit_id'] ?? 0);
if ($sit_id <= 0) {
    header("Location: /ccs_sitin/admin/sitin.php?error=Invalid+record");
    exit;
}

// Get the sit-in record
$stmt = mysqli_prepare($conn, "SELECT id_number, time_in FROM sitin_records WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $sit_id);
mysqli_stmt_execute($stmt);
$record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$record) {
    header("Location: /ccs_sitin/admin/sitin.php?error=Record+not+found");
    exit;
}

// Calculate hours spent
$hours = round((time() - strtotime($record['time_in'])) / 3600, 2);

// Update sit-in record to Done
$stmt2 = mysqli_prepare($conn, "UPDATE sitin_records SET status='Done', time_out=NOW() WHERE id=?");
mysqli_stmt_bind_param($stmt2, 'i', $sit_id);
mysqli_stmt_execute($stmt2);

// Update student's total_hours
if ($hours > 0) {
    $stmt3 = mysqli_prepare($conn, "UPDATE students SET total_hours = total_hours + ? WHERE id_number = ?");
    mysqli_stmt_bind_param($stmt3, 'ds', $hours, $record['id_number']);
    mysqli_stmt_execute($stmt3);
}

header("Location: /ccs_sitin/admin/sitin.php?success=Student+timed+out+successfully");
exit;
?>