<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Export Logic
if (isset($_GET['export'])) {
    $type = $_GET['export'];
    $filename = "CCS_Report_" . date('Y-m-d');
    
    $data = mysqli_query($conn, "SELECT r.id, r.date, r.time, r.name, r.id_number, r.lab, r.purpose, r.status 
                                 FROM reservations r ORDER BY r.date DESC");
    
    if ($type == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Date', 'Time', 'Name', 'ID Number', 'Lab', 'Purpose', 'Status']);
        while ($row = mysqli_fetch_assoc($data)) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    } elseif ($type == 'doc') {
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment; Filename=" . $filename . ".doc");
        echo "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\"><body>";
        echo "<h1>CCS Reservation Report</h1>";
        echo "<table border='1'><tr><th>Date</th><th>Time</th><th>Name</th><th>Lab</th><th>Status</th></tr>";
        while ($row = mysqli_fetch_assoc($data)) {
            echo "<tr><td>{$row['date']}</td><td>{$row['time']}</td><td>{$row['name']}</td><td>{$row['lab']}</td><td>{$row['status']}</td></tr>";
        }
        echo "</table></body></html>";
        exit();
    }
}

// Helper: returns badge class based on status
function getBadgeClass($status) {
    return match(strtolower(trim($status))) {
        'approved'            => 'badge-space-success',
        'declined','rejected' => 'badge-space-danger',
        default               => 'badge-space-warning'
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container  { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .section-title   { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }

        .export-buttons  { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .btn-export      { padding: 0.6rem 1.2rem; border-radius: var(--radius-sm); font-weight: 600; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; transition: var(--transition); font-size: 0.88rem; }
        .btn-csv         { background: rgba(16,185,129,0.2); color: var(--accent-green); border: 1px solid var(--accent-green); }
        .btn-csv:hover   { background: var(--accent-green); color: #fff; }
        .btn-doc         { background: rgba(59,130,246,0.2); color: var(--accent-blue);  border: 1px solid var(--accent-blue); }
        .btn-doc:hover   { background: var(--accent-blue);  color: #fff; }
        .btn-pdf         { background: rgba(239,68,68,0.2);  color: var(--accent-red);   border: 1px solid var(--accent-red); }
        .btn-pdf:hover   { background: var(--accent-red);   color: #fff; }

        /* Status badges */
        .badge-space         { padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
        .badge-space-success { background: rgba(16,185,129,0.2); color: #10b981; }
        .badge-space-warning { background: rgba(245,158,11,0.2);  color: #f59e0b; }
        .badge-space-danger  { background: rgba(239,68,68,0.2);   color: #ef4444; }

        @media print {
            .navbar-space, .export-buttons { display: none; }
            body { background: #fff; color: #000; }
            .glass-card { border: 1px solid #ccc; box-shadow: none; background: #fff; }
            .data-table-space th { color: #000; background: #f0f0f0; }
            .data-table-space td { color: #000; }
        }
    </style>
</head>
<body>
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Admin Dashboard</div>
            <div class="nav-links-space">
                <a href="/ccs_sitin/admin/dashboard.php"     class="nav-link-space">Home</a>
                <a href="/ccs_sitin/admin/search.php"        class="nav-link-space">Search</a>
                <a href="/ccs_sitin/admin/students.php"      class="nav-link-space">Students</a>
                <a href="/ccs_sitin/admin/sitin.php"         class="nav-link-space">Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Records</a>
                <a href="/ccs_sitin/admin/reports.php"       class="nav-link-space active">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php"      class="nav-link-space">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php"   class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php"   class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/admin/add_reward.php"    class="nav-link-space">Add Reward</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="padding:0.5rem 1rem;font-size:0.85rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">📊 System Reports</h2>

        <div class="export-buttons">
            <a href="?export=csv" class="btn-export btn-csv">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </a>
            <a href="?export=doc" class="btn-export btn-doc">
                <i class="bi bi-file-earmark-word"></i> Export DOC
            </a>
            <a href="#" onclick="window.print()" class="btn-export btn-pdf">
                <i class="bi bi-file-earmark-pdf"></i> Print / Save PDF
            </a>
        </div>

        <div class="glass-card fade-in-space">
            <div style="padding:1.25rem; border-bottom:1px solid var(--space-border);">
                <h3 style="margin:0; display:flex; align-items:center; gap:0.5rem; font-size:1.1rem;">
                    <i class="bi bi-table" style="color:var(--accent-cyan);"></i> Reservation Logs
                </h3>
            </div>
            <div class="table-container-space">
                <table class="data-table-space">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Student Name</th>
                            <th>ID Number</th>
                            <th>Lab</th>
                            <th>Purpose</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $reports = mysqli_query($conn, "SELECT * FROM reservations ORDER BY date DESC, time ASC");
                        if (mysqli_num_rows($reports) > 0):
                            while ($row = mysqli_fetch_assoc($reports)):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['time']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td style="font-family:'JetBrains Mono',monospace; color:var(--accent-cyan);">
                                <?= htmlspecialchars($row['id_number']) ?>
                            </td>
                            <td>Lab <?= htmlspecialchars($row['lab']) ?></td>
                            <td><?= htmlspecialchars($row['purpose']) ?></td>
                            <td>
                                <span class="badge-space <?= getBadgeClass($row['status']) ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">
                                No records found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>