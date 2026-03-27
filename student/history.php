<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}

// ✅ FIXED: Go up ONE level from /student/ to /ccs_sitin/, then into /config/
include __DIR__ . "/../config/database.php";

// ✅ Check if database connection succeeded
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$id = $_SESSION['user_id'];
// Fetch student history
$history = mysqli_query($conn, "SELECT * FROM sit_history WHERE id_number='$id' ORDER BY date DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>History | CCS Student</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-4">
<h2>History Information</h2>
<table class="table table-striped">
<thead class="table-primary">
<tr>
<th>ID Number</th>
<th>Name</th>
<th>Sit Purpose</th>
<th>Laboratory</th>
<th>Login</th>
<th>Logout</th>
<th>Date</th>
</tr>
</thead>
<tbody>
<?php if($history && mysqli_num_rows($history) > 0): ?>
<?php while($row = mysqli_fetch_assoc($history)): ?>
<tr>
<td><?= htmlspecialchars($row['id_number']) ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['sit_purpose']) ?></td>
<td><?= htmlspecialchars($row['laboratory']) ?></td>
<td><?= htmlspecialchars($row['login_time']) ?></td>
<td><?= htmlspecialchars($row['logout_time']) ?></td>
<td><?= htmlspecialchars($row['date']) ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7" class="text-center">No data available</td></tr>
<?php endif; ?>
</tbody>
</table>
<a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>