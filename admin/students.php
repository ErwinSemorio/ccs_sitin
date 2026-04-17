<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Fetch all students
$result = mysqli_query($conn, "SELECT * FROM students ORDER BY last_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Students | CCS Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
/* ══ DESIGN SYSTEM (Space Theme) ══ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
--navy: #0f2044; --blue: #1a56db; --blue-lt: #e8f0fe;
--green: #057a55; --green-lt: #def7ec; --amber: #d97706; --amber-lt: #fef3c7;
--red: #e02424; --gold: #f59e0b;
--bg: #f1f5fb; --surface: #ffffff; --border: #e2e8f4;
--text: #0f2044; --muted: #64748b;
--shadow: 0 4px 20px rgba(15,32,68,.10);
}
body {
font-family: 'Plus Jakarta Sans', sans-serif;
background: var(--bg);
color: var(--text);
min-height: 100vh;
}
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
/* ══ PAGE LAYOUT ══ */
.page { padding: 28px 32px; max-width: 1400px; margin: 0 auto; animation: fadeUp .4s ease both; }
.section-heading {
font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 22px;
display: flex; align-items: center; gap: 10px;
}
.section-heading::before {
content: ''; width: 5px; height: 24px;
background: var(--blue); border-radius: 3px;
}
/* ══ CARD ══ */
.card {
background: var(--surface);
border: 1.5px solid var(--border);
border-radius: 16px; box-shadow: var(--shadow); overflow: hidden;
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
/* ══ TABLE TOOLBAR ══ */
.table-toolbar {
display: flex; align-items: center; justify-content: space-between;
padding: 14px 20px; border-bottom: 1.5px solid var(--border); gap: 12px; flex-wrap: wrap;
}
.search-input {
padding: 7px 14px; border: 1.5px solid var(--border); border-radius: 9px;
font-family: 'Plus Jakarta Sans', sans-serif;
font-size: 13px; color: var(--text); background: var(--bg);
outline: none; transition: border-color .18s; width: 220px;
}
.search-input:focus { border-color: var(--blue); }
/* ══ TABLE STYLES ══ */
table { width: 100%; border-collapse: collapse; }
thead th {
padding: 11px 16px; text-align: left;
font-size: 11px; font-weight: 700; color: var(--muted);
text-transform: uppercase; letter-spacing: 1px;
background: #f8fafc; border-bottom: 1.5px solid var(--border);
}
tbody tr { transition: background .13s; }
tbody tr:hover { background: #f7f9ff; }
tbody td {
padding: 13px 16px; font-size: 13px; font-weight: 500;
border-bottom: 1.5px solid var(--border); color: var(--text);
}
tbody tr:last-child td { border-bottom: none; }
.td-mono { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--muted); }
/* ══ BADGES ══ */
.badge {
display: inline-flex; align-items: center; gap: 5px;
padding: 3px 10px; border-radius: 20px;
font-size: 11px; font-weight: 700;
}
.badge-blue { background: var(--blue-lt); color: var(--blue); }
@keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
@media (max-width: 1100px) {
.topnav-links { display: none; }
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
<ul class="topnav-links">
<li><a href="/ccs_sitin/admin/dashboard.php">Home</a></li>
<li><a href="/ccs_sitin/admin/search.php">Search</a></li>
<li><a href="/ccs_sitin/admin/students.php" class="active">Students</a></li>
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

<!-- ══ CONTENT ══ -->
<div class="page">
<div class="section-heading">Students Information</div>
<div class="card">
<div class="card-head">
<span class="chip">👥</span>
Registered Students
</div>
<div class="table-toolbar">
<div class="table-toolbar-left">
<label style="font-size:12px;font-weight:600;color:var(--muted);display:flex;align-items:center;gap:6px">
<span style="font-weight:700;color:var(--text)">Total: <?php echo mysqli_num_rows($result); ?></span> students
</label>
</div>
<div style="display:flex;align-items:center;gap:8px">
<span style="font-size:12px;font-weight:600;color:var(--muted)">Search:</span>
<input class="search-input" id="searchInput" placeholder="Type ID or name..." onkeyup="filterStudentsTable()"/>
</div>
</div>
<div style="overflow-x:auto">
<table id="studentsTable">
<thead>
<tr>
<th>#</th>
<th>ID Number</th>
<th>Full Name</th>
<th>Course</th>
<th>Email</th>
<th>Address</th>
<th>Registered</th>
</tr>
</thead>
<tbody id="studentsTbody">
<?php
$count = 1;
if (mysqli_num_rows($result) > 0):
while ($row = mysqli_fetch_assoc($result)):
?>
<tr>
<td class="td-mono"><?php echo $count++; ?></td>
<td class="td-mono"><?php echo htmlspecialchars($row['id_number']); ?></td>
<td><strong><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name']); ?></strong></td>
<td><span class="badge badge-blue"><?php echo htmlspecialchars($row['course']); ?></span></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['address']); ?></td>
<td class="td-mono"><?php echo isset($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'N/A'; ?></td>
</tr>
<?php
endwhile;
else:
?>
<tr><td colspan="7" style="text-align:center; padding: 30px; color: var(--muted);">No students registered yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>

<script>
// Filter students table
function filterStudentsTable() {
const q = document.getElementById('searchInput').value.toLowerCase();
const rows = document.querySelectorAll('#studentsTbody tr');
let count = 0;
rows.forEach(row => {
const text = row.innerText.toLowerCase();
const show = text.includes(q);
row.style.display = show ? '' : 'none';
if (show) count++;
});
}
</script>
</body>
</html>