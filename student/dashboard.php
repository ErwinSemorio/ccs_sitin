<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}

include __DIR__ . "/../config/database.php";

$id = $_SESSION['user_id'];

// 🔒 SECURE: Fetch student info using prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id_number = ?");
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

// Calculate Reward Level
$points = $student['points'] ?? 0;
$level = 'Bronze';
$next_threshold = 100;
$color = '#cd7f32';
$icon = '🥉';

if ($points >= 500) {
    $level = 'Gold';
    $next_threshold = 1000;
    $color = '#FFD700';
    $icon = '🥇';
} elseif ($points >= 200) {
    $level = 'Silver';
    $next_threshold = 500;
    $color = '#C0C0C0';
    $icon = '🥈';
}

// Progress bar calculation
$progress_percent = ($points >= $next_threshold) ? 100 : ($points / $next_threshold) * 100;

// Fetch announcements
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | CCS Sit-In</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    
    <style>
        :root {
            --primary: #0d47a1;
            --primary-dark: #08347a;
            --bg-body: #f3f4f8;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius-lg: 16px;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }

        /* --- Navbar --- */
        .navbar-custom {
            background-color: var(--primary);
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.2);
            padding: 0.8rem 0;
        }
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            padding: 0.5rem 1rem !important;
        }
        .nav-link:hover, .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
        }
        .btn-logout {
            background: #ffc107;
            color: var(--primary-dark);
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 0.4rem 1rem;
            transition: transform 0.2s;
        }
        .btn-logout:hover {
            background: #ffca2c;
            transform: translateY(-2px);
        }

        /* --- Cards --- */
        .custom-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            height: 100%;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .card-header-custom {
            padding: 1.25rem;
            font-weight: 700;
            font-size: 1.1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card-header-blue { background: #eff6ff; color: var(--primary); }
        .card-header-gold { background: #fffbeb; color: #b45309; }
        
        /* --- Student Profile --- */
        .profile-circle {
            width: 100px;
            height: 100px;
            background: #e0e7ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
            color: var(--primary);
            border: 4px solid white;
            box-shadow: var(--shadow-sm);
        }
        .student-detail {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px dashed #e5e7eb;
            font-size: 0.9rem;
        }
        .student-detail:last-child { border-bottom: none; }
        .label { color: var(--text-muted); }
        .value { font-weight: 600; }

        /* --- Rewards --- */
        .rewards-wrapper {
            text-align: center;
            padding: 1.5rem;
        }
        .level-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
            color: <?php echo $color; ?>;
        }
        .points-display {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1;
            color: var(--text-main);
        }
        .points-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 1.5rem; }
        
        .progress-custom {
            height: 10px;
            background-color: #e5e7eb;
            border-radius: 99px;
            overflow: hidden;
            margin: 0.5rem 0;
        }
        .progress-bar-custom {
            height: 100%;
            background-color: <?php echo $color; ?>;
            width: <?php echo $progress_percent; ?>%;
            border-radius: 99px;
            transition: width 1s ease-in-out;
        }

        .earn-tips {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1rem;
            text-align: left;
            margin-top: 1.5rem;
        }
        .earn-tips h6 { font-size: 0.85rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem; }
        .earn-tips ul { padding-left: 1.2rem; margin-bottom: 0; }
        .earn-tips li { font-size: 0.8rem; color: var(--text-main); margin-bottom: 0.25rem; }

        /* --- Announcements --- */
        .announcement-scroll {
            max-height: 420px;
            overflow-y: auto;
            padding: 0;
        }
        .announcement-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }
        .announcement-item:last-child { border-bottom: none; }
        .ann-date { font-size: 0.75rem; color: var(--primary); font-weight: 700; margin-bottom: 0.25rem; }
        .ann-msg { font-size: 0.9rem; line-height: 1.5; }

        /* --- Rules --- */
        .rules-list { padding: 0 1.5rem 1.5rem; }
        .rules-list li {
            margin-bottom: 0.75rem;
            line-height: 1.6;
            font-size: 0.9rem;
            color: #4b5563;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">🎓 CCS Sit-In System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell me-1"></i>Notification</a></li>
                    <li class="nav-item"><a class="nav-link" href="edit_profile.php"><i class="bi bi-pencil-square me-1"></i>Edit Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="history.php"><i class="bi bi-clock-history me-1"></i>History</a></li>
                    <li class="nav-item"><a class="nav-link" href="reservation.php"><i class="bi bi-calendar-check me-1"></i>Reservation</a></li>
                    <li class="nav-item ms-2"><a href="/ccs_sitin/logout.php" class="btn-logout btn">Log out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="row g-4">
            
            <!-- 1. Student Info -->
            <div class="col-lg-4">
                <div class="custom-card">
                    <div class="card-header-custom card-header-blue">
                        <i class="bi bi-person-badge"></i> Student Information
                    </div>
                    <div class="p-4 text-center">
                        <div class="profile-circle">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($student['first_name'] . " " . $student['last_name']) ?></h5>
                        <p class="text-muted small"><?= htmlspecialchars($student['course']) ?></p>
                        
                        <div class="mt-3 text-start">
                            <div class="student-detail"><span class="label"><i class="bi bi-hash me-2"></i>ID Number</span><span class="value"><?= htmlspecialchars($student['id_number']) ?></span></div>
                            <div class="student-detail"><span class="label"><i class="bi bi-envelope me-2"></i>Email</span><span class="value"><?= htmlspecialchars($student['email']) ?></span></div>
                            <div class="student-detail"><span class="label"><i class="bi bi-lightning me-2"></i>Sessions</span><span class="value"><?= htmlspecialchars($student['session'] ?? 0) ?></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rewards Card -->
<div class="col-md-4">
    <div class="card h-100">
        <div class="card-header bg-warning text-dark">
            <i class="bi bi-trophy me-2"></i>Your Rewards
        </div>
        <div class="card-body text-center">
            <!-- Level Badge -->
            <?php
            $points = $student['points'] ?? 0;
            $level = 'Bronze';
            $color = '#cd7f32';
            $next = 100;
            
            if ($points >= 500) { $level = 'Gold'; $color = '#FFD700'; $next = 1000; }
            elseif ($points >= 200) { $level = 'Silver'; $color = '#C0C0C0'; $next = 500; }
            ?>
            
            <div style="font-size: 1.5rem; font-weight: 800; color: <?= $color ?>; margin-bottom: 5px;">
                🏆 <?= $level ?> Member
            </div>
            
            <!-- Points Display -->
            <div style="font-size: 3rem; font-weight: 800; color: #0d47a1; margin: 10px 0;">
                <?= $points ?>
            </div>
            <p class="text-muted mb-3">points earned</p>
            
            <!-- Progress to Next Level -->
            <div class="mb-3">
                <small class="text-muted d-block mb-1">Progress to <?= $next ?> pts</small>
                <div class="progress" style="height: 8px;">
                    <?php $progress = min(100, ($points / $next) * 100); ?>
                    <div class="progress-bar" style="width: <?= $progress ?>%; background: <?= $color ?>;"></div>
                </div>
            </div>
            
            <!-- How to Earn -->
            <div class="text-start small" style="background: #f8f9fa; padding: 12px; border-radius: 8px;">
                <strong>💡 How to earn points:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    <li>+10 pts per completed sit-in</li>
                    <li>+50 pts for perfect attendance</li>
                    <li>+5 pts for helpful feedback</li>
                </ul>
            </div>
        </div>
    </div>
</div>

            <!-- 3. Announcements -->
            <div class="col-lg-4">
                <div class="custom-card">
                    <div class="card-header-custom card-header-blue">
                        <i class="bi bi-megaphone"></i> Announcements
                    </div>
                    <div class="announcement-scroll">
                    <?php if($announcements && mysqli_num_rows($announcements) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($announcements)): ?>
                    <div class="announcement-item">
                        <div class="announcement-header">
                            CCS Admin | <?= date('Y-M-d', strtotime($row['date'])) ?>
                        </div>
                        <div class="announcement-content">
                            <?= nl2br(htmlspecialchars($row['message'])) ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="announcement-item">
                        <p class="text-muted mb-0">No announcements yet.</p>
                    </div>
                <?php endif; ?> 
                    </div>
                </div>
            </div>

            <!-- 4. Rules & Regulations (Full Width) -->
            <div class="col-12">
                <div class="custom-card">
                    <div class="card-header-custom card-header-blue">
                        <i class="bi bi-shield-lock"></i> Laboratory Rules and Regulations
                    </div>
                    <div class="text-center py-3 border-bottom bg-light">
                        <h6 class="mb-0 fw-bold text-primary">University of Cebu</h6>
                        <small class="text-muted">COLLEGE OF INFORMATION & COMPUTER STUDIES</small>
                    </div>
                    <div class="rules-list">
                        <ol>
                            <li>Maintain silence, proper decorum, and discipline inside the laboratory.</li>
                            <li>Games are not allowed inside the lab. (Includes computer games, card games, etc.)</li>
                            <li>Surfing the Internet is allowed <b>only with permission</b> of the instructor.</li>
                            <li>Students must wear proper attire and ID while inside the laboratory.</li>
                            <li><b>NO</b> eating and drinking inside the laboratory.</li>
                            <li>Follow all instructions from the laboratory instructor or proctor.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>