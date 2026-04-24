<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// --- SORTING LOGIC ---
$allowed_columns = ['date', 'time', 'name', 'status', 'lab']; // Whitelist for security
$sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_columns) ? $_GET['sort'] : 'date';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';

// Build Query
$sql = "SELECT * FROM reservations ORDER BY `$sort_column` $sort_order";
$reservations = mysqli_query($conn, $sql);

// Helper function to generate sort links with arrows
function get_sort_link($column, $current_sort, $current_order) {
    $next_order = ($current_sort === $column && $current_order === 'asc') ? 'desc' : 'asc';
    $arrow = '';
    if ($current_sort === $column) {
        $arrow = $current_order === 'asc' ? ' <i class="bi bi-arrow-up-short"></i>' : ' <i class="bi bi-arrow-down-short"></i>';
    } else {
        $arrow = ' <i class="bi bi-arrow-down-up" style="opacity:0.3; font-size:0.8em;"></i>';
    }
    
    // Preserve other GET params like search if needed, but for now just sort
    return "<a href='?sort=$column&order=$next_order' style='color:inherit; text-decoration:none; display:flex; align-items:center; gap:4px;'>
                $column $arrow
            </a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; animation: fadeInSpace 0.5s ease-out; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .action-btns { display: flex; gap: 0.5rem; }
        .data-table-space td { vertical-align: middle; }
        .badge-space { white-space: nowrap; }
        
        /* Sorting Styles */
        .data-table-space th a {
            color: var(--accent-cyan);
            transition: opacity 0.2s;
        }
        .data-table-space th a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
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
                <a href="/ccs_sitin/admin/reservation.php" class="nav-link-space active">Reservation</a>
                <a href="/ccs_sitin/admin/leaderboard.php" class="nav-link-space">Leaderboard</a>
                <a href="/ccs_sitin/admin/add_reward.php" class="nav-link-space">Add Reward</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">📅 Lab Reservations</h2>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert-space alert-space-<?= $_GET['msg'] === 'approved' ? 'success' : ($_GET['msg'] === 'rejected' ? 'danger' : 'warning') ?>" style="margin-bottom: 1.5rem;">
                <i class="bi bi-<?= $_GET['msg'] === 'approved' ? 'check-circle' : 'x-circle' ?>-fill"></i>
                Reservation <?= htmlspecialchars($_GET['msg']) ?> successfully!
            </div>
        <?php endif; ?>

        <div class="glass-card fade-in-space">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.75rem; font-size: 1.1rem;">
                    <i class="bi bi-calendar-check" style="color: var(--accent-cyan);"></i> 
                    Reservation Requests
                    <?php
                    $pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM reservations WHERE status='Pending'"))['c'];
                    if ($pending_count > 0):
                    ?>
                        <span class="badge-space badge-space-warning"><?= $pending_count ?> Pending</span>
                    <?php endif; ?>
                </h3>
                <input type="text" id="searchRes" class="form-control-space" placeholder="Search reservations..." style="width: 250px; margin-bottom: 0;" onkeyup="filterTable()">
            </div>

            <div class="table-container-space">
                <table class="data-table-space" id="resTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <!-- Sortable Headers -->
                            <th><?= get_sort_link('name', $sort_column, $sort_order) ?></th>
                            <th>ID Number</th>
                            <th><?= get_sort_link('lab', $sort_column, $sort_order) ?></th>
                            <th><?= get_sort_link('date', $sort_column, $sort_order) ?></th>
                            <th><?= get_sort_link('time', $sort_column, $sort_order) ?></th>
                            <th>Purpose</th>
                            <th><?= get_sort_link('status', $sort_column, $sort_order) ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reservations && mysqli_num_rows($reservations) > 0):
                            $i = 1;
                            while ($res = mysqli_fetch_assoc($reservations)): 
                                // Standardized Status Color Logic
                                $status_lower = strtolower($res['status']);
                                if ($status_lower === 'approved') {
                                    $badgeClass = 'badge-space-success';
                                    $statusText = 'Approved';
                                } elseif ($status_lower === 'rejected' || $status_lower === 'declined' || $status_lower === 'invalid') {
                                    $badgeClass = 'badge-space-danger';
                                    $statusText = 'Rejected';
                                } else {
                                    $badgeClass = 'badge-space-warning';
                                    $statusText = 'Pending';
                                }
                        ?>
                        <tr>
                            <td style="color: var(--text-muted);"><?= $i++ ?></td>
                            <td><strong><?= htmlspecialchars($res['name']) ?></strong></td>
                            <td style="font-family: 'JetBrains Mono', monospace; color: var(--accent-cyan);">
                                <?= htmlspecialchars($res['id_number']) ?>
                            </td>
                            <td>Lab <?= htmlspecialchars($res['lab']) ?></td>
                            <td style="font-family: 'JetBrains Mono', monospace;">
                                <?= htmlspecialchars($res['date']) ?>
                            </td>
                            <td><?= htmlspecialchars($res['time']) ?></td>
                            <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($res['purpose']) ?>
                            </td>
                            
                            <!-- STATUS COLUMN -->
                            <td>
                                <span class="badge-space <?= $badgeClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>

                            <!-- ACTIONS COLUMN -->
                            <td>
                                <?php if ($res['status'] === 'Pending'): ?>
                                <div class="action-btns">
                                    <form method="POST" action="/ccs_sitin/process/reservation_process.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-space btn-space-success" style="padding: 0.35rem 0.8rem; font-size: 0.75rem;">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="/ccs_sitin/process/reservation_process.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-space btn-space-danger" style="padding: 0.35rem 0.8rem; font-size: 0.75rem;">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </form>
                                </div>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">— No action</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile;
                        else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                <i class="bi bi-calendar-x" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                No reservations yet.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchRes').value.toLowerCase();
            document.querySelectorAll('#resTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>