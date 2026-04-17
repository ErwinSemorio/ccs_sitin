<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
header("Location: /ccs_sitin/login.php");
exit();
}
include __DIR__ . "/../config/database.php";
$records = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM sitin_records ORDER BY date DESC, time_in DESC"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sit-in Records | CCS Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
:root {
--navy: #0f2044; --blue: #1a56db; --blue-lt: #e8f0fe;
--green: #057a55; --green-lt: #def7ec; --amber: #d97706; --amber-lt: #fef3c7;
--bg: #f1f5fb; --surface: #ffffff; --border: #e2e8f4;
--text: #0f2044; --muted: #64748b; --shadow: 0 4px 20px rgba(15,32,68,.10);
}
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; }
.topnav { background: var(--navy); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; height: 58px; box-shadow: 0 2px 20px rgba(0,0,0,.25); }
.topnav-brand { color: #fff; font-weight: 800; font-size: 15px; display: flex; align-items: center; gap: 10px; }
.topnav-brand .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 8px var(--gold); }
.topnav-links { display: flex; align-items: center; gap: 2px; list-style: none; }
.topnav-links a { color: rgba(255,255,255,.7); text-decoration: none; padding: 8px 13px; font-size: 13px; font-weight: 600; border-radius: 8px; transition: .18s; }
.topnav-links a:hover, .topnav-links a.active { color: #fff; background: rgba(255,255,255,.12); }
.btn-logout { background: var(--gold); color: var(--navy); font-weight: 800; border-radius: 8px; padding: 7px 16px; text-decoration: none; font-size: 13px; }
.page { max-width: 1300px; margin: 30px auto; padding: 0 20px; }
.card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
.card-head { padding: 16px 20px; border-bottom: 1px solid var(--border); font-weight: 700; font-size: 15px; color: var(--navy); display: flex; justify-content: space-between; align-items: center; }
.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { text-align: left; padding: 12px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); background: #f8fafc; border-bottom: 1px solid var(--border); }
td { padding: 14px 20px; font-size: 13px; border-bottom: 1px solid var(--border); vertical-align: middle; }
tr:hover td { background: #fafbfc; }
.badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-active { background: var(--amber-lt); color: var(--amber); }
.badge-done { background: var(--green-lt); color: var(--green); }
.search-bar { padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; width: 250px; font-family: inherit; outline: none; }
.search-bar:focus { border-color: var(--blue); }
</style>
</head>
<body>
<nav class="topnav">
<div class="topnav-brand">
<div class="dot"></div>
College of Computer Studies Admin
</div>
<ul class="topnav-links">
<li><a href="/ccs_sitin/admin/dashboard.php">Home</a></li>
<li><a href="/ccs_sitin/admin/search.php">Search</a></li>
<li><a href="/ccs_sitin/admin/students.php">Students</a></li>
<li><a href="/ccs_sitin/admin/sitin.php">Sit-in</a></li>
<li><a href="/ccs_sitin/admin/sitin_records.php" class="active">Sit-in Records</a></li>
<li><a href="/ccs_sitin/admin/reports.php">Reports</a></li>
<li><a href="/ccs_sitin/admin/feedback.php">Feedback</a></li>
<li><a href="/ccs_sitin/admin/reservation.php">Reservation</a></li>
<li><a href="/ccs_sitin/admin/leaderboard.php">Leaderboard</a></li>
<li><a href="/ccs_sitin/admin/add_reward.php">Add Reward</a></li>
<li><a href="/ccs_sitin/logout.php" class="btn-logout">Log out</a></li>
</ul>
</nav>

<div class="page">
<div class="card">
<div class="card-head">
<span>📜 Sit-in History</span>
<input type="text" class="search-bar" id="searchInput" placeholder="Search records..." onkeyup="filterTable()">
</div>
<div class="table-container">
<table id="recordsTable">
<thead>
<tr><th>ID Number</th><th>Name</th><th>Purpose</th><th>Lab</th><th>Date</th><th>Time In</th><th>Time Out</th><th>Status</th></tr>
</thead>
<tbody>
<?php foreach($records as $r): ?>
<tr>
<td style="font-family:'JetBrains Mono',monospace; font-size:12px;"><?php echo htmlspecialchars($r['id_number']); ?></td>
<td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
<td><?php echo htmlspecialchars($r['purpose']); ?></td>
<td>Lab <?php echo htmlspecialchars($r['lab']); ?></td>
<td style="font-size:12px;"><?php echo htmlspecialchars($r['date']); ?></td>
<td style="font-size:12px;"><?php echo htmlspecialchars($r['time_in']); ?></td>
<td style="font-size:12px;"><?php echo $r['time_out'] ? htmlspecialchars($r['time_out']) : '—'; ?></td>
<td><span class="badge <?php echo ($r['status'] === 'Active') ? 'badge-active' : 'badge-done'; ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</div>

<script>
function filterTable() {
const input = document.getElementById('searchInput').value.toLowerCase();
const rows = document.querySelectorAll('#recordsTable tbody tr');
rows.forEach(row => {
row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
});
}
</script>
</body>
</html>