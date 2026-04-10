<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Fetch active sit-ins
$active = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM sitin_records WHERE status='Active' ORDER BY time_in DESC"), MYSQLI_ASSOC);
$students = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM students"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Current Sit-in | CCS Admin</title>
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
.badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-active { background: var(--amber-lt); color: var(--amber); }
.btn { padding: 9px 20px; border-radius: 9px; border: none; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .18s; }
.btn-primary { background: var(--blue); color: #fff; }
.btn-primary:hover { background: #1347c0; transform: translateY(-1px); }
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
        <li><a href="sitin.php" class="active">Sit-in</a></li>
        <li><a href="sitin_records.php">Sit-in Records</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="reservation.php">Reservation</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="add_reward.php">Add Reward</a></li>
        <li><a href="/ccs_sitin/logout.php" class="logout-btn-nav">Log out</a></li>
    </ul>
</nav>

<div class="page">
    <div class="section-heading">Current Sit-in</div>
    <div class="card">
        <div class="card-head">
            <span>🖥️ Active Sessions</span>
            <button class="btn btn-primary" onclick="openSitInModal()">+ New Sit-in</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>ID Number</th><th>Name</th><th>Purpose</th><th>Lab</th><th>Time In</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if (count($active) > 0): ?>
                        <?php foreach ($active as $a): ?>
                        <tr>
                            <td style="font-family:'JetBrains Mono',monospace; font-size:12px;"><?php echo htmlspecialchars($a['id_number']); ?></td>
                            <td><strong><?php echo htmlspecialchars($a['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($a['purpose']); ?></td>
                            <td>Lab <?php echo htmlspecialchars($a['lab']); ?></td>
                            <td style="font-size:12px;"><?php echo htmlspecialchars($a['time_in']); ?></td>
                            <td><span class="badge badge-active">Active</span></td>
                            <td>
                                <button class="btn btn-primary" onclick="timeOut(<?php echo $a['id']; ?>)">Time Out</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--muted);">No active sit-ins</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sit-In Modal -->
<div id="sitInModal" style="display:none; position:fixed; inset:0; background:rgba(15,32,68,0.45); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:white; border-radius:18px; box-shadow:0 20px 60px rgba(15,32,68,0.25); width:100%; max-width:460px; overflow:hidden; animation:slideUp .25s cubic-bezier(.22,1,.36,1);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:var(--navy); color:#fff;">
            <h3 style="font-size:15px; font-weight:800;">🖥️ Sit In Form</h3>
            <button onclick="closeSitInModal()" style="width:28px; height:28px; border-radius:7px; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:16px; cursor:pointer;">✕</button>
        </div>
        <div style="padding:22px; display:flex; flex-direction:column; gap:14px;">
            <div>
                <label style="display:block; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">ID Number</label>
                <input id="si-id" placeholder="e.g. 2021-00124" oninput="autoFillName()" style="width:100%; padding:9px 14px; border:1.5px solid var(--border); border-radius:9px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:var(--text); background:var(--bg); outline:none;">
            </div>
            <div>
                <label style="display:block; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Student Name</label>
                <input id="si-name" placeholder="Auto-filled" readonly style="width:100%; padding:9px 14px; border:1.5px solid var(--border); border-radius:9px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:var(--text); background:#f1f5fb; outline:none;">
            </div>
            <div>
                <label style="display:block; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Purpose</label>
                <select id="si-purpose" style="width:100%; padding:9px 14px; border:1.5px solid var(--border); border-radius:9px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:var(--text); background:var(--bg); outline:none;">
                    <option value="">Select purpose...</option>
                    <option>C Programming</option><option>Java</option><option>Web Development</option><option>Database Systems</option><option>ASP.Net</option><option>PHP</option><option>C#</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Lab</label>
                <select id="si-lab" style="width:100%; padding:9px 14px; border:1.5px solid var(--border); border-radius:9px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:var(--text); background:var(--bg); outline:none;">
                    <option value="">Select lab...</option><option>521</option><option>522</option><option>523</option><option>524</option><option>525</option><option>526</option>
                </select>
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end; gap:8px; padding:16px 22px; border-top:1.5px solid var(--border);">
            <button onclick="closeSitInModal()" style="padding:9px 20px; border-radius:9px; border:none; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; background:var(--bg); color:var(--muted);">Close</button>
            <button onclick="submitSitIn()" style="padding:9px 20px; border-radius:9px; border:none; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; background:var(--blue); color:#fff;">Sit In</button>
        </div>
    </div>
</div>

<script>
function toggleNav() { document.getElementById('topnavLinks').classList.toggle('show'); }
const students = <?php echo json_encode($students); ?>;

function openSitInModal() { document.getElementById('sitInModal').style.display = 'flex'; }
function closeSitInModal() { document.getElementById('sitInModal').style.display = 'none'; }

function autoFillName() {
    const id = document.getElementById('si-id').value.trim();
    const s = students.find(x => x.id_number === id);
    document.getElementById('si-name').value = s ? s.first_name + ' ' + s.last_name : '';
}

function submitSitIn() {
    const id = document.getElementById('si-id').value.trim();
    const purpose = document.getElementById('si-purpose').value;
    const lab = document.getElementById('si-lab').value;
    if (!id || !purpose || !lab) { alert('Please fill all fields.'); return; }
    
    fetch('/ccs_sitin/process/sitin_process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_number=${id}&purpose=${purpose}&lab=${lab}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { alert('Success!'); location.reload(); }
        else { alert(data.message); }
    });
}

function timeOut(sitId) {
    if (!confirm('Time out this student?')) return;
    fetch('/ccs_sitin/process/timeout_process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `sit_id=${sitId}`
    })
    .then(res => res.json())
    .then(() => location.reload());
}
</script>
</body>
</html>