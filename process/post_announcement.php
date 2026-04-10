<?php
session_start();
include __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$message = trim($_POST['message'] ?? '');
if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit;
}

// Save to database using prepared statement
$stmt = mysqli_prepare($conn, "INSERT INTO announcements (message, created_by) VALUES (?, ?)");
$admin_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
mysqli_stmt_bind_param($stmt, 'ss', $message, $admin_name);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    echo json_encode([
        'success' => true, 
        'id' => mysqli_insert_id($conn),
        'date' => date('Y-M-d'),
        'message' => htmlspecialchars($message)
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>