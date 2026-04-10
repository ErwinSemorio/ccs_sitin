<?php
session_start();
include __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_number = trim($_POST['id_number'] ?? '');
$points = intval($_POST['points'] ?? 0);
$reason = trim($_POST['reason'] ?? '');

if (!$id_number || $points <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE students SET points = points + ? WHERE id_number = ?");
mysqli_stmt_bind_param($stmt, 'is', $points, $id_number);
$result = mysqli_stmt_execute($stmt);

echo json_encode(['success' => $result]);
?>