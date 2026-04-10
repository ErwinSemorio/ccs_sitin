<?php
session_start();
include __DIR__ . '/../../config/database.php';
include __DIR__ . '/../../config/functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_number = trim($_POST['id_number'] ?? '');
$points    = floatval($_POST['points'] ?? 0);
$hours     = floatval($_POST['hours'] ?? 0);
$tasks     = intval($_POST['tasks'] ?? 0);

if (!$id_number) {
    echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
    exit;
}

// Update student record
$stmt = mysqli_prepare($conn, "UPDATE students SET 
    points = points + ?, 
    total_hours = total_hours + ?, 
    tasks_completed = tasks_completed + ? 
    WHERE id_number = ?");
mysqli_stmt_bind_param($stmt, 'ddis', $points, $hours, $tasks, $id_number);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    // Recalculate weighted score
    $newScore = updateStudentScore($conn, $id_number);
    echo json_encode(['success' => true, 'new_score' => $newScore]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>