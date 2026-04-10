<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../config/functions.php";

// Fetch students with weighted score calculation
$sql = "SELECT id_number, first_name, last_name, course, points, total_hours, tasks_completed, weighted_score 
        FROM students 
        ORDER BY weighted_score DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}

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
<style>
/* [Keep your existing styles] */
:root { --navy: #0f2044; --blue: #1a56db; --bg: #f1f5fb; --surface: #ffffff; --border: #e2e8f4; --text: #0f2044; --muted: #64748b; --shadow: 0 4px 20px rgba(15,32,68,.10); --amber-lt: #fef3c7; --amber: #d97706; --gold: #f59e0b; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
.topnav { background: var(--navy); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; height: 58px; box-shadow: 0 2px 20px rgba(0,0,0,.25); }
.topnav-brand { font-size: 15px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; }
.topnav-links { display: flex; align-items: center; gap: 2px; list-style: none; }
.topnav-links a { display: block; padding: 8px 13px; color: rgba(255,255,255,.7); text-decoration: none; font-size: 13px; font-weight: 600; border-radius: 8px; transition: all .18s; }
.topnav-links a:hover, .topnav-links a.active { color: #fff; background: rgba(255,255,255,.12); }
.logout-btn-nav { padding: 7px 16px !important; background: var(--gold) !important; color: var(--navy) !important; font-weight: 800 !important; border-radius: 8px !important; margin-left: 6px; }
.nav-toggle { display: none; font-size: 24px; color: white; cursor: pointer; padding: 8px; background: none; border: none; }
.page { padding: 28px 32px; max-width: 1000px; margin: 0 auto; animation: fadeUp .4s ease both; }
.section-heading { font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 22px; display: flex; align-items: center; gap: 10px; }
.section-heading::before { content: ''; width: 5px; height: 24px; background: var(--blue); border-radius: 3px; }
.card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
.card-head { padding: 16px 20px; border-bottom: 1px solid var(--border); font-weight: 700; font-size: 15px; color: var(--navy); display: flex; justify-content: space-between; align-items: center; }
.leaderboard-item { display: flex; align-items: center; padding: 14px 20px; border-bottom: 1px solid var(--border); transition: background .2s; }
.leaderboard-item:hover { background: #f8fafc; }
.rank { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; margin-right: 15px; }
.rank-1 { background: linear-gradient(135deg, #ffd700, #ffed4e); color: #0f2044; }
.rank-2 { background: linear-gradient(135deg, #c0c0c0, #e8e8e8); color: #0f2044; }
.rank-3 { background: linear-gradient(135deg, #cd7f32, #e8a87c); color: #fff; }
.rank-other { background: var(--bg); color: var(--muted); font-weight: 700; }
.student-info { flex: 1; }
.student-name { font-weight: 700; font-size: 14px; color: var(--text); }
.student-meta { font-size: 12px; color: var(--muted); }
.score-badge { background: var(--amber-lt); color: var(--amber); padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 5px; }
.search-bar { padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; width: 250px; font-family: inherit; outline: none; }
@keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
@media (max-width: 1100px) {
    .nav-toggle { display: block; }
    .topnav-links { display: none; position: absolute; top: 58px; left: 0; right: 0; background: var(--navy); flex-direction: column; padding: 10px 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); gap: 5px; }
    .topnav-links.show { display: flex; }
    .topnav-links a { width: 100%; padding: 12px; text-align: center; }
    .logout-btn-nav { margin: 10px 0 0 0; width: 100%; text-align: center; }
    .page { padding: 16px; }
}
</style>
</head>
<body>
<nav class="topnav">
    <div class="topnav-brand"><div class="dot"></div>College of Computer Studies Admin</div>
    <button class="nav-toggle" onclick="toggleNav()"><i class="bi bi-list"></i></button>
    <ul class="topnav-links" id="topnavLinks">
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="search.php">Search</a></li>
        <li><a href="students.php">Students</a></li>
        <li><a href="sitin.php">Sit-in</a></li>
        <li><a href="sitin_records.php">Sit-in Records</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="reservation.php">Reservation</a></li>
        <li><a href="leaderboard.php" class="active">Leaderboard</a></li>
        <li><a href="add_reward.php">Add Reward</a></li>
        <li><a href="/ccs_sitin/logout.php" class="logout-btn-nav">Log out</a></li>
    </ul>
</nav>

<div class="page">
    <div class="section-heading">🏆 Student Leaderboard</div>
    <div class="card">
        <div class="card-head">
            <span>🏅 Top Performers (Weighted Score)</span>
            <input type="text" class="search-bar" placeholder="Search..." onkeyup="filterLeaderboard()">
        </div>
        <div id="leaderboardList">
            <?php if (count($leaderboard) > 0): ?>
                <?php foreach ($leaderboard as $i => $s): 
                    $rank = $i + 1;
                    $rankClass = $rank <= 3 ? "rank-$rank" : "rank-other";
                    // Calculate score if not already stored
                    $score = $s['weighted_score'] > 0 ? $s['weighted_score'] : 
                             calculateWeightedScore($s['points'], $s['total_hours'], $s['tasks_completed']);
                ?>
                <div class="leaderboard-item">
                    <div class="rank <?php echo $rankClass; ?>"><?php echo $rank; ?></div>
                    <div class="student-info">
                        <div class="student-name"><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></div>
                        <div class="student-meta">
                            <?php echo htmlspecialchars($s['id_number']); ?> • <?php echo htmlspecialchars($s['course']); ?>
                        </div>
                        <div style="font-size:11px;color:var(--muted);margin-top:4px">
                            <span title="Points (60%)">🎁 <?php echo $s['points']; ?></span> • 
                            <span title="Hours (20%)">⏱️ <?php echo $s['total_hours']; ?>h</span> • 
                            <span title="Tasks (20%)">✅ <?php echo $s['tasks_completed']; ?></span>
                        </div>
                    </div>
                    <div class="score-badge">
                        <i class="bi bi-trophy-fill"></i>
                        <?php echo number_format($score, 1); ?> pts
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding:40px; text-align:center; color:var(--muted);">No students have scores yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleNav() { document.getElementById('topnavLinks').classList.toggle('show'); }
function filterLeaderboard() {
    const q = document.querySelector('.search-bar').value.toLowerCase();
    document.querySelectorAll('.leaderboard-item').forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>