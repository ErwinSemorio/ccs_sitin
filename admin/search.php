<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: /ccs_sitin/login.php"); exit(); }
include __DIR__ . "/../config/database.php";
$students = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM students ORDER BY last_name ASC"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search | CCS Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
/* Copy the SAME CSS from sitin.php here */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root { --navy: #0f2044; --blue: #1a56db; --blue-lt: #e8f0fe; --green: #057a55; --green-lt: #def7ec; --amber: #d97706; --amber-lt: #fef3c7; --red: #e02424; --gold: #f59e0b; --bg: #f1f5fb; --surface: #ffffff; --border: #e2e8f4; --text: #0f2044; --muted: #64748b; --shadow: 0 4px 20px rgba(15,32,68,.10); }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
.topnav { position: sticky; top: 0; z-index: 100; background: var(--navy); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; height: 58px; box-shadow: 0 2px 20px rgba(0,0,0,.25); }
.topnav-brand { font-size: 15px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; }
.topnav-brand .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 8px var(--gold); }
.topnav-links { display: flex; align-items: center; gap: 2px; list-style: none; }
.topnav-links a { display: block; padding: 8px 13px; color: rgba(255,255,255,.7); text-decoration: none; font-size: 13px; font-weight: 600; border-radius: 8px; transition: all .18s; }
.topnav-links a:hover, .topnav-links a.active { color: #fff; background: rgba(255,255,255,.12); }
.logout-btn-nav { padding: 7px 16px !important; background: var(--gold) !important; color: var(--navy) !important; font-weight: 800 !important; border-radius: 8px !important; margin-left: 6px; }
.page { padding: 28px 32px; max-width: 1000px; margin: 0 auto; animation: fadeUp .4s ease both; }
.section-heading { font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 22px; display: flex; align-items: center; gap: 10px; }
.section-heading::before { content: ''; width: 5px; height: 24px; background: var(--blue); border-radius: 3px; }
.card { background: var(--surface); border: 1.5px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
.card-head { display: flex; align-items: center; gap: 9px; padding: 16px 20px; border-bottom: 1.5px solid var(--border); font-size: 14px; font-weight: 700; color: var(--navy); }
.card-head .chip { width: 28px; height: 28px; border-radius: 8px; background: var(--blue-lt); color: var(--blue); display: flex; align-items: center; justify-content: center; font-size: 14px; }
.search-input { padding: 7px 14px; border: 1.5px solid var(--border); border-radius: 9px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; color: var(--text); background: var(--bg); outline: none; transition: border-color .18s; width: 100%; }
.search-input:focus { border-color: var(--blue); }
.result-item { padding: 14px; border: 1px solid var(--border); border-radius: 10px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; transition: all .2s; }
.result-item:hover { border-color: var(--blue); background: var(--blue-lt); }
.btn { padding: 9px 20px; border-radius: 9px; border: none; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .18s; }
.btn-primary { background: var(--blue); color: #fff; }
.btn-primary:hover { background: #1347c0; transform: translateY(-1px); }
@keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
@media (max-width: 1100px) { .topnav-links { display: none; } .page { padding: 16px; } }
</style>
</head>
<body>
<nav class="topnav">
    <div class="topnav-brand"><div class="dot"></div>College of Computer Studies Admin</div>
    <ul class="topnav-links">
        <li><a href="/ccs_sitin/admin/dashboard.php">Home</a></li>
        <li><a href="/ccs_sitin/admin/search.php" class="active">Search</a></li>
        <li><a href="/ccs_sitin/admin/students.php">Students</a></li>
        <li><a href="/ccs_sitin/admin/sitin.php">Sit-in</a></li>
        <li><a href="/ccs_sitin/admin/sitin_records.php">Sit-in Records</a></li>
        <li><a href="/ccs_sitin/admin/reports.php">Reports</a></li>
        <li><a href="/ccs_sitin/admin/feedback.php">Feedback</a></li>
        <li><a href="/ccs_sitin/admin/reservation.php">Reservation</a></li>
        <li><a href="/ccs_sitin/admin/leaderboard.php">Leaderboard</a></li>
        <li><a href="/ccs_sitin/admin/add_reward.php">Add Reward</a></li>
        <li><a href="/ccs_sitin/logout.php" class="logout-btn-nav">Log out</a></li>
    </ul>
</nav>

<div class="page">
    <div class="section-heading">Search Student</div>
    <div class="card" style="max-width:600px">
        <div class="card-head"><span class="chip">🔍</span> Find a Student</div>
        <div class="card-body">
            <div style="display:flex;gap:10px;">
                <input type="text" class="search-input" id="searchInput" placeholder="Enter ID number or name..." oninput="filterResults()">
            </div>
            <div id="results" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
const students = <?php echo json_encode($students); ?>;
function filterResults() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const div = document.getElementById('results');
    if (!q) { div.innerHTML = ''; return; }
    const res = students.filter(s => (s.first_name + ' ' + s.last_name).toLowerCase().includes(q) || s.id_number.includes(q));
    div.innerHTML = res.length ? res.map(s => `
        <div class="result-item">
            <div class="result-info">
                <h6>${s.first_name} ${s.last_name}</h6>
                <small>${s.id_number} - ${s.course}</small>
            </div>
            <a href="sitin.php?id=${s.id_number}" class="btn btn-primary">Sit In</a>
        </div>
    `).join('') : '<p class="text-muted" style="text-align:center;padding:20px;">No students found.</p>';
}
</script>
</body>
</html>