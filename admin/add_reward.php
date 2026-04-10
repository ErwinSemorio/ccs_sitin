<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Query with Error Check
$sql = "SELECT id_number, first_name, last_name, course, points FROM students ORDER BY last_name ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($conn) . "<br><br>Did you run the ALTER TABLE command to add the 'points' column?");
}

$students = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Reward/Points | CCS Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
/* [Keep your existing styles here] */
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
.card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); padding: 30px; margin-bottom: 20px; }
.card-head { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--navy); }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
.form-control, .form-select { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 14px; outline: none; }
.btn { padding: 10px 20px; border-radius: 10px; border: none; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 8px; }
.btn-success { background: var(--green); color: #fff; }
.student-card { border: 1.5px solid var(--border); border-radius: 12px; padding: 15px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; transition: all .2s; }
.student-info h6 { margin: 0; font-weight: 700; color: var(--navy); }
.student-info small { color: var(--muted); font-size: 12px; }
.points-display { background: var(--amber-lt); color: var(--amber); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 14px; }
.search-bar { padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; width: 300px; font-family: inherit; outline: none; margin-bottom: 20px; }
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
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="add_reward.php" class="active">Add Reward</a></li>
        <li><a href="/ccs_sitin/logout.php" class="logout-btn-nav">Log out</a></li>
    </ul>
</nav>

<div class="page">
    <div class="section-heading">⭐ Add Reward/Points</div>
    
    <div class="card">
        <h5 class="card-head">Award Points to Student</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select Student</label>
                    <input type="text" class="form-control" id="studentSearch" placeholder="Search by ID or name..." oninput="filterStudents()">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Points to Add</label>
                    <input type="number" class="form-control" id="pointsInput" placeholder="e.g. 10" min="1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Reason</label>
                    <select class="form-select" id="reasonSelect">
                        <option value="">Select reason...</option>
                        <option value="perfect_attendance">Perfect Attendance (+50)</option>
                        <option value="completed_session">Completed Session (+10)</option>
                        <option value="excellent_work">Excellent Work (+20)</option>
                        <option value="custom">Custom Points</option>
                    </select>
                </div>
            </div>
        </div>
        <button class="btn btn-success" onclick="addPoints()"><i class="bi bi-plus-circle"></i> Add Points</button>
    </div>

    <div class="card">
        <h5 class="card-head">Student List</h5>
        <input type="text" class="search-bar" placeholder="Search students..." oninput="filterStudents()">
        <div id="studentList">
            <?php foreach ($students as $s): ?>
            <div class="student-card" data-id="<?php echo $s['id_number']; ?>">
                <div class="student-info">
                    <h6><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></h6>
                    <small><?php echo htmlspecialchars($s['id_number']); ?> • <?php echo htmlspecialchars($s['course']); ?></small>
                </div>
                <div class="points-display">
                    <i class="bi bi-star-fill"></i> <?php echo htmlspecialchars($s['points']); ?> pts
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function toggleNav() { document.getElementById('topnavLinks').classList.toggle('show'); }
const students = <?php echo json_encode($students); ?>;

function filterStudents() {
    const q = document.querySelector('.search-bar').value.toLowerCase();
    document.querySelectorAll('.student-card').forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}

document.getElementById('reasonSelect').addEventListener('change', function() {
    const pointsMap = {
        'perfect_attendance': 50,
        'completed_session': 10,
        'excellent_work': 20
    };
    if (pointsMap[this.value]) {
        document.getElementById('pointsInput').value = pointsMap[this.value];
    }
});

function addPoints() {
    const studentCard = document.querySelector('.student-card:hover');
    const studentId = studentCard?.dataset.id;
    const points = document.getElementById('pointsInput').value;
    
    if (!studentId || !points) {
        alert('Please select a student and enter points');
        return;
    }
    
    // Note: You need to create /process/add_points.php for this to actually save
    fetch('/ccs_sitin/process/add_points.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_number=${studentId}&points=${points}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(`Successfully added ${points} points!`);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>
</body>
</html>