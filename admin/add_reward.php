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
    <!-- Space Theme CSS -->
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1200px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .form-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1.5rem; }
        @media(max-width: 900px) { .form-grid { grid-template-columns: 1fr; } }
        
        .student-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem; border-bottom: 1px solid var(--space-border);
            transition: var(--transition);
        }
        .student-item:hover { background: rgba(0, 212, 255, 0.05); }
        .student-item:last-child { border-bottom: none; }
        .student-name { color: var(--text-primary); font-weight: 700; margin-bottom: 0.2rem; }
        .student-meta { font-size: 0.85rem; color: var(--text-secondary); }
        .points-badge {
            background: rgba(251, 191, 36, 0.15); color: var(--accent-gold);
            padding: 0.4rem 0.8rem; border-radius: 99px; font-weight: 700;
            display: flex; align-items: center; gap: 0.4rem; font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Space Theme Navbar -->
    <nav class="navbar-space">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Admin</div>
            <div class="nav-links-space">
                <a href="/ccs_sitin/admin/dashboard.php" class="nav-link-space">Home</a>
                <a href="/ccs_sitin/admin/search.php" class="nav-link-space">Search</a>
                <a href="/ccs_sitin/admin/students.php" class="nav-link-space">Students</a>
                <a href="/ccs_sitin/admin/sitin.php" class="nav-link-space">Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Records</a>
                <a href="/ccs_sitin/admin/reports.php" class="nav-link-space">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php" class="nav-link-space">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/admin/add_reward.php" class="nav-link-space active">Add Reward</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">⭐ Add Reward/Points</h2>
        
        <!-- Add Points Form -->
        <div class="glass-card fade-in-space">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem; font-size: 1.1rem;">
                    <i class="bi bi-gift" style="color: var(--accent-cyan);"></i> Award Points to Student
                </h3>
            </div>
            <form action="/ccs_sitin/process/add_points.php" method="POST" style="padding: 1.5rem;">
                <div class="form-grid">
                    <div class="form-group-space">
                        <label class="form-label-space">Select Student</label>
                        <select name="id_number" required class="form-control-space">
                            <option value="">Choose a student...</option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?php echo $s['id_number']; ?>">
                                    <?php echo htmlspecialchars($s['last_name'] . ', ' . $s['first_name']); ?> (<?php echo $s['points']; ?> pts)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group-space">
                        <label class="form-label-space">Points to Add</label>
                        <input type="number" name="points" class="form-control-space" placeholder="e.g. 10" min="1" required>
                    </div>
                    <div class="form-group-space">
                        <label class="form-label-space">Reason</label>
                        <select name="reason" class="form-control-space">
                            <option value="completed_session">Completed Session (+10)</option>
                            <option value="perfect_attendance">Perfect Attendance (+50)</option>
                            <option value="excellent_work">Excellent Work (+20)</option>
                            <option value="custom">Custom Points</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-space btn-space-success">
                    <i class="bi bi-plus-circle"></i> Add Points
                </button>
            </form>
        </div>

        <!-- Student List -->
        <div class="glass-card fade-in-space" style="animation-delay: 0.1s; margin-top: 1.5rem;">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem; font-size: 1.1rem;">
                    <i class="bi bi-people" style="color: var(--accent-cyan);"></i> Student List
                </h3>
                <input type="text" id="searchStudents" class="form-control-space" placeholder="Search students..." style="width: 250px; margin-bottom: 0;" oninput="filterStudents()">
            </div>
            <div id="studentListContainer" style="max-height: 500px; overflow-y: auto;">
                <?php foreach ($students as $s): ?>
                <div class="student-item" data-name="<?php echo strtolower($s['first_name'] . ' ' . $s['last_name']); ?>">
                    <div>
                        <div class="student-name"><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></div>
                        <div class="student-meta"><?php echo htmlspecialchars($s['id_number']); ?> • <?php echo htmlspecialchars($s['course']); ?></div>
                    </div>
                    <div class="points-badge"><i class="bi bi-star-fill"></i> <?php echo $s['points']; ?> pts</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function filterStudents() {
            const q = document.getElementById('searchStudents').value.toLowerCase();
            document.querySelectorAll('.student-item').forEach(item => {
                item.style.display = item.getAttribute('data-name').includes(q) ? '' : 'none';
            });
        }
    </script>
</body>
</html>