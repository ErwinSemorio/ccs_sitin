<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Fetch all students for the search filter
$students_query = mysqli_query($conn, "SELECT * FROM students ORDER BY last_name ASC");
$students = [];
while($row = mysqli_fetch_assoc($students_query)) {
    $students[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search | CCS Admin</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Space Theme CSS -->
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .result-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.25rem; border: 1px solid var(--space-border); border-radius: 12px;
            margin-bottom: 12px; background: rgba(10, 15, 30, 0.4); transition: all 0.2s;
        }
        .result-item:hover { background: rgba(26, 86, 219, 0.15); border-color: var(--accent-blue); }
        .result-name { color: var(--text-primary); font-weight: 700; font-size: 1.1rem; margin-bottom: 0.25rem; }
        .result-info { color: var(--text-secondary); font-size: 0.9rem; }
    </style>
</head>
<body>
    <!-- Space Theme Navbar -->
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space">
                <i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i>
                CCS Admin Dashboard
            </div>
            <ul class="nav-links-space" style="list-style: none; margin: 0; padding: 0; display: flex; gap: 1.5rem; align-items: center;">
                <li><a href="/ccs_sitin/admin/dashboard.php" class="nav-link-space">Home</a></li>
                <li><a href="/ccs_sitin/admin/search.php" class="nav-link-space active">Search</a></li>
                <li><a href="/ccs_sitin/admin/students.php" class="nav-link-space">Students</a></li>
                <li><a href="/ccs_sitin/admin/sitin.php" class="nav-link-space">Sit-in</a></li>
                <li><a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Sit-in Records</a></li>
                <li><a href="/ccs_sitin/admin/reports.php" class="nav-link-space">Reports</a></li>
                <li><a href="/ccs_sitin/admin/feedback.php" class="nav-link-space">Feedback</a></li>
                <li><a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a></li>
                <li><a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a></li>
                <li><a href="/ccs_sitin/admin/add_reward.php" class="nav-link-space">Add Reward</a></li>
                <li><a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-container" style="max-width: 1000px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <span style="width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px;"></span>
            Search Student
        </h2>

        <!-- Search Card -->
        <div class="glass-card fade-in-space">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--space-border);">
                <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="bi bi-search" style="color: var(--accent-blue);"></i> Find a Student
                </h3>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: flex; gap: 10px; margin-bottom: 1.5rem;">
                    <input type="text" class="form-control-space" id="searchInput" placeholder="Enter ID number or name..." oninput="filterResults()" style="width: 100%; background: var(--space-deep); color: var(--text-primary);">
                </div>
                
                <div id="results">
                    <!-- Results will appear here -->
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="bi bi-search" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        <p class="mt-2">Type to search students...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP data to JS
        const studentsData = <?php echo json_encode($students); ?>;

        function filterResults() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const container = document.getElementById('results');
            
            if (!q) {
                container.innerHTML = `<div style="text-align: center; padding: 3rem; color: var(--text-muted);"><i class="bi bi-search" style="font-size: 2.5rem; opacity: 0.5;"></i><p class="mt-2">Type to search students...</p></div>`;
                return;
            }

            const res = studentsData.filter(s => 
                (s.first_name + ' ' + s.last_name).toLowerCase().includes(q) || 
                s.id_number.includes(q)
            );

            if (res.length === 0) {
                container.innerHTML = `<div style="text-align: center; padding: 2rem; color: var(--text-muted);">No students found.</div>`;
                return;
            }

            container.innerHTML = res.map(s => `
                <div class="result-item fade-in-space">
                    <div>
                        <div class="result-name">${s.first_name} ${s.last_name}</div>
                        <div class="result-info">${s.id_number} | ${s.course}</div>
                    </div>
                    <a href="sitin.php?id=${s.id_number}" class="btn-space btn-space-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                        <i class="bi bi-plus-lg"></i> Sit-In
                    </a>
                </div>
            `).join('');
        }
    </script>
</body>
</html>