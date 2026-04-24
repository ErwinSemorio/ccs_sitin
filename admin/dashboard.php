<?php
session_start();
// 🔒 Security: Ensure only logged-in admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ccs_sitin/login.php");
    exit();
}

include __DIR__ . "/../config/database.php";

// 📊 Fetch Dashboard Statistics
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students"))['total'] ?? 0;
$total_sitin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sitin_records"))['total'] ?? 0;
$active_sitin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sitin_records WHERE status='Active'"))['total'] ?? 0;

// 📢 Fetch Latest Announcements for Initial Load
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY date DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Admin | Dashboard</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Space Theme CSS -->
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .section-title { font-size: 1.8rem; margin-bottom: 2rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 28px; background: var(--accent-cyan); border-radius: 2px; }
        .ann-text { white-space: pre-line; font-size: 0.9rem; color: var(--text-primary); line-height: 1.5; }
        .ann-date { font-size: 0.8rem; color: var(--accent-cyan); margin-bottom: 0.4rem; font-family: 'JetBrains Mono', monospace; }
        @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <!-- 🔹 Space Theme Navbar -->
    <nav class="navbar-space">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <div class="navbar-brand-space">
                <i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i>
                CCS Admin Dashboard
            </div>
            <div class="nav-links-space">
                <a href="/ccs_sitin/admin/dashboard.php" class="nav-link-space active">Home</a>
                <a href="/ccs_sitin/admin/search.php" class="nav-link-space">Search</a>
                <a href="/ccs_sitin/admin/students.php" class="nav-link-space">Students</a>
                <a href="/ccs_sitin/admin/sitin.php" class="nav-link-space">Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Sit-in Records</a>
                <a href="/ccs_sitin/admin/reports.php" class="nav-link-space">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php" class="nav-link-space">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/admin/add_reward.php" class="nav-link-space">Add Reward</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <!-- 🔹 Main Content -->
    <div class="page-container">
        <h2 class="section-title">Dashboard Overview</h2>
        
        <div class="grid-2">
            <!-- 📊 Statistics & Chart Card -->
            <div class="glass-card fade-in-space">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="bi bi-bar-chart-fill" style="color: var(--accent-cyan);"></i>
                        Statistics
                    </h3>
                </div>
                <div style="padding: 1.5rem;">
                    <!-- Stat 1 -->
                    <div class="stat-card-space" style="background: rgba(10, 15, 30, 0.4); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                        <div class="stat-icon-space" style="background: rgba(59, 130, 246, 0.2); color: var(--accent-blue);">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="stat-value-space"><?php echo $total_students; ?></div>
                            <div class="stat-label-space">Students Registered</div>
                        </div>
                    </div>
                    <!-- Stat 2 -->
                    <div class="stat-card-space" style="background: rgba(10, 15, 30, 0.4); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                        <div class="stat-icon-space" style="background: rgba(245, 158, 11, 0.2); color: var(--accent-gold);">
                            <i class="bi bi-laptop-fill"></i>
                        </div>
                        <div>
                            <div class="stat-value-space"><?php echo $active_sitin; ?></div>
                            <div class="stat-label-space">Currently Sit-in</div>
                        </div>
                    </div>
                    <!-- Stat 3 -->
                    <div class="stat-card-space" style="background: rgba(10, 15, 30, 0.4); border-radius: 8px; padding: 1rem;">
                        <div class="stat-icon-space" style="background: rgba(16, 185, 129, 0.2); color: var(--accent-green);">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="stat-value-space"><?php echo $total_sitin; ?></div>
                            <div class="stat-label-space">Total Sit-in</div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div style="margin-top: 2rem; height: 250px; position: relative;">
                        <canvas id="purposeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 📢 Announcements Card -->
            <div class="glass-card fade-in-space" style="animation-delay: 0.1s;">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="bi bi-megaphone-fill" style="color: var(--accent-cyan);"></i>
                        Announcement
                    </h3>
                </div>
                <div style="padding: 1.5rem;">
                    <!-- Post Form -->
                    <form id="announcementForm">
                        <div class="form-group-space">
                            <textarea class="form-control-space" id="announceText" rows="4" placeholder="Write a new announcement..." style="resize: vertical; color: var(--text-primary);" required maxlength="1000"></textarea>
                        </div>
                        <button type="submit" class="btn-space btn-space-success" id="postBtn">
                            <i class="bi bi-send"></i> Post Announcement
                        </button>
                    </form>

                    <!-- Posted List -->
                    <h4 style="margin: 2rem 0 1rem; color: #fff; font-size: 1.1rem;">Posted Announcements</h4>
                    <div id="postedList" style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
                        <?php if ($announcements && mysqli_num_rows($announcements) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($announcements)): ?>
                            <div style="padding: 1rem; border-bottom: 1px solid var(--space-border); animation: fadeInSpace 0.4s ease;">
                                <div class="ann-date">CCS Admin | <?= date('Y-M-d', strtotime($row['date'])) ?></div>
                                <div class="ann-text"><?= htmlspecialchars($row['message']) ?></div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="padding: 1.5rem; text-align: center; color: var(--text-muted);">
                                <i class="bi bi-chat-square-text" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.4;"></i>
                                No announcements posted yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 Scripts -->
    <script>
        // 📊 Chart.js Configuration
        const ctx = document.getElementById('purposeChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['C#', 'C', 'Java', 'ASP.Net', 'PHP'],
                datasets: [{
                    data: [4, 3, 3, 2, 3],
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
                        labels: { color: '#94a3b8', font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }, padding: 15 }
                    }
                }
            }
        });

        // 📢 AJAX Announcement Posting
        document.getElementById('announcementForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('postBtn');
            const text = document.getElementById('announceText').value.trim();
            const list = document.getElementById('postedList');
            
            if (!text) { alert('Please enter an announcement'); return; }
            
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Posting...';
            
            try {
                const formData = new URLSearchParams();
                formData.append('message', text);
                
                const response = await fetch('/ccs_sitin/process/add_announcement.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Remove empty state if present
                    const emptyState = list.querySelector('.text-muted');
                    if (emptyState) emptyState.remove();
                    
                    // Prepend new announcement
                    const now = new Date().toISOString().slice(0, 10);
                    const newDiv = document.createElement('div');
                    newDiv.style.cssText = 'padding: 1rem; border-bottom: 1px solid var(--space-border); animation: fadeInSpace 0.4s ease;';
                    newDiv.innerHTML = `
                        <div class="ann-date">CCS Admin | ${now}</div>
                        <div class="ann-text">${text.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                    `;
                    list.insertBefore(newDiv, list.firstChild);
                    
                    document.getElementById('announceText').value = '';
                    alert('✅ Announcement posted successfully!');
                } else {
                    alert('❌ Error: ' + data.message);
                }
            } catch (err) {
                console.error(err);
                alert('❌ Network error. Check if /ccs_sitin/process/add_announcement.php exists.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send"></i> Post Announcement';
            }
        });
    </script>
</body>
</html>