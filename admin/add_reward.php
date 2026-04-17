<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$students = mysqli_fetch_all(mysqli_query($conn, "SELECT id_number, first_name, last_name, course, points FROM students ORDER BY last_name ASC"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Reward | CCS Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
/* Same CSS as above */
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
.card { background: var(--surface); border: 1.5px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); padding: 30px; margin-bottom: 20px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
.form-control, .form-select { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 14px; outline: none; }
.form-control:focus, .form-select:focus { border-color: var(--blue); }
.btn { padding: 10px 20px; border-radius: 10px; border: none; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 8px; }
.btn-success { background: var(--green); color: #fff; }
.btn-success:hover { background: #046343; }
.student-card { border: 1.5px solid var(--border); border-radius: 12px; padding: 15px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; transition: all .2s; }
.student-card:hover { border-color: var(--blue); background: var(--blue-lt); }
.student-info h6 { margin: 0; font-weight: 700; color: var(--navy); }
.student-info small { color: var(--muted); font-size: 12px; }
.points-display { background: var(--amber-lt); color: var(--amber); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 14px; }
.search-bar { padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; width: 300px; font-family: inherit; outline: none; margin-bottom: 20px; }
.search-bar:focus { border-color: var(--blue); }
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
        <li><a href="/ccs_sitin/admin/sitin.php">Sit-in</a></li>
        <li><a href="/ccs_sitin/admin/sitin_records.php">Sit-in Records</a></li>
        <li><a href="/ccs_sitin/admin/reports.php">Reports</a></li>
        <li><a href="/ccs_sitin/admin/feedback.php">Feedback</a></li>
        <li><a href="/ccs_sitin/admin/reservation.php">Reservation</a></li>
        <li><a href="/ccs_sitin/admin/leaderboard.php">Leaderboard</a></li>
        <li><a href="/ccs_sitin/admin/add_reward.php" class="active">Add Reward</a></li>
        <li><a href="/ccs_sitin/logout.php" class="logout-btn-nav">Log out</a></li>
    </ul>
</nav>

<div class="page">
    <div class="section-heading">⭐ Add Reward/Points</div>
    
    <div class="card">
        <h5 class="fw-bold mb-4" style="margin-top:0;">Award Points to Student</h5>
        <form action="/ccs_sitin/process/add_points.php" method="POST">
            <div class="row" style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Select Student</label>
                    <select name="id_number" class="form-select" required>
                        <option value="">Choose a student...</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?php echo $s['id_number']; ?>"><?php echo htmlspecialchars($s['last_name'] . ', ' . $s['first_name']); ?> (<?php echo $s['points']; ?> pts)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Points to Add</label>
                    <input type="number" name="points" class="form-control" placeholder="e.g. 10" min="1" required>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <select name="reason" class="form-select">
                        <option value="completed_session">Completed Session (+10)</option>
                        <option value="perfect_attendance">Perfect Attendance (+50)</option>
                        <option value="excellent_work">Excellent Work (+20)</option>
                        <option value="custom">Custom Points</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Points</button>
        </form>
    </div>

    <div class="card">
        <h5 class="fw-bold mb-3" style="margin-top:0;">Student List</h5>
        <input type="text" class="search-bar" placeholder="Search students..." oninput="filterStudents()">
        <div id="studentList">
            <?php foreach ($students as $s): ?>
            <div class="student-card" data-id="<?php echo $s['id_number']; ?>">
                <div class="student-info">
                    <h6><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></h6>
                    <small><?php echo htmlspecialchars($s['id_number']); ?> • <?php echo htmlspecialchars($s['course']); ?></small>
                </div>
                <div class="points-display">
                    <i class="bi bi-star-fill"></i> <?php echo $s['points']; ?> pts
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function filterStudents() {
    const q = document.querySelector('.search-bar').value.toLowerCase();
    document.querySelectorAll('.student-card').forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>