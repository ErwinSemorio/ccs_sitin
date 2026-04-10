<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reservation | CCS Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --navy: #0f2044; --blue: #1a56db; --blue-lt: #e8f0fe;
    --green: #057a55; --green-lt: #def7ec; --amber: #d97706; --amber-lt: #fef3c7;
    --red: #e02424; --gold: #f59e0b;
    --bg: #f1f5fb; --surface: #ffffff; --border: #e2e8f4;
    --text: #0f2044; --muted: #64748b;
    --shadow: 0 4px 20px rgba(15,32,68,.10);
}
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
.topnav { position: sticky; top: 0; z-index: 100; background: var(--navy); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; height: 58px; box-shadow: 0 2px 20px rgba(0,0,0,.25); }
.topnav-brand { font-size: 15px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; }
.topnav-brand .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 8px var(--gold); }
.topnav-links { display: flex; align-items: center; gap: 2px; list-style: none; }
.topnav-links a { display: block; padding: 8px 13px; color: rgba(255,255,255,.7); text-decoration: none; font-size: 13px; font-weight: 600; border-radius: 8px; transition: all .18s; }
.topnav-links a:hover, .topnav-links a.active { color: #fff; background: rgba(255,255,255,.12); }
.logout-btn-nav { padding: 7px 16px !important; background: var(--gold) !important; color: var(--navy) !important; font-weight: 800 !important; border-radius: 8px !important; margin-left: 6px; }
.nav-toggle { display: none; font-size: 24px; color: white; cursor: pointer; padding: 8px; background: none; border: none; }
.nav-toggle:hover { background: rgba(255,255,255,0.1); border-radius: 6px; }
.page { padding: 28px 32px; max-width: 1400px; margin: 0 auto; animation: fadeUp .4s ease both; }
.section-heading { font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 22px; display: flex; align-items: center; gap: 10px; }
.section-heading::before { content: ''; width: 5px; height: 24px; background: var(--blue); border-radius: 3px; }
.card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
.card-head { padding: 16px 20px; border-bottom: 1px solid var(--border); font-weight: 700; font-size: 15px; color: var(--navy); display: flex; justify-content: space-between; align-items: center; }
.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { text-align: left; padding: 12px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); background: #f8fafc; border-bottom: 1px solid var(--border); }
td { padding: 14px 20px; font-size: 13px; border-bottom: 1px solid var(--border); vertical-align: middle; }
tr:hover td { background: #fafbfc; }
.search-bar { padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; width: 250px; font-family: inherit; outline: none; }
.search-bar:focus { border-color: var(--blue); }
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
        <li><a href="reservation.php" class="active">Reservation</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="add_reward.php">Add Reward</a></li>
        <li><a href="/ccs_sitin/logout.php" class="logout-btn-nav">Log out</a></li>
    </ul>
</nav>

<div class="page">
    <div class="section-heading">Reservations</div>
    <div class="card">
        <div class="card-head">
            <span>📅 Lab Reservations</span>
            <input type="text" class="search-bar" placeholder="Search..." onkeyup="filterTable()">
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Reservation ID</th><th>ID Number</th><th>Name</th><th>Lab</th><th>Date</th><th>Time</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <tr><td colspan="8" style="text-align:center; padding:40px; color:var(--muted);">No reservations available</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleNav() { document.getElementById('topnavLinks').classList.toggle('show'); }
function filterTable() {
    const input = document.querySelector('.search-bar').value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
    });
}
</script>
</body>
</html>