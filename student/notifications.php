<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | CCS Sit-In</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 800px; margin: 0 auto; padding: 2rem; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .notif-scroll { max-height: 700px; overflow-y: auto; }
        .notif-item { padding: 1.25rem; border-bottom: 1px solid var(--space-border); transition: background 0.2s; }
        .notif-item:hover { background: rgba(0, 212, 255, 0.05); }
        .notif-item:last-child { border-bottom: none; }
        .notif-date { font-size: 0.75rem; color: var(--accent-cyan); margin-bottom: 0.5rem; font-family: 'JetBrains Mono', monospace; }
        .notif-msg { font-size: 0.95rem; color: var(--text-primary); line-height: 1.6; white-space: pre-line; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Sit-In System</div>
            <div class="nav-links-space">
                <a href="dashboard.php" class="nav-link-space">Home</a>
                <a href="notifications.php" class="nav-link-space active">Notification</a>
                <a href="edit_profile.php" class="nav-link-space">Edit Profile</a>
                <a href="history.php" class="nav-link-space">History</a>
                <a href="reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">🔔 Latest Announcements</h2>
        <div class="glass-card fade-in-space">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                    <i class="bi bi-bell-fill" style="color: var(--accent-cyan);"></i> Recent Updates
                </h3>
            </div>
            <div class="notif-scroll">
                <?php if($announcements && mysqli_num_rows($announcements) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($announcements)): ?>
                    <div class="notif-item">
                        <div class="notif-date">CCS Admin | <?= date('F d, Y', strtotime($row['date'])) ?></div>
                        <div class="notif-msg"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.4;"></i>
                        <p>No announcements at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>