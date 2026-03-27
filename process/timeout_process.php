<?php
include __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$sit_id = intval($_POST['sit_id']);
mysqli_query($conn, "UPDATE sitin_records SET status='Done', time_out=NOW() WHERE id=$sit_id");
echo json_encode(['success' => true]);