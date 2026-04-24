<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../config/functions.php";
$sql = "SELECT id_number, first_name, last_name, course, points, total_hours, tasks_completed, weighted_score FROM students ORDER BY weighted_score DESC";
$result = mysqli_query($conn, $sql);
if (!$result) die("Database Error: " . mysqli_error($conn));
$leaderboard = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Space Theme CSS -->
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1000px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        
        .lb-item {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem 1.25rem; border-bottom: 1px solid var(--space-border);
            transition: var(--transition);
        }
        .lb-item:hover { background: rgba(0, 212, 255, 0.05); }
        .lb-item:last-child { border-bottom: none; }
        
        .rank-badge {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.9rem; flex-shrink: 0;
        }
        .rank-1 { background: linear-gradient(135deg, var(--accent-gold), #f59e0b); color: var(--space-deep); box-shadow: 0 0 10px rgba(251,191,36,0.4); }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #cbd5e1); color: var(--space-deep); }
        .rank-3 { background: linear-gradient(135deg, #b45309, #cd7f32); color: #fff; }
        .rank-other { background: var(--space-surface); border: 1px solid var(--space-border); color: var(--text-secondary); }
        
        .lb-info { flex: 1; }
        .lb-name { font-weight: 700; color: var(--text-primary); font-size: 1rem; }
        .lb-meta { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.2rem; }
        .lb-stats { display: flex; gap: 0.5rem; margin-top: 0.3rem; font-size: 0.7rem; color: var(--text-muted); }
        
        .score-pill {
            background: rgba(251, 191, 36, 0.15); color: var(--accent-gold);
            padding: 0.4rem 0.9rem; border-radius: 99px; font-weight: 700; font-size: 0.9rem;
            display: flex; align-items: center; gap: 0.4rem;
            box-shadow: 0 0 8px rgba(251,191,36,0.2);
        }
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
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space active">Leaderboard</a>
                <a href="/ccs_sitin/admin/add_reward.php" class="nav-link-space">Add Reward</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">🏆 Student Leaderboard</h2>
        <div class="glass-card fade-in-space">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem; font-size: 1.1rem;">
                    <i class="bi bi-trophy" style="color: var(--accent-cyan);"></i> Top Performers (Weighted Score)
                </h3>
                <input type="text" id="searchLb" class="form-control-space" placeholder="Search students..." style="width: 250px; margin-bottom: 0;" onkeyup="filterLb()">
            </div>
            <div id="lbContainer" style="max-height: 600px; overflow-y: auto;">
                <?php if (count($leaderboard) > 0): ?>
                    <?php foreach ($leaderboard as $i => $s):
                        $rank = $i + 1;
                        $rankClass = $rank <= 3 ? "rank-$rank" : "rank-other";
                        $score = $s['weighted_score'] > 0 ? $s['weighted_score'] : calculateWeightedScore($s['points'], $s['total_hours'], $s['tasks_completed']);
                    ?>
                    <div class="lb-item" data-name="<?php echo strtolower($s['first_name'] . ' ' . $s['last_name']); ?>">
                        <div class="rank-badge <?php echo $rankClass; ?>"><?php echo $rank; ?></div>
                        <div class="lb-info">
                            <div class="lb-name"><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></div>
                            <div class="lb-meta"><?php echo htmlspecialchars($s['id_number']); ?> • <?php echo htmlspecialchars($s['course']); ?></div>
                            <div class="lb-stats">
                                <span>🎁 <?php echo $s['points']; ?> pts</span>
                                <span>•</span>
                                <span>⏱️ <?php echo $s['total_hours']; ?>h</span>
                                <span>•</span>
                                <span>✅ <?php echo $s['tasks_completed']; ?> tasks</span>
                            </div>
                        </div>
                        <div class="score-pill">
                            <i class="bi bi-trophy-fill"></i>
                            <?php echo number_format($score, 1); ?> pts
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted);">No students have scores yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function filterLb() {
            const q = document.getElementById('searchLb').value.toLowerCase();
            document.querySelectorAll('.lb-item').forEach(item => {
                item.style.display = item.getAttribute('data-name').includes(q) ? '' : 'none';
            });
        }
    </script>
</body>
</html>