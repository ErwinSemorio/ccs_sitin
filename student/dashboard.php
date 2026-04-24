<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id_number = ?");
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

// Get profile photo
$profile_photo = $student['profile_photo'] ?? 'default.jpg';
$photo_path = "/ccs_sitin/uploads/profiles/" . $profile_photo;
$photo_exists = file_exists(__DIR__ . '/../uploads/profiles/' . $profile_photo);

// Rewards calculation
$points = $student['points'] ?? 0;
$level = 'Bronze'; $next_threshold = 100; $color = '#cd7f32';
if ($points >= 500) { $level = 'Gold'; $next_threshold = 1000; $color = '#FFD700'; }
elseif ($points >= 200) { $level = 'Silver'; $next_threshold = 500; $color = '#C0C0C0'; }
$progress_percent = ($points >= $next_threshold) ? 100 : ($points / $next_threshold) * 100;

// Announcements
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | CCS Sit-In</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .grid-3 { display: grid; grid-template-columns: 300px 1fr 1fr; gap: 2rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem; }
        @media (max-width: 1100px) { .grid-3, .grid-2 { grid-template-columns: 1fr; } }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        
        .profile-circle { 
            width: 120px; height: 120px; 
            background: rgba(0, 212, 255, 0.1); 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            margin: 0 auto 1rem; 
            border: 3px solid var(--accent-cyan);
            overflow: hidden;
            box-shadow: var(--shadow-glow);
        }
        .profile-circle img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .student-detail { display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--space-border); font-size: 0.9rem; }
        .student-detail:last-child { border-bottom: none; }
        .announcement-scroll { max-height: 400px; overflow-y: auto; }
        .announcement-item { padding: 1rem; border-bottom: 1px solid var(--space-border); }
        .announcement-item:last-child { border-bottom: none; }
        .ann-date { font-size: 0.75rem; color: var(--accent-cyan); margin-bottom: 0.25rem; font-family: 'JetBrains Mono', monospace; }
        .ann-msg { font-size: 0.9rem; color: var(--text-primary); white-space: pre-line; }
    </style>
</head>
<body>
    <!-- Space Theme Navbar -->
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space">
                <i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i>
                CCS Sit-In System
            </div>
            <div class="nav-links-space">
                <a href="dashboard.php" class="nav-link-space active">Home</a>
                <a href="notifications.php" class="nav-link-space">Notification</a>
                <a href="edit_profile.php" class="nav-link-space">Edit Profile</a>
                <a href="history.php" class="nav-link-space">History</a>
                <a href="reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">Dashboard Overview</h2>
        
        <div class="grid-3">
            <!-- 1. Student Info with Profile Photo -->
            <div class="glass-card fade-in-space">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                        <i class="bi bi-person-badge" style="color: var(--accent-cyan);"></i> Student Info
                    </h3>
                </div>
                <div style="padding: 1.5rem; text-align: center;">
                    <!-- Profile Photo -->
                    <div class="profile-circle">
                        <img src="<?= $photo_exists ? $photo_path : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22 viewBox=%220 0 120 120%22%3E%3Crect fill=%22%231e293b%22 width=%22120%22 height=%22120%22/%3E%3Ccircle cx=%2260%22 cy=%2250%22 r=%2225%22 fill=%22%2300d4ff%22/%3E%3Cpath d=%22M60 85 Q30 85 30 100 L30 110 L90 110 L90 100 Q90 85 60 85%22 fill=%22%2300d4ff%22/%3E%3C/svg%3E' ?>" 
                             alt="Profile"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22 viewBox=%220 0 120 120%22%3E%3Crect fill=%22%231e293b%22 width=%22120%22 height=%22120%22/%3E%3Ccircle cx=%2260%22 cy=%2250%22 r=%2225%22 fill=%22%2300d4ff%22/%3E%3Cpath d=%22M60 85 Q30 85 30 100 L30 110 L90 110 L90 100 Q90 85 60 85%22 fill=%22%2300d4ff%22/%3E%3C/svg%3E'">
                    </div>
                    <h4 style="margin: 0.5rem 0; color: #fff;"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h4>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;"><?= htmlspecialchars($student['course']) ?></p>
                    <div style="text-align: left;">
                        <div class="student-detail">
                            <span style="color: var(--text-muted);">ID Number</span>
                            <span style="font-family: 'JetBrains Mono', monospace; color: var(--accent-cyan);"><?= htmlspecialchars($student['id_number']) ?></span>
                        </div>
                        <div class="student-detail">
                            <span style="color: var(--text-muted);">Email</span>
                            <span><?= htmlspecialchars($student['email']) ?></span>
                        </div>
                        <div class="student-detail">
                            <span style="color: var(--text-muted);">Sessions</span>
                            <span><?= $student['session'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Rewards -->
            <div class="glass-card fade-in-space" style="animation-delay: 0.1s;">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                        <i class="bi bi-trophy" style="color: var(--accent-gold);"></i> Your Rewards
                    </h3>
                </div>
                <div style="padding: 1.5rem; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: <?= $color; ?>; margin-bottom: 0.5rem;">🏆 <?= $level ?> Member</div>
                    <div style="font-size: 3rem; font-weight: 800; color: #fff; margin: 1rem 0;"><?= $points ?></div>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">points earned</p>
                    <div style="margin-bottom: 1.5rem;">
                        <small style="color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Progress to <?= $next_threshold ?> pts</small>
                        <div style="background: rgba(100, 120, 160, 0.2); border-radius: 99px; height: 8px; overflow: hidden;">
                            <div style="background: <?= $color; ?>; height: 100%; width: <?= $progress_percent ?>%; border-radius: 99px; transition: width 0.5s ease;"></div>
                        </div>
                    </div>
                    <div style="background: rgba(10, 15, 30, 0.6); padding: 1rem; border-radius: 8px; text-align: left; font-size: 0.85rem;">
                        <strong style="color: var(--accent-cyan);">💡 How to earn:</strong>
                        <ul style="margin: 0.5rem 0 0 1.2rem; padding: 0; color: var(--text-secondary);">
                            <li>+10 pts per completed sit-in</li>
                            <li>+50 pts perfect attendance</li>
                            <li>+5 pts helpful feedback</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 3. Announcements -->
            <div class="glass-card fade-in-space" style="animation-delay: 0.2s;">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                        <i class="bi bi-megaphone" style="color: var(--accent-cyan);"></i> Announcements
                    </h3>
                </div>
                <div class="announcement-scroll" style="padding: 0 1.25rem;">
                    <?php if($announcements && mysqli_num_rows($announcements) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($announcements)): ?>
                        <div class="announcement-item">
                            <div class="ann-date">CCS Admin | <?= date('Y-M-d', strtotime($row['date'])) ?></div>
                            <div class="ann-msg"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                            <i class="bi bi-chat-square-text" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4;"></i>
                            No announcements yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Rules Card -->
        <div class="glass-card fade-in-space" style="margin-top: 2rem;">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> Laboratory Rules
                </h3>
            </div>
            <div style="padding: 1.5rem;">
                <ol style="color: var(--text-primary); line-height: 1.8; padding-left: 1.5rem;">
                    <li>Maintain silence, proper decorum, and discipline inside the laboratory.</li>
                    <li>Games are not allowed inside the lab (computer games, card games, etc.).</li>
                    <li>Surfing the Internet is allowed <strong>only with permission</strong> of the instructor.</li>
                    <li>Students must wear proper attire and ID while inside the laboratory.</li>
                    <li><strong>NO</strong> eating and drinking inside the laboratory.</li>
                    <li>Follow all instructions from the laboratory instructor or proctor.</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>