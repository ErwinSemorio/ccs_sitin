<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Fetch all sit-in records (with logout time)
$sql = "SELECT s.*, sr.time_in, sr.time_out, sr.purpose, sr.laboratory, sr.session, sr.date
        FROM sitin_records sr
        JOIN students s ON sr.id_number = s.id_number
        ORDER BY sr.date DESC, sr.time_in DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Sit-in Records | CCS Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
.header { background-color: #0d47a1; color: white; padding: 15px 0; margin-bottom: 30px; }
.nav-link { color: white !important; margin: 0 10px; }
.card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.card-header { background-color: #0d47a1; color: white; font-weight: bold; }
</style>
</head>
<body>
<!-- Header -->
<div class="header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">CCS Admin Dashboard</h4>
            <nav>
                <a href="/ccs_sitin/admin/dashboard.php" class="nav-link d-inline">Home</a>
                <a href="/ccs_sitin/admin/students.php" class="nav-link d-inline">Students</a>
                <a href="/ccs_sitin/admin/current_sitin.php" class="nav-link d-inline">Current Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link d-inline">Sit-in Records</a>
                <a href="/ccs_sitin/logout.php" class="btn btn-warning btn-sm ms-2">Log out</a>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-clock-history"></i> Sit-in Records (History)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="sitinRecordsTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Purpose</th>
                            <th>Lab</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Duration</th>
                            <th>Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <?php
                                // Calculate duration
                                $duration = 'N/A';
                                if($row['time_in'] && $row['time_out']) {
                                    $timeIn = strtotime($row['time_in']);
                                    $timeOut = strtotime($row['time_out']);
                                    $diff = $timeOut - $timeIn;
                                    $hours = floor($diff / 3600);
                                    $minutes = floor(($diff % 3600) / 60);
                                    $duration = $hours . 'h ' . $minutes . 'm';
                                }
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_number']) ?></td>
                                    <td><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?></td>
                                    <td><?= htmlspecialchars($row['course']) ?></td>
                                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                                    <td><?= htmlspecialchars($row['laboratory']) ?></td>
                                    <td><?= htmlspecialchars($row['date']) ?></td>
                                    <td><?= htmlspecialchars($row['time_in']) ?></td>
                                    <td><?= htmlspecialchars($row['time_out']) ?></td>
                                    <td><?= htmlspecialchars($duration) ?></td>
                                    <td><?= htmlspecialchars($row['session']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="10" class="text-center">No sit-in records available</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <a href="/ccs_sitin/admin/dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#sitinRecordsTable').DataTable({
        "pageLength": 10,
        "order": [[5, 'desc']]
    });
});
</script>
</body>
</html>