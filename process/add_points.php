<?php
session_start();
include __DIR__ . '/../config/database.php';
include __DIR__ . '/../config/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ccs_sitin/login.php");
    exit;
}

$id_number = trim($_POST['id_number'] ?? '');
$points    = intval($_POST['points'] ?? 0);
$reason    = trim($_POST['reason'] ?? '');

if (!$id_number || $points <= 0) {
    header("Location: /ccs_sitin/admin/add_reward.php?error=Invalid+input");
    exit;
}

// Update points
$stmt = mysqli_prepare($conn, "UPDATE students SET points = points + ? WHERE id_number = ?");
mysqli_stmt_bind_param($stmt, 'is', $points, $id_number);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    // Recalculate weighted score
    updateStudentScore($conn, $id_number);
    header("Location: /ccs_sitin/admin/add_reward.php?success=Points+added+successfully");
} else {
    header("Location: /ccs_sitin/admin/add_reward.php?error=Failed+to+add+points");
}
exit;