<?php
include __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$sit_id = intval($_POST['sit_id'] ?? 0);
if ($sit_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid record ID']);
    exit;
}

// Get the sit-in record
$stmt = mysqli_prepare($conn, "SELECT id_number, time_in, time_out FROM sitin_records WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $sit_id);
mysqli_stmt_execute($stmt);
$record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($record && $record['time_in']) {
    // Calculate hours
    $timeIn = strtotime($record['time_in']);
    $timeOut = $record['time_out'] ? strtotime($record['time_out']) : time();
    $hours = round(($timeOut - $timeIn) / 3600, 2);
    
    // Update sit-in record
    mysqli_query($conn, "UPDATE sitin_records SET status='Done', time_out=NOW() WHERE id=$sit_id");
    
    // Update student's total_hours for weighted scoring
    if ($hours > 0) {
        mysqli_query($conn, "UPDATE students SET total_hours = total_hours + $hours WHERE id_number='{$record['id_number']}'");
    }
}

echo json_encode(['success' => true]);
?>