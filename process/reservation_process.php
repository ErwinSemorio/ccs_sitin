<?php
session_start();
// Ensure only admins can process this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Switch to POST to match your Admin Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];
    $status = '';

    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    }

    if ($status) {
        $stmt = mysqli_prepare($conn, "UPDATE reservations SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        mysqli_stmt_execute($stmt);
        
        $msg = strtolower($status);
        header("Location: /ccs_sitin/admin/reservation.php?msg=" . $msg);
        exit();
    }
}

// Fallback redirect if something goes wrong
header("Location: /ccs_sitin/admin/reservation.php");
exit();