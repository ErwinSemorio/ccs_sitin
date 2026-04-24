<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include __DIR__ . "/../config/database.php";

$id = $_SESSION['user_id'];
$uploadDir = __DIR__ . "/../uploads/profiles/";

// Create upload directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check if file was uploaded
if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$file = $_FILES['profile_photo'];
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Validate file type
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed']);
    exit();
}

// Validate file size
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB']);
    exit();
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFilename = $id . '_' . time() . '.' . $extension;
$uploadPath = $uploadDir . $newFilename;

// Delete old photo if exists (but not default.jpg)
$oldResult = mysqli_query($conn, "SELECT profile_photo FROM students WHERE id_number = '$id'");
if ($oldResult && $oldRow = mysqli_fetch_assoc($oldResult)) {
    $oldPhoto = $oldRow['profile_photo'];
    if ($oldPhoto && $oldPhoto !== 'default.jpg' && file_exists($uploadDir . $oldPhoto)) {
        unlink($uploadDir . $oldPhoto);
    }
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Update database
    $stmt = mysqli_prepare($conn, "UPDATE students SET profile_photo = ? WHERE id_number = ?");
    mysqli_stmt_bind_param($stmt, "ss", $newFilename, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Profile photo updated successfully',
            'photo' => $newFilename
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}
?>