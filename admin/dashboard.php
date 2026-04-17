<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

$students_result = mysqli_query($conn, "SELECT * FROM students ORDER BY last_name ASC");
$students_data = [];
while ($row = mysqli_fetch_assoc($students_result)) {
    $students_data[] = $row;
}
$total_students = count($students_data);

$total_sitin_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM sitin_records");
$total_sitin = mysqli_fetch_assoc($total_sitin_result)['total'] ?? 0;
$active_sitin_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM sitin_records WHERE status='Active'");
$active_sitin = mysqli_fetch_assoc($active_sitin_result)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Admin | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .section-title { 
            font-size: 1.8rem; 
            margin-bottom: 2rem; 
            color: #fff;
            display: flex; 
            align-items: center; 
            gap: 0.75rem;
        }
        .section-title::before {
            content: '';
            width: 4px;
            height: 28px;
            background: var(--accent-cyan);
            border-radius: 2px;
        }
        @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space">
                <i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i>
                CCS Admin Dashboard
            </div>
            <div class="nav-links-space">
                <a href="dashboard.php" class="nav-link-space active">Home</a>
                <a href="students.php" class="nav-link-space">Students</a>
                <a href="sitin.php" class="nav-link-space">Sit-in</a>
                <a href="sitin_records.php" class="nav-link-space">Records</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">Dashboard Overview</h2>
        
        <div class="grid-2">
            <!-- Statistics Card -->
            <div class="glass-card">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="bi bi-bar-chart-fill" style="color: var(--accent-cyan);"></i>
                        Statistics
                    </h3>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="margin-bottom: 1rem;">
                        <div class="stat-card-space" style="background: rgba(10, 15, 30, 0.4); border-radius: 8px; padding: 1rem;">
                            <div class="stat-icon-space" style="background: rgba(59, 130, 246, 0.2); color: var(--accent-blue);">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <div class="stat-value-space"><?php echo $total_students; ?></div>
                                <div class="stat-label-space">Students Registered</div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <div class="stat-card-space" style="background: rgba(10, 15, 30, 0.4); border-radius: 8px; padding: 1rem;">
                            <div class="stat-icon-space" style="background: rgba(245, 158, 11, 0.2); color: var(--accent-gold);">
                                <i class="bi bi-laptop-fill"></i>
                            </div>
                            <div>
                                <div class="stat-value-space"><?php echo $active_sitin; ?></div>
                                <div class="stat-label-space">Currently Sit-in</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="stat-card-space" style="background: rgba(10, 15, 30, 0.4); border-radius: 8px; padding: 1rem;">
                            <div class="stat-icon-space" style="background: rgba(16, 185, 129, 0.2); color: var(--accent-green);">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <div class="stat-value-space"><?php echo $total_sitin; ?></div>
                                <div class="stat-label-space">Total Sit-in</div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 2rem; height: 250px;">
                        <canvas id="purposeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Announcement Card -->
            <div class="glass-card">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="bi bi-megaphone-fill" style="color: var(--accent-cyan);"></i>
                        Announcement
                    </h3>
                </div>
                <div style="padding: 1.5rem;">
                    <div class="form-group-space">
                        <textarea class="form-control-space" id="announceText" rows="4" placeholder="Write a new announcement..." style="resize: vertical;"></textarea>
                    </div>
                    <button class="btn-space btn-space-success" onclick="postAnnouncement()">
                        <i class="bi bi-send"></i> Post Announcement
                    </button>
                    
                    <h4 style="margin: 2rem 0 1rem; color: #fff; font-size: 1.1rem;">Posted Announcements</h4>
                    <div id="postedList">
                        <div style="padding: 1rem; border-bottom: 1px solid var(--space-border);">
                            <div style="font-size: 0.85rem; color: var(--accent-cyan); margin-bottom: 0.5rem; font-family: 'JetBrains Mono', monospace;">
                                CCS Admin | 2026-Feb-11
                            </div>
                        </div>
                        <div style="padding: 1rem;">
                            <div style="font-size: 0.85rem; color: var(--accent-cyan); margin-bottom: 0.5rem; font-family: 'JetBrains Mono', monospace;">
                                CCS Admin | 2024-May-08
                            </div>
                            <div style="color: var(--text-secondary); font-size: 0.9rem;">
                                Important Announcement — We are excited to announce the launch of our new website!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Chart
    const ctx = document.getElementById('purposeChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['C#','C','Java','ASP.Net','PHP'],
            datasets: [{
                data: [4,3,3,2,3],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(0, 212, 255, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        padding: 15
                    }
                }
            }
        }
    });

    // Announcement
    function postAnnouncement() {
        const text = document.getElementById('announceText').value.trim();
        if (!text) {
            alert('Please enter an announcement');
            return;
        }
        
        const list = document.getElementById('postedList');
        const div = document.createElement('div');
        div.style.cssText = 'padding: 1rem; border-bottom: 1px solid var(--space-border); animation: fadeInSpace 0.4s ease;';
        div.innerHTML = `
            <div style="font-size: 0.85rem; color: var(--accent-cyan); margin-bottom: 0.5rem; font-family: 'JetBrains Mono', monospace;">
                CCS Admin | ${new Date().toISOString().slice(0,10)}
            </div>
            <div style="color: var(--text-primary); font-size: 0.9rem;">${text}</div>
        `;
        list.insertBefore(div, list.firstChild);
        document.getElementById('announceText').value = '';
    }
    </script>
</body>
</html>