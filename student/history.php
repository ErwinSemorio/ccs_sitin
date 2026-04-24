<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM sitin_records WHERE id_number = ? ORDER BY date DESC, time_in DESC");
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$history = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History | CCS Sit-In</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .modal-space { display: none; position: fixed; inset: 0; background: rgba(5, 8, 15, 0.85); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(8px); }
        .modal-space.open { display: flex; }
        .modal-content-space { background: var(--space-deep); border: 1px solid var(--space-border); border-radius: var(--radius); width: 450px; max-width: 95%; box-shadow: var(--shadow-panel); overflow: hidden; }
        .modal-header { padding: 1.25rem; background: rgba(0, 212, 255, 0.05); border-bottom: 1px solid var(--space-border); display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 1.5rem; }
        .modal-footer { padding: 1.25rem; border-top: 1px solid var(--space-border); display: flex; justify-content: flex-end; gap: 10px; }
        .star-rating { display: flex; gap: 8px; font-size: 28px; color: #334155; cursor: pointer; }
        .star-rating i:hover, .star-rating i.active { color: var(--accent-gold); transform: scale(1.1); transition: 0.2s; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-space">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Sit-In System</div>
            <div class="nav-links-space">
                <a href="dashboard.php" class="nav-link-space">Home</a>
                <a href="notifications.php" class="nav-link-space">Notification</a>
                <a href="edit_profile.php" class="nav-link-space">Edit Profile</a>
                <a href="history.php" class="nav-link-space active">History</a>
                <a href="reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">📜 Sit-in History</h2>
        <div class="glass-card fade-in-space">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-clock-history" style="color: var(--accent-cyan);"></i> Your Sessions</h3>
                <span class="badge-space badge-space-info">Total: <?php echo count($history); ?></span>
            </div>
            <div class="table-container-space">
                <table class="data-table-space">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Purpose</th>
                            <th>Lab</th>
                            <th>Time In</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Feedback</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($history) > 0): ?>
                            <?php foreach ($history as $row):
                                $duration = "N/A";
                                if ($row['time_in'] && $row['time_out']) {
                                    $diff = strtotime($row['time_out']) - strtotime($row['time_in']);
                                    $hours = floor($diff / 3600);
                                    $minutes = floor(($diff % 3600) / 60);
                                    $duration = ($hours > 0 ? $hours . "h " : "") . $minutes . "m";
                                }
                                $hasFeedback = !empty($row['feedback']);
                                $rating = $row['rating'] ?? 0;
                            ?>
                            <tr>
                                <td style="font-family: 'JetBrains Mono', monospace;"><?= htmlspecialchars($row['date']) ?></td>
                                <td><?= htmlspecialchars($row['purpose']) ?></td>
                                <td>Lab <?= htmlspecialchars($row['lab']) ?></td>
                                <td style="font-family: 'JetBrains Mono', monospace;"><?= htmlspecialchars($row['time_in']) ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Active'): ?>
                                        <span style="color: var(--text-muted);">—</span>
                                    <?php else: ?>
                                        <strong><?= $duration ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-space <?= $row['status'] === 'Active' ? 'badge-space-warning' : 'badge-space-success' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($hasFeedback): ?>
                                        <div>
                                            <span style="color: var(--accent-gold);">
                                                <?php for($i=1; $i<=$rating; $i++) echo '★'; ?>
                                            </span>
                                            <br>
                                            <small style="color: var(--text-muted);"><?= htmlspecialchars(substr($row['feedback'], 0, 25)) ?>...</small>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">No feedback</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Done' && !$hasFeedback): ?>
                                        <button class="btn-space btn-space-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" onclick="openFeedbackModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['purpose']) ?>', '<?= htmlspecialchars($row['date']) ?>')">
                                            Give Feedback
                                        </button>
                                    <?php elseif ($row['status'] === 'Active'): ?>
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">Active</span>
                                    <?php else: ?>
                                        <span class="badge-space badge-space-success"><i class="bi bi-check"></i> Sent</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                    <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4;"></i>
                                    No history available yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal-space" id="feedbackModal">
        <div class="modal-content-space">
            <div class="modal-header">
                <h3 style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="bi bi-chat-square-dots" style="color: var(--accent-blue);"></i> Give Feedback
                </h3>
                <button onclick="closeModal()" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.2rem;">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <form id="feedbackForm">
                <div class="modal-body">
                    <p style="color: var(--text-secondary); margin-bottom: 1rem;">Session: <span id="modalSessionDetails" style="color: var(--text-primary); font-weight: 600;"></span></p>
                    <input type="hidden" id="recordId">
                    <div class="form-group-space">
                        <label class="form-label-space">Your Rating</label>
                        <div class="star-rating" id="starRating">
                            <i class="bi bi-star-fill" data-value="1"></i>
                            <i class="bi bi-star-fill" data-value="2"></i>
                            <i class="bi bi-star-fill" data-value="3"></i>
                            <i class="bi bi-star-fill" data-value="4"></i>
                            <i class="bi bi-star-fill" data-value="5"></i>
                        </div>
                        <input type="hidden" id="ratingValue" name="rating" value="5">
                    </div>
                    <div class="form-group-space">
                        <label class="form-label-space">Comments</label>
                        <textarea class="form-control-space" id="feedbackText" rows="3" placeholder="How was your experience?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-space btn-space-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-space btn-space-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const stars = document.querySelectorAll('#starRating i');
        const ratingInput = document.getElementById('ratingValue');
        stars.forEach(star => star.addEventListener('click', () => {
            const value = star.getAttribute('data-value');
            ratingInput.value = value;
            stars.forEach(s => s.classList.toggle('active', s.getAttribute('data-value') <= value));
        }));
        const modal = document.getElementById('feedbackModal');
        function openFeedbackModal(id, purpose, date) {
            document.getElementById('recordId').value = id;
            document.getElementById('modalSessionDetails').textContent = `${purpose} (${date})`;
            document.getElementById('feedbackText').value = '';
            ratingInput.value = 5;
            stars.forEach(s => s.classList.toggle('active', s.getAttribute('data-value') <= 5));
            modal.classList.add('open');
        }
        function closeModal() { modal.classList.remove('open'); }
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('/ccs_sitin/process/feedback_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `record_id=${document.getElementById('recordId').value}&rating=${ratingInput.value}&feedback=${encodeURIComponent(document.getElementById('feedbackText').value)}`
            }).then(res => res.json()).then(data => {
                if(data.success) { alert('Feedback submitted!'); closeModal(); location.reload(); }
                else alert('Error: ' + (data.message || 'Failed'));
            }).catch(() => alert('Submission failed.'));
        });
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    </script>
</body>
</html>