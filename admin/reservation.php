<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Space Theme CSS -->
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
    </style>
</head>
<body>
    <!-- Space Theme Navbar -->
    <nav class="navbar-space">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Admin</div>
            <div class="nav-links-space">
                <a href="/ccs_sitin/admin/dashboard.php" class="nav-link-space">Home</a>
                <a href="/ccs_sitin/admin/search.php" class="nav-link-space">Search</a>
                <a href="/ccs_sitin/admin/students.php" class="nav-link-space">Students</a>
                <a href="/ccs_sitin/admin/sitin.php" class="nav-link-space">Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Records</a>
                <a href="/ccs_sitin/admin/reports.php" class="nav-link-space">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php" class="nav-link-space">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space active">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/admin/add_reward.php" class="nav-link-space">Add Reward</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">📅 Lab Reservations</h2>
        <div class="glass-card fade-in-space">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem; font-size: 1.1rem;">
                    <i class="bi bi-calendar-check" style="color: var(--accent-cyan);"></i> Reservation Logs
                </h3>
                <input type="text" id="searchRes" class="form-control-space" placeholder="Search reservations..." style="width: 250px; margin-bottom: 0;" onkeyup="filterTable()">
            </div>
            <div class="table-container-space">
                <table class="data-table-space" id="resTable">
                    <thead>
                        <tr>
                            <th>Reservation ID</th>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Lab</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                <i class="bi bi-calendar-x" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                No reservations available yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchRes').value.toLowerCase();
            document.querySelectorAll('#resTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>