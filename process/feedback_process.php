<?php
session_start();
include __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// 🔒 Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$record_id = intval($_POST['record_id'] ?? 0);
$rating    = $_POST['rating'] ?? null;
$feedback  = trim($_POST['feedback'] ?? '');

// Validate
if ($record_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid record ID']);
    exit;
}
if ($rating !== null && (!is_numeric($rating) || $rating < 1 || $rating > 5)) {
    echo json_encode(['success' => false, 'message' => 'Rating must be 1-5']);
    exit;
}

// 🔐 Prepared statement to prevent SQLi
$stmt = mysqli_prepare($conn, "UPDATE sitin_records SET feedback = ?, rating = ? WHERE id = ? AND id_number = ?");
$student_id = $_SESSION['user_id'];
mysqli_stmt_bind_param($stmt, 'sisi', $feedback, $rating, $record_id, $student_id);
$result = mysqli_stmt_execute($stmt);

echo json_encode(['success' => $result]);
?>