<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Fetch active sit-ins
$active = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM sitin_records WHERE status='Active' ORDER BY time_in DESC"), MYSQLI_ASSOC);
// Fetch students for autocomplete
$students = mysqli_fetch_all(mysqli_query($conn, "SELECT id_number, first_name, last_name FROM students"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sit-in | CCS Admin</title>
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
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
.topnav { position: sticky; top: 0; z-index: 100; background: var(--navy); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; height: 58px; box-shadow: 0 2px 20px rgba(0,0,0,.25); }
.topnav-brand { font-size: 15px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; }
.topnav-brand .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 8px var(--gold); }
.topnav-links { display: flex; align-items: center; gap: 2px; list-style: none; }
.topnav-links a { display: block; padding: 8px 13px; color: rgba(255,255,255,.7); text-decoration: none; font-size: 13px; font-weight: 600; border-radius: 8px; transition: all .18s; }
.topnav-links a:hover, .topnav-links a.active { color: #fff; background: rgba(255,255,255,.12); }
.logout-btn-nav { padding: 7px 16px !important; background: var(--gold) !important; color: var(--navy) !important; font-weight: 800 !important; border-radius: 8px !important; margin-left: 6px; }
.page { padding: 28px 32px; max-width: 1400px; margin: 0 auto; animation: fadeUp .4s ease both; }
.section-heading { font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 22px; display: flex; align-items: center; gap: 10px; }
.section-heading::before { content: ''; width: 5px; height: 24px; background: var(--blue); border-radius: 3px; }
.card { background: var(--surface); border: 1.5px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
.card-head { display: flex; align-items: center; gap: 9px; padding: 16px 20px; border-bottom: 1.5px solid var(--border); font-size: 14px; font-weight: 700; color: var(--navy); }
.card-head .chip { width: 28px; height: 28px; border-radius: 8px; background: var(--blue-lt); color: var(--blue); display: flex; align-items: center; justify-content: center; font-size: 14px; }
.table-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1.5px solid var(--border); gap: 12px; flex-wrap: wrap; }
.search-input { padding: 7px 14px; border: 1.5px solid var(--border); border-radius: 9px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; color: var(--text); background: var(--bg); outline: none; transition: border-color .18s; width: 220px; }
.search-input:focus { border-color: var(--blue); }
table { width: 100%; border-collapse: collapse; }
thead th { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; background: #f8fafc; border-bottom: 1.5px solid var(--border); }
tbody tr { transition: background .13s; }
tbody tr:hover { background: #f7f9ff; }
tbody td { padding: 13px 16px; font-size: 13px; font-weight: 500; border-bottom: 1.5px solid var(--border); color: var(--text); }
tbody tr:last-child td { border-bottom: none; }
.td-mono { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--muted); }
.badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-active { background: var(--amber-lt); color: var(--amber); }
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px; border-radius: 9px; border: none; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .18s; }
.btn-primary { background: var(--blue); color: #fff; }
.btn-primary:hover { background: #1347c0; transform: translateY(-1px); }
.btn-sm { padding: 5px 13px; font-size: 12px; border-radius: 7px; border: none; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; cursor: pointer; transition: all .15s; }
.btn-sm-teal { background: #0694a2; color: #fff; }
.btn-sm-teal:hover { background: #047481; }
.no-data { text-align: center; padding: 36px; color: var(--muted); font-size: 13px; font-weight: 600; }
@keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
@media (max-width: 1100px) { .topnav-links { display: none; } .page { padding: 16px; } }
</style>
</head>
<body>
<nav class="topnav">
    <div class="topnav-brand"><div class="dot"></div>College of Computer Studies Admin</div>
    <ul class="topnav-links">
        <li><a href="/ccs_sitin/admin/dashboard.php">Home</a></li>
        <li><a href="/ccs_sitin/admin/search.php">Search</a></li>
        <li><a href="/ccs_sitin/admin/students.php">Students</a></li>
        <li><a href="/ccs_sitin/admin/sitin.php" class="active">Sit-in</a></li>
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
    <div class="section-heading">Current Sit-in</div>
    <div class="card">
        <div class="card-head">
            <span class="chip">🖥️</span> Active Sessions
            <button class="btn btn-primary" onclick="openModal()"><i class="bi bi-plus-lg"></i> New Sit-in</button>
        </div>
        <div class="table-container" style="overflow-x:auto;">
            <table>
                <thead>
                    <tr><th>ID Number</th><th>Name</th><th>Purpose</th><th>Lab</th><th>Time In</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if (count($active) > 0): ?>
                        <?php foreach ($active as $a): ?>
                        <tr>
                            <td class="td-mono"><?php echo htmlspecialchars($a['id_number']); ?></td>
                            <td><strong><?php echo htmlspecialchars($a['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($a['purpose']); ?></td>
                            <td>Lab <?php echo htmlspecialchars($a['lab']); ?></td>
                            <td class="td-mono"><?php echo htmlspecialchars($a['time_in']); ?></td>
                            <td><span class="badge badge-active">Active</span></td>
                            <td>
                                <form action="/ccs_sitin/process/timeout_process.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="sit_id" value="<?php echo $a['id']; ?>">
                                    <button type="submit" class="btn-sm btn-sm-teal">Time Out</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="no-data">No active sit-ins</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="sitInModal" style="display:none; position:fixed; inset:0; background:rgba(15,32,68,0.45); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:white; border-radius:18px; box-shadow:0 20px 60px rgba(15,32,68,0.25); width:100%; max-width:460px; overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:var(--navy); color:#fff;">
            <h3 style="font-size:15px; font-weight:800;">🖥️ Sit In Form</h3>
            <button onclick="closeModal()" style="width:28px; height:28px; border-radius:7px; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:16px; cursor:pointer;">✕</button>
        </div>
        <form action="/ccs_sitin/process/sitin_process.php" method="POST" style="padding:22px; display:flex; flex-direction:column; gap:14px;">
            <div>
                <label style="display:block; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">ID Number</label>
                <input name="id_number" list="studentList" required style="width:100%; padding:9px 14px; border:1.5px solid var(--border); border-radius:9px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:var(--text); background:var(--bg); outline:none;">
                <datalist id="studentList">
                    <?php foreach($students as $s): ?><option value="<?php echo $s['id_number']; ?>"><?php endforeach; ?>
                </datalist>
            </div>
            <div>
                <label style="display:block; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Purpose</label>
                <select name="purpose" required style="width:100%; padding:9px 14px; border:1.5px solid var(--border); border-radius:9px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:var(--text); background:var(--bg); outline:none;">
                    <option value="">Select purpose...</option>
                    <option>C Programming</option><option>Java</option><option>Web Development</option><option>Database Systems</option><option>ASP.Net</option><option>PHP</option><option>C#</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Lab</label>
                <select name="lab" required style="width:100%; padding:9px 14px; border:1.5px solid var(--border); border-radius:9px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:var(--text); background:var(--bg); outline:none;">
                    <option value="">Select lab...</option>
                    <option>521</option><option>522</option><option>523</option><option>524</option><option>525</option><option>526</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; padding-top:10px; border-top:1.5px solid var(--border); margin-top:10px;">
                <button type="button" onclick="closeModal()" style="padding:9px 20px; border-radius:9px; border:none; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; background:var(--bg); color:var(--muted);">Close</button>
                <button type="submit" style="padding:9px 20px; border-radius:9px; border:none; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; background:var(--blue); color:#fff;">Sit In</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('sitInModal').style.display = 'flex'; }
function closeModal() { document.getElementById('sitInModal').style.display = 'none'; }
</script>
</body>
</html>