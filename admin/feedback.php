<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$feedback = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM sitin_records WHERE feedback IS NOT NULL ORDER BY date DESC"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Reports | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .search-wrapper { width: 100%; max-width: 300px; margin-left: auto; }
    </style>
</head>
<body>
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Admin</div>
            <div class="nav-links-space">
                <a href="/ccs_sitin/admin/dashboard.php" class="nav-link-space">Home</a>
                <a href="/ccs_sitin/admin/search.php" class="nav-link-space">Search</a>
                <a href="/ccs_sitin/admin/students.php" class="nav-link-space">Students</a>
                <a href="/ccs_sitin/admin/sitin.php" class="nav-link-space">Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Records</a>
                <a href="/ccs_sitin/admin/reports.php" class="nav-link-space">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php" class="nav-link-space active">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/admin/add_reward.php" class="nav-link-space">Add Reward</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <div class="section-title">Student Feedback Reports</div>
        <div class="glass-card">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border); display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="bi bi-chat-square-quote" style="color: var(--accent-cyan);"></i> Feedback Entries
                </h3>
                <div class="search-wrapper">
                    <input type="text" id="searchInput" class="form-control-space" placeholder="Search..." style="margin-bottom: 0;" onkeyup="filterTable()">
                </div>
            </div>
            <div class="table-container-space">
                <table class="data-table-space" id="feedbackTable">
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Lab</th><th>Date</th><th>Feedback</th><th>Rating</th></tr>
                    </thead>
                    <tbody>
                        <?php if (count($feedback) > 0): ?>
                            <?php foreach ($feedback as $f): ?>
                                <tr>
                                    <td style="font-family: 'JetBrains Mono', monospace;"><?php echo htmlspecialchars($f['id_number']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($f['name']); ?></strong></td>
                                    <td>Lab <?php echo htmlspecialchars($f['lab']); ?></td>
                                    <td><?php echo htmlspecialchars($f['date']); ?></td>
                                    <td><?php echo htmlspecialchars($f['feedback']); ?></td>
                                    <td style="color: var(--accent-gold);">
                                        <?php for($i=0; $i<($f['rating'] ?? 0); $i++) echo '★'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; padding: 2rem; color: var(--text-muted);">No feedback available yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('#feedbackTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>