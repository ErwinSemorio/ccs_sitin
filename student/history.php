<?php
session_start();
// ✅ Security: Ensure only logged-in students can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}

include __DIR__ . "/../config/database.php";

$id = $_SESSION['user_id'];

// ✅ Fetch student history from the correct 'sitin_records' table
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
    <title>History & Feedback | CCS Student</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --navy: #0f2044; --blue: #1a56db; --blue-lt: #e8f0fe;
            --green: #057a55; --green-lt: #def7ec; --amber: #d97706; --amber-lt: #fef3c7;
            --bg: #f1f5fb; --surface: #ffffff; --border: #e2e8f4;
            --text: #0f2044; --muted: #64748b; --shadow: 0 4px 20px rgba(15,32,68,.10);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; padding-bottom: 40px; }
        
        /* Header / Navbar */
        .header { background-color: #0d47a1; color: white; padding: 15px 0; margin-bottom: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .nav-link { color: rgba(255,255,255,0.85) !important; margin: 0 8px; font-weight: 500; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { color: white !important; text-decoration: none; background: rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 10px; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        
        /* Cards */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; margin-bottom: 20px; }
        .card-head { padding: 20px; border-bottom: 1px solid var(--border); font-weight: 700; font-size: 16px; color: var(--navy); display: flex; justify-content: space-between; align-items: center; }
        
        /* Tables */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); background: #f8fafc; border-bottom: 1px solid var(--border); }
        td { padding: 14px 20px; font-size: 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafbfc; }
        
        /* Badges & Buttons */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-active { background: var(--amber-lt); color: var(--amber); }
        .badge-done { background: var(--green-lt); color: var(--green); }
        .badge-feedback { background: var(--blue-lt); color: var(--blue); }
        
        .btn { padding: 8px 16px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; font-size: 13px; }
        .btn-primary { background: var(--blue); color: white; }
        .btn-primary:hover { opacity: 0.9; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }
        
        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-overlay.open { display: flex; }
        .modal { background: white; width: 450px; max-width: 90%; padding: 24px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal h3 { margin-top: 0; color: var(--navy); display: flex; align-items: center; gap: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; color: var(--muted); }
        .form-control { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-family: inherit; }
        .form-control:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(26,86,219,0.1); }
        
        /* Star Rating */
        .star-rating { display: flex; gap: 8px; font-size: 28px; color: #e2e8f0; cursor: pointer; margin-bottom: 10px; }
        .star-rating i:hover, .star-rating i.active { color: #ffc107; transform: scale(1.1); transition: 0.2s; }
        
        .empty-state { text-align: center; padding: 50px; color: var(--muted); }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">Dashboard</h4>
                <nav>
                    <a href="notifications.php" class="nav-link d-inline">Notification</a>
                    <a href="dashboard.php" class="nav-link d-inline">Home</a>
                    <a href="edit_profile.php" class="nav-link d-inline">Edit Profile</a>
                    <a href="history.php" class="nav-link d-inline active">History</a>
                    <a href="reservation.php" class="nav-link d-inline">Reservation</a>
                    <a href="/ccs_sitin/logout.php" class="btn btn-warning btn-sm ms-2 px-3">Log out</a>
                </nav>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-head">
                <span><i class="bi bi-clock-history me-2"></i>Sit-in History</span>
                <span class="badge badge-active">Total: <?php echo count($history); ?></span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Purpose</th>
                            <th>Laboratory</th>
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
                                // Calculate duration
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
                                <td><?= htmlspecialchars($row['date']) ?></td>
                                <td><?= htmlspecialchars($row['purpose']) ?></td>
                                <td>Lab <?= htmlspecialchars($row['lab']) ?></td>
                                <td><?= htmlspecialchars($row['time_in']) ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Active'): ?>
                                        <span class="text-muted">—</span>
                                    <?php else: ?>
                                        <strong><?= $duration ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= ($row['status'] === 'Active') ? 'badge-active' : 'badge-done' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($hasFeedback): ?>
                                        <div>
                                            <span class="badge badge-feedback" style="color:#ffc107; background:#fffbeb;">
                                                <?php for($i=1; $i<=$rating; $i++) echo "★"; ?>
                                            </span>
                                            <small class="d-block text-muted mt-1"><?= htmlspecialchars(substr($row['feedback'], 0, 30)) ?>...</small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">No feedback</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Done' && !$hasFeedback): ?>
                                        <button class="btn btn-primary btn-sm" onclick="openFeedbackModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['purpose']) ?>', '<?= htmlspecialchars($row['date']) ?>')">
                                            <i class="bi bi-pencil"></i> Give Feedback
                                        </button>
                                    <?php elseif ($row['status'] === 'Active'): ?>
                                        <span class="text-muted small">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-done"><i class="bi bi-check"></i> Sent</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-2">No history available yet. Start a sit-in to see it here!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal-overlay" id="feedbackModal">
        <div class="modal">
            <h3><i class="bi bi-chat-square-dots" style="color:var(--blue);"></i> Give Feedback</h3>
            <p class="text-muted mb-3" style="font-size:14px;">Session: <span id="modalSessionDetails" style="color:var(--text); font-weight:600;"></span></p>
            
            <form id="feedbackForm">
                <input type="hidden" id="recordId">
                
                <div class="form-group">
                    <label>Your Rating</label>
                    <div class="star-rating" id="starRating">
                        <i class="bi bi-star-fill" data-value="1"></i>
                        <i class="bi bi-star-fill" data-value="2"></i>
                        <i class="bi bi-star-fill" data-value="3"></i>
                        <i class="bi bi-star-fill" data-value="4"></i>
                        <i class="bi bi-star-fill" data-value="5"></i>
                    </div>
                    <input type="hidden" id="ratingValue" name="rating" value="5">
                </div>
                
                <div class="form-group">
                    <label>Comments</label>
                    <textarea class="form-control" id="feedbackText" rows="3" placeholder="How was your experience? (Optional)" required></textarea>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" style="background: #e2e8f4; color: #64748b;" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Star Rating Logic
        const stars = document.querySelectorAll('#starRating i');
        const ratingInput = document.getElementById('ratingValue');
        
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = star.getAttribute('data-value');
                ratingInput.value = value;
                updateStars(value);
            });
        });

        function updateStars(value) {
            stars.forEach(s => {
                if (s.getAttribute('data-value') <= value) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        }

        // Modal Logic
        const modal = document.getElementById('feedbackModal');
        function openFeedbackModal(id, purpose, date) {
            document.getElementById('recordId').value = id;
            document.getElementById('modalSessionDetails').textContent = `${purpose} (${date})`;
            document.getElementById('feedbackText').value = '';
            ratingInput.value = 5;
            updateStars(5);
            modal.classList.add('open');
        }

        function closeModal() {
            modal.classList.remove('open');
        }

        // Submit Feedback via AJAX
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const recordId = document.getElementById('recordId').value;
            const rating = document.getElementById('ratingValue').value;
            const feedback = document.getElementById('feedbackText').value;

            // Send to process file
            fetch('/ccs_sitin/process/feedback_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `record_id=${recordId}&rating=${rating}&feedback=${encodeURIComponent(feedback)}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Feedback submitted successfully!');
                    closeModal();
                    location.reload(); // Refresh page to update table
                } else {
                    alert('Error: ' + (data.message || 'Submission failed'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Submission failed. Please try again.');
            });
        });

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    </script>
</body>
</html>