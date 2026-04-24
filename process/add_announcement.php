<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include database
require_once __DIR__ . "/../config/database.php";

// Check if database connection exists
if (!isset($conn)) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get and sanitize message
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit();
}

// Escape the message
$message = mysqli_real_escape_string($conn, $message);

// Insert into database (WITHOUT created_by column)
$sql = "INSERT INTO announcements (message, date) VALUES ('$message', NOW())";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Announcement posted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>