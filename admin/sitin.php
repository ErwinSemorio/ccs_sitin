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
    <title>Sit-in Management | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Space Theme CSS -->
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        
        /* Modal Styling for Space Theme */
        .modal-space {
            display: none; position: fixed; inset: 0;
            background: rgba(5, 8, 15, 0.8); z-index: 1000;
            align-items: center; justify-content: center;
            backdrop-filter: blur(8px);
        }
        .modal-space.open { display: flex; }
        .modal-content-space {
            background: var(--space-deep); border: 1px solid var(--space-border);
            border-radius: var(--radius); width: 450px; max-width: 95%;
            box-shadow: var(--shadow-panel); overflow: hidden;
        }
        .modal-header {
            padding: 1.25rem; background: rgba(0, 212, 255, 0.05);
            border-bottom: 1px solid var(--space-border); display: flex; justify-content: space-between; align-items: center;
        }
        .modal-body { padding: 1.5rem; }
        .modal-footer { padding: 1.25rem; border-top: 1px solid var(--space-border); display: flex; justify-content: flex-end; gap: 10px; }
    </style>
</head>
<body>
    <!-- Space Theme Navbar -->
    <nav class="navbar-space">
        <div class="container">
            <div class="navbar-brand-space">
                <i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i>
                CCS Admin
            </div>
            <div class="nav-links-space">
                <a href="/ccs_sitin/admin/dashboard.php" class="nav-link-space">Home</a>
                <a href="/ccs_sitin/admin/search.php" class="nav-link-space">Search</a>
                <a href="/ccs_sitin/admin/students.php" class="nav-link-space">Students</a>
                <a href="/ccs_sitin/admin/sitin.php" class="nav-link-space active">Sit-in</a>
                <a href="/ccs_sitin/admin/sitin_records.php" class="nav-link-space">Records</a>
                <a href="/ccs_sitin/admin/reports.php" class="nav-link-space">Reports</a>
                <a href="/ccs_sitin/admin/feedback.php" class="nav-link-space">Feedback</a>
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/admin/add_reward.php" class="nav-link-space">Add Reward</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <?php if (isset($_GET['success'])): ?>
    <div class="alert-space alert-space-success" style="margin-bottom: 1.5rem;">
        <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['success']) ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert-space alert-space-danger" style="margin-bottom: 1.5rem;">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>
        <div class="section-title">Active Sit-in Sessions</div>
        
        <div class="glass-card">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="bi bi-activity" style="color: var(--accent-cyan);"></i> Currently Active
                </h3>
                <button class="btn-space btn-space-primary" onclick="document.getElementById('sitInModal').classList.add('open')">
                    <i class="bi bi-plus-lg"></i> New Sit-in
                </button>
            </div>
            
            <div class="table-container-space">
                <table class="data-table-space">
                    <thead>
                        <tr>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Purpose</th>
                            <th>Lab</th>
                            <th>Time In</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($active) > 0): ?>
                            <?php foreach ($active as $a): ?>
                                <tr>
                                    <td style="font-family: 'JetBrains Mono', monospace; color: var(--accent-cyan);"><?php echo htmlspecialchars($a['id_number']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($a['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($a['purpose']); ?></td>
                                    <td>Lab <?php echo htmlspecialchars($a['lab']); ?></td>
                                    <td><?php echo htmlspecialchars($a['time_in']); ?></td>
                                    <td>
                                        <span class="badge-space badge-space-warning">
                                            <span style="width:6px;height:6px;background:currentColor;border-radius:50%;margin-right:5px;display:inline-block;"></span>
                                            Active
                                        </span>
                                    </td>
                                    <td>
                                        <form action="/ccs_sitin/process/timeout_process.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="sit_id" value="<?php echo $a['id']; ?>">
                                            <button type="submit" class="btn-space btn-space-danger" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Time Out</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    <i class="bi bi-emoji-neutral" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                    No active sessions currently
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sit-in Modal -->
    <div class="modal-space" id="sitInModal">
        <div class="modal-content-space">
            <div class="modal-header">
                <h3 style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="bi bi-laptop" style="color: var(--accent-blue);"></i> Register Sit-in
                </h3>
                <button onclick="document.getElementById('sitInModal').classList.remove('open')" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.2rem;"><i class="bi bi-x"></i></button>
            </div>
            <form action="/ccs_sitin/process/sitin_process.php" method="POST">
                <div class="modal-body">
                    <div class="form-group-space">
                        <label class="form-label-space">ID Number</label>
                        <input name="id_number" list="studentList" required class="form-control-space" placeholder="Enter ID or Name...">
                        <datalist id="studentList">
                            <?php foreach($students as $s): ?>
                                <option value="<?php echo $s['id_number']; ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="form-group-space">
                        <label class="form-label-space">Purpose</label>
                        <select name="purpose" required class="form-control-space">
                            <option value="">Select purpose...</option>
                            <option>C Programming</option><option>Java</option><option>Web Development</option>
                            <option>Database Systems</option><option>ASP.Net</option><option>PHP</option><option>C#</option>
                        </select>
                    </div>
                    <div class="form-group-space">
                        <label class="form-label-space">Lab</label>
                        <select name="lab" required class="form-control-space">
                            <option value="">Select lab...</option>
                            <option>521</option><option>522</option><option>523</option>
                            <option>524</option><option>525</option><option>526</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('sitInModal').classList.remove('open')" class="btn-space btn-space-secondary">Cancel</button>
                    <button type="submit" class="btn-space btn-space-primary">Check In</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>