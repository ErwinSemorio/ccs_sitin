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
    <title>Reports | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem; }
        @media(max-width: 900px) { .chart-grid { grid-template-columns: 1fr; } }
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
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Records</a>
                <a href="/ccs_sitin/admin/reports.php" class="nav-link-space active">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php" class="nav-link-space">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <div class="section-title">Sit-in Analytics</div>
        
        <div class="chart-grid">
            <div class="glass-card">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="bi bi-bar-chart-fill" style="color: var(--accent-blue);"></i> By Purpose
                    </h3>
                </div>
                <div style="padding: 1.5rem; position: relative; height: 300px;">
                    <canvas id="purposeChart"></canvas>
                </div>
            </div>

            <div class="glass-card">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="bi bi-building" style="color: var(--accent-green);"></i> By Lab
                    </h3>
                </div>
                <div style="padding: 1.5rem; position: relative; height: 300px;">
                    <canvas id="labChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#94a3b8', font: { family: "'Plus Jakarta Sans', sans-serif" } } } },
            scales: {
                x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(100,120,160,0.1)' } },
                y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(100,120,160,0.1)' } }
            }
        };

        new Chart(document.getElementById('purposeChart'), {
            type: 'bar',
            data: {
                labels: ['C#','Java','PHP','Web Dev','Database'],
                datasets: [{ label: 'Sessions', data: [12, 19, 8, 15, 10], backgroundColor: 'rgba(59, 130, 246, 0.7)', borderRadius: 6 }]
            },
            options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { display: false } } }
        });

        new Chart(document.getElementById('labChart'), {
            type: 'line',
            data: {
                labels: ['521','522','523','524','525','526'],
                datasets: [{ label: 'Activity', data: [5, 2, 8, 12, 6, 4], borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.4 }]
            },
            options: chartDefaults
        });
    </script>
</body>
</html>