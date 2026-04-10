<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Fetch students from database
$students_result = mysqli_query($conn, "SELECT * FROM students ORDER BY last_name ASC");
$students_data = [];
while ($row = mysqli_fetch_assoc($students_result)) {
    $students_data[] = $row;
}
$total_students = count($students_data);

// Fetch stats
$total_sitin_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM sitin_records");
$total_sitin = mysqli_fetch_assoc($total_sitin_result)['total'] ?? 0;
$active_sitin_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM sitin_records WHERE status='Active'");
$active_sitin = mysqli_fetch_assoc($active_sitin_result)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>CCS Admin | Sit-In Management</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --navy: #0f2044; --navy2: #162b58; --navy3: #1e3a70;
    --blue: #1a56db; --blue-lt: #e8f0fe;
    --teal: #0694a2; --teal-lt: #e0f5f5;
    --green: #057a55; --green-lt: #def7ec;
    --amber: #d97706; --amber-lt: #fef3c7;
    --red: #e02424; --red-lt: #fde8e8;
    --gold: #f59e0b;
    --bg: #f1f5fb; --surface: #ffffff; --border: #e2e8f4;
    --text: #0f2044; --muted: #64748b;
    --shadow: 0 4px 20px rgba(15,32,68,.10);
    --shadow-sm: 0 2px 8px rgba(15,32,68,.07);
}
html, body { min-height: 100%; background: var(--bg); color: var(--text); font-family: 'Plus Jakarta Sans', sans-serif; }

/* ══ TOPNAV ══ */
.topnav {
    position: sticky; top: 0; z-index: 100;
    background: var(--navy);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 28px; height: 58px;
    box-shadow: 0 2px 20px rgba(0,0,0,.25);
}
.topnav-brand {
    font-size: 15px; font-weight: 800; color: #fff;
    display: flex; align-items: center; gap: 10px;
}
.topnav-brand .dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--gold); box-shadow: 0 0 8px var(--gold);
}
.topnav-links { display: flex; align-items: center; gap: 2px; list-style: none; }
.topnav-links a {
    display: block; padding: 8px 13px;
    color: rgba(255,255,255,.7); text-decoration: none;
    font-size: 13px; font-weight: 600; border-radius: 8px;
    transition: all .18s;
}
.topnav-links a:hover, .topnav-links a.active {
    color: #fff; background: rgba(255,255,255,.12);
}
.logout-btn-nav {
    padding: 7px 16px !important;
    background: var(--gold) !important; color: var(--navy) !important;
    font-weight: 800 !important; border-radius: 8px !important; margin-left: 6px;
}
.logout-btn-nav:hover { opacity: .88 !important; }

/* Mobile Toggle */
.nav-toggle {
    display: none; font-size: 24px; color: white;
    cursor: pointer; padding: 8px; background: none; border: none;
}
.nav-toggle:hover { background: rgba(255,255,255,0.1); border-radius: 6px; }

/* ══ PAGE LAYOUT ══ */
.page { padding: 28px 32px; max-width: 1300px; margin: 0 auto; animation: fadeUp .4s ease both; }
.section-heading {
    font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 22px;
    display: flex; align-items: center; gap: 10px;
}
.section-heading::before {
    content: ''; width: 5px; height: 24px;
    background: var(--blue); border-radius: 3px;
}
.card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 16px; box-shadow: var(--shadow-sm);
}
.card-head {
    display: flex; align-items: center; gap: 9px;
    padding: 16px 20px; border-bottom: 1.5px solid var(--border);
    font-size: 14px; font-weight: 700; color: var(--navy);
}
.card-head .chip {
    width: 28px; height: 28px; border-radius: 8px;
    background: var(--blue-lt); color: var(--blue);
    display: flex; align-items: center; justify-content: center; font-size: 14px;
}
.card-body { padding: 20px; }
.home-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.stat-row { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }
.stat-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px; background: var(--bg);
    border-radius: 10px; border: 1.5px solid var(--border);
}
.stat-item .label { font-size: 13px; font-weight: 600; color: var(--muted); }
.stat-item .val { font-size: 20px; font-weight: 800; color: var(--navy); font-family: 'JetBrains Mono', monospace; }
.chart-wrap { height: 220px; position: relative; }
.announce-form textarea {
    width: 100%; min-height: 80px; border: 1.5px solid var(--border);
    border-radius: 10px; padding: 10px 14px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; color: var(--text); resize: vertical; outline: none;
    transition: border-color .18s; background: var(--bg);
}
.announce-form textarea:focus { border-color: var(--blue); }
.btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 20px; border-radius: 9px; border: none;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; font-weight: 700; cursor: pointer; transition: all .18s;
}
.btn-primary { background: var(--blue); color: #fff; }
.btn-primary:hover { background: #1347c0; transform: translateY(-1px); }
.btn-danger { background: var(--red); color: #fff; }
.btn-danger:hover { background: #c01d1d; }
.btn-success { background: var(--green); color: #fff; }
.btn-success:hover { background: #046343; }
.btn-secondary { background: var(--bg); color: var(--muted); border: 1.5px solid var(--border); }
.btn-secondary:hover { background: var(--border); }
.posted-list { display: flex; flex-direction: column; gap: 0; }
.posted-item { padding: 13px 0; border-bottom: 1.5px solid var(--border); }
.posted-item:last-child { border-bottom: none; }
.posted-meta { font-size: 11px; font-weight: 700; color: var(--blue); font-family: 'JetBrains Mono', monospace; margin-bottom: 5px; }
.posted-text { font-size: 13px; color: var(--muted); line-height: 1.55; }

/* ══ RESPONSIVE ══ */
@keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }

@media (max-width: 1100px) {
    .nav-toggle { display: block; }
    .topnav-links {
        display: none; position: absolute; top: 58px; left: 0; right: 0;
        background: var(--navy); flex-direction: column;
        padding: 10px 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); gap: 5px;
    }
    .topnav-links.show { display: flex; }
    .topnav-links a { width: 100%; padding: 12px; text-align: center; }
    .logout-btn-nav { margin: 10px 0 0 0; width: 100%; text-align: center; }
    .home-grid { grid-template-columns: 1fr; }
    .page { padding: 16px; }
}
</style>
</head>
<body>
<!-- ══ TOPNAV ══ -->
<nav class="topnav">
    <div class="topnav-brand">
        <div class="dot"></div>
        College of Computer Studies Admin
    </div>
    
    <!-- Mobile Toggle Button -->
    <button class="nav-toggle" onclick="toggleNav()">
        <i class="bi bi-list"></i>
    </button>
    
    <ul class="topnav-links" id="topnavLinks">
        <li><a href="dashboard.php" class="active">Home</a></li>
        <li><a href="search.php">Search</a></li>
        <li><a href="students.php">Students</a></li>
        <li><a href="sitin.php">Sit-in</a></li>
        <li><a href="sitin_records.php">Sit-in Records</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="reservation.php">Reservation</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="add_reward.php">Add Reward</a></li>
        <li><a href="/ccs_sitin/logout.php" class="logout-btn-nav">Log out</a></li>
    </ul>
</nav>

<!-- ══ HOME ══ -->
<div class="page">
    <div class="section-heading">Dashboard Overview</div>
    <div class="home-grid">
        <div class="card">
            <div class="card-head"><span class="chip">📊</span> Statistics</div>
            <div class="card-body">
                <div class="stat-row">
                    <div class="stat-item">
                        <span class="label">Students Registered</span>
                        <span class="val"><?php echo $total_students; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="label">Currently Sit-in</span>
                        <span class="val"><?php echo $active_sitin; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="label">Total Sit-in</span>
                        <span class="val"><?php echo $total_sitin; ?></span>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="purposeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="card">
    <div class="card-head"><span class="chip">📢</span> Announcement</div>
    <div class="card-body">
        <div class="announce-form">
            <textarea id="announceText" placeholder="New Announcement..."></textarea>
            <div style="margin-top:10px">
                <button class="btn btn-success" onclick="postAnnouncement()">
                    <i class="bi bi-send me-1"></i> Submit
                </button>
            </div>
        </div>
        <div style="margin-top:20px;font-size:15px;font-weight:800;color:var(--navy);margin-bottom:12px">Posted Announcements</div>
        <div class="posted-list" id="postedList">
            <?php
            // Fetch existing announcements from database
            $announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY date DESC LIMIT 10");
            while ($ann = mysqli_fetch_assoc($announcements)):
            ?>
            <div class="posted-item">
                <div class="posted-meta">
                    <?= htmlspecialchars($ann['created_by'] ?? 'CCS Admin') ?> | 
                    <?= date('Y-M-d', strtotime($ann['date'])) ?>
                </div>
                <div class="posted-text"><?= nl2br(htmlspecialchars($ann['message'])) ?></div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<script>
// Mobile Toggle
function toggleNav() {
    document.getElementById('topnavLinks').classList.toggle('show');
}

// Chart
window.addEventListener('load', () => {
    const ctx = document.getElementById('purposeChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['C#','C','Java','ASP.Net','PHP'],
            datasets: [{ data:[4,3,3,2,3], backgroundColor:['#6366f1','#ef4444','#f59e0b','#10b981','#3b82f6'], borderWidth:0 }]
        },
        options: { plugins:{ legend:{ position:'top', labels:{ font:{ family:'Plus Jakarta Sans', size:11 }, boxWidth:12 } } }, animation:{ duration:800 } }
    });
});

// Announcement
function postAnnouncement() {
    const text = document.getElementById('announceText').value.trim();
    if (!text) return;
    const now = new Date();
    const dateStr = now.toISOString().slice(0, 10);
    const div = document.createElement('div');
    div.className = 'posted-item';
    div.innerHTML = `<div class="posted-meta">CCS Admin | ${dateStr}</div><div class="posted-text">${text}</div>`;
    document.getElementById('postedList').prepend(div);
    document.getElementById('announceText').value = '';
}
</script>
</body>
</html>