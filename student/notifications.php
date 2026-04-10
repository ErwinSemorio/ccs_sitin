<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY date DESC");
$active_page = 'notifications';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | CCS Sit-In</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        :root { --primary: #0d47a1; --bg-body: #f3f4f8; --card-bg: #ffffff; --text-main: #1f2937; --text-muted: #6b7280; --border-color: #e5e7eb; --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1); --radius-lg: 16px; }
        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: var(--text-main); }
        .navbar-custom { background-color: var(--primary); box-shadow: 0 4px 12px rgba(13,71,161,0.2); padding: 0.8rem 0; }
        .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 500; font-size: 0.9rem; padding: 0.5rem 1rem !important; }
        .nav-link:hover, .nav-link.active { color: white !important; background: rgba(255,255,255,0.15); border-radius: 8px; }
        .btn-logout { background: #ffc107; color: #08347a; font-weight: 700; border: none; border-radius: 8px; padding: 0.4rem 1rem; }
        .custom-card { background: var(--card-bg); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--border-color); height: 100%; overflow: hidden; }
        .card-header-custom { padding: 1.25rem; font-weight: 700; font-size: 1.1rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.5rem; background: #eff6ff; color: var(--primary); }
        .notif-item { padding: 1.25rem; border-bottom: 1px solid var(--border-color); transition: background 0.2s; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: #f8fafc; }
        .notif-date { font-size: 0.75rem; color: var(--primary); font-weight: 700; margin-bottom: 0.25rem; }
        .notif-msg { font-size: 0.95rem; line-height: 1.6; color: #374151; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="#">🎓 CCS Sit-In</a>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item"><a class="nav-link <?= $active_page=='dashboard'?'active':'' ?>" href="dashboard.php"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link <?= $active_page=='notifications'?'active':'' ?>" href="notifications.php"><i class="bi bi-bell me-1"></i>Notification</a></li>
                    <li class="nav-item"><a class="nav-link <?= $active_page=='edit_profile'?'active':'' ?>" href="edit_profile.php"><i class="bi bi-pencil-square me-1"></i>Edit Profile</a></li>
                    <li class="nav-item"><a class="nav-link <?= $active_page=='history'?'active':'' ?>" href="history.php"><i class="bi bi-clock-history me-1"></i>History</a></li>
                    <li class="nav-item"><a class="nav-link <?= $active_page=='reservation'?'active':'' ?>" href="reservation.php"><i class="bi bi-calendar-check me-1"></i>Reservation</a></li>
                    <li class="nav-item ms-2"><a href="/ccs_sitin/logout.php" class="btn-logout btn">Log out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="custom-card">
                    <div class="card-header-custom"><i class="bi bi-bell-fill"></i> Latest Announcements</div>
                    <div style="max-height: 70vh; overflow-y: auto;">
                        <?php if($announcements && mysqli_num_rows($announcements) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($announcements)): ?>
                            <div class="notif-item">
                                <div class="notif-date"><?= date('F d, Y', strtotime($row['date'])) ?></div>
                                <div class="notif-msg"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="p-5 text-center text-muted">
                                <i class="bi bi-inbox mb-3" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                <p class="mb-0">No announcements at this time.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>