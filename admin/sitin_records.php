<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$records = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM sitin_records ORDER BY date DESC, time_in DESC"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Records | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid var(--space-border); flex-wrap: wrap; gap: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Admin</div>
            <div class="nav-links-space">
                <a href="/ccs_sitin/admin/dashboard.php" class="nav-link-space">Home</a>
                <a href="/ccs_sitin/admin/search.php" class="nav-link-space">Search</a>
                <a href="/ccs_sitin/admin/students.php" class="nav-link-space">Students</a>
                <a href="/ccs_sitin/admin/sitin.php" class="nav-link-space">Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space active">Records</a>
                <a href="/ccs_sitin/admin/reports.php" class="nav-link-space">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php" class="nav-link-space">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <div class="section-title">Sit-in History Logs</div>
        <div class="glass-card">
            <div class="toolbar">
                <div class="badge-space badge-space-info" style="font-size: 0.85rem;">
                    Total Records: <?php echo count($records); ?>
                </div>
                <input type="text" class="form-control-space" style="width: 250px; margin-bottom: 0;" id="searchInput" placeholder="Search records..." onkeyup="filterTable()">
            </div>
            <div class="table-container-space">
                <table class="data-table-space" id="recordsTable">
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Purpose</th><th>Lab</th><th>Date</th><th>Time In</th><th>Time Out</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($records as $r): ?>
                        <tr>
                            <td style="font-family: 'JetBrains Mono', monospace; color: var(--accent-cyan);"><?php echo htmlspecialchars($r['id_number']); ?></td>
                            <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($r['purpose']); ?></td>
                            <td>Lab <?php echo htmlspecialchars($r['lab']); ?></td>
                            <td style="color: var(--text-muted);"><?php echo htmlspecialchars($r['date']); ?></td>
                            <td style="font-family: 'JetBrains Mono', monospace;"><?php echo htmlspecialchars($r['time_in']); ?></td>
                            <td style="font-family: 'JetBrains Mono', monospace; color: <?php echo $r['time_out'] ? 'var(--text-primary)' : 'var(--text-muted)'; ?>">
                                <?php echo $r['time_out'] ? htmlspecialchars($r['time_out']) : '—'; ?>
                            </td>
                            <td>
                                <?php if ($r['status'] === 'Active'): ?>
                                    <span class="badge-space badge-space-warning">Active</span>
                                <?php else: ?>
                                    <span class="badge-space badge-space-success">Done</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('#recordsTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>