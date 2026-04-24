<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$result = mysqli_query($conn, "SELECT * FROM students ORDER BY last_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid var(--space-border); flex-wrap: wrap; gap: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Admin</div>
            <div class="nav-links-space">
                <a href="/ccs_sitin/admin/dashboard.php" class="nav-link-space">Home</a>
                <a href="/ccs_sitin/admin/search.php" class="nav-link-space">Search</a>
                <a href="/ccs_sitin/admin/students.php" class="nav-link-space active">Students</a>
                <a href="/ccs_sitin/admin/sitin.php" class="nav-link-space">Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Records</a>
                <a href="/ccs_sitin/admin/reports.php" class="nav-link-space">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php" class="nav-link-space">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <div class="section-title">Registered Students</div>
        <div class="glass-card">
            <div class="toolbar">
                <div class="badge-space badge-space-info" style="font-size: 0.85rem;">
                    Total: <?php echo mysqli_num_rows($result); ?> Students
                </div>
                <input class="form-control-space" style="width: 250px; margin-bottom: 0;" id="searchInput" placeholder="Search ID or Name..." onkeyup="filterStudentsTable()"/>
            </div>
            <div class="table-container-space">
                <table class="data-table-space" id="studentsTable">
                    <thead>
                        <tr><th>#</th><th>ID Number</th><th>Full Name</th><th>Course</th><th>Email</th><th>Address</th><th>Registered</th></tr>
                    </thead>
                    <tbody id="studentsTbody">
                        <?php
                        $count = 1;
                        if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td style="color: var(--text-muted);"><?php echo $count++; ?></td>
                            <td style="font-family: 'JetBrains Mono', monospace; color: var(--accent-cyan);"><?php echo htmlspecialchars($row['id_number']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name']); ?></strong></td>
                            <td><span class="badge-space badge-space-primary"><?php echo htmlspecialchars($row['course']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td style="color: var(--text-muted);"><?php echo isset($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'N/A'; ?></td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr><td colspan="7" style="text-align:center; padding: 2rem; color: var(--text-muted);">No students found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterStudentsTable() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('#studentsTbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        }
    </script>
</body>
</html>