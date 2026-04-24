<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$id = $_SESSION['user_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name, last_name FROM students WHERE id_number='$id'"));
$full_name = $student['first_name'] . ' ' . $student['last_name'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $lab = mysqli_real_escape_string($conn, $_POST['lab']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    
    $check = mysqli_query($conn, "SELECT * FROM reservations WHERE id_number='$id' AND date='$date' AND time='$time'");
    if (mysqli_num_rows($check) > 0) {
        $message = '<div class="alert-space alert-space-danger"><i class="bi bi-exclamation-triangle"></i> You already have a reservation at this time!</div>';
    } else {
        $sql = "INSERT INTO reservations (id_number, name, date, time, lab, purpose, status) VALUES ('$id', '$full_name', '$date', '$time', '$lab', '$purpose', 'Pending')";
        if (mysqli_query($conn, $sql)) {
            $message = '<div class="alert-space alert-space-success"><i class="bi bi-check-circle"></i> Reservation submitted! Waiting for approval.</div>';
        } else {
            $message = '<div class="alert-space alert-space-danger"><i class="bi bi-x-circle"></i> Error: ' . mysqli_error($conn) . '</div>';
        }
    }
}

$reservations = mysqli_query($conn, "SELECT * FROM reservations WHERE id_number='$id' ORDER BY date DESC, time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations | CCS Sit-In</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; }
        @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }
        
        .lab-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 0.5rem; }
        .lab-card {
            background: rgba(10, 15, 30, 0.6); border: 1px solid var(--space-border);
            border-radius: var(--radius-sm); padding: 1rem; cursor: pointer;
            transition: all 0.2s ease; display: flex; align-items: center; gap: 1rem; position: relative;
        }
        .lab-card:hover { border-color: var(--accent-cyan); background: rgba(0, 212, 255, 0.05); transform: translateY(-2px); }
        .lab-card.selected { border-color: var(--accent-cyan); background: rgba(0, 212, 255, 0.1); box-shadow: 0 0 15px rgba(0, 212, 255, 0.2); }
        .lab-icon { font-size: 1.5rem; color: var(--text-secondary); width: 40px; height: 40px; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .lab-card.selected .lab-icon { color: var(--accent-cyan); background: rgba(0, 212, 255, 0.1); }
        .lab-info { display: flex; flex-direction: column; }
        .lab-number { font-weight: 700; color: #fff; font-size: 0.9rem; }
        .lab-name { font-size: 0.75rem; color: var(--text-muted); }
        .check-mark { position: absolute; top: 10px; right: 10px; color: var(--accent-cyan); font-size: 1.2rem; opacity: 0; transform: scale(0.5); transition: all 0.2s ease; }
        .lab-card.selected .check-mark { opacity: 1; transform: scale(1); }
        
        .res-item { padding: 1rem; border-bottom: 1px solid var(--space-border); transition: background 0.2s; }
        .res-item:hover { background: rgba(0, 212, 255, 0.05); }
        .res-item:last-child { border-bottom: none; }
        .res-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
        .res-date { font-weight: 700; color: var(--text-primary); font-family: 'JetBrains Mono', monospace; }
        .res-time { color: var(--text-secondary); font-size: 0.85rem; }
        
        #dateInput::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
        .date-error { color: var(--accent-red); font-size: 0.75rem; margin-top: 0.25rem; display: none; }
        .date-error.show { display: block; }
    </style>
</head>
<body>
    <nav class="navbar-space">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="navbar-brand-space"><i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i> CCS Sit-In System</div>
            <div class="nav-links-space">
                <a href="dashboard.php" class="nav-link-space">Home</a>
                <a href="notifications.php" class="nav-link-space">Notification</a>
                <a href="edit_profile.php" class="nav-link-space">Edit Profile</a>
                <a href="history.php" class="nav-link-space">History</a>
                <a href="reservation.php" class="nav-link-space active">Reservation</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">📅 Lab Reservations</h2>
        <div class="grid-2">
            <!-- Form -->
            <div class="glass-card fade-in-space">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                        <i class="bi bi-calendar-plus" style="color: var(--accent-cyan);"></i> Make a Reservation
                    </h3>
                </div>
                <div style="padding: 1.5rem;">
                    <?= $message ?>
                    <form method="POST" id="reservationForm">
                        <div class="form-group-space">
                            <label class="form-label-space">Date</label>
                            <input type="date" class="form-control-space" name="date" id="dateInput" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                            <small id="dateError" class="date-error"><i class="bi bi-exclamation-circle"></i> Please enter a valid date</small>
                        </div>
                        <div class="form-group-space">
                            <label class="form-label-space">Time Slot</label>
                            <select class="form-control-space" name="time" required>
                                <option value="">Select time...</option>
                                <option value="08:00-10:00">08:00 AM - 10:00 AM</option>
                                <option value="10:00-12:00">10:00 AM - 12:00 PM</option>
                                <option value="13:00-15:00">01:00 PM - 03:00 PM</option>
                                <option value="15:00-17:00">03:00 PM - 05:00 PM</option>
                            </select>
                        </div>
                        
                        <div class="form-group-space">
                            <label class="form-label-space">Select Laboratory</label>
                            <input type="hidden" name="lab" id="selectedLab" required>
                            <div class="lab-grid">
                                <div class="lab-card" onclick="selectLab('1', this)">
                                    <div class="lab-icon"><i class="bi bi-pc-display"></i></div>
                                    <div class="lab-info"><span class="lab-number">Lab 1</span><span class="lab-name">Programming</span></div>
                                    <div class="check-mark"><i class="bi bi-check-circle-fill"></i></div>
                                </div>
                                <div class="lab-card" onclick="selectLab('2', this)">
                                    <div class="lab-icon"><i class="bi bi-code-slash"></i></div>
                                    <div class="lab-info"><span class="lab-number">Lab 2</span><span class="lab-name">Web Dev</span></div>
                                    <div class="check-mark"><i class="bi bi-check-circle-fill"></i></div>
                                </div>
                                <div class="lab-card" onclick="selectLab('3', this)">
                                    <div class="lab-icon"><i class="bi bi-database"></i></div>
                                    <div class="lab-info"><span class="lab-number">Lab 3</span><span class="lab-name">Database</span></div>
                                    <div class="check-mark"><i class="bi bi-check-circle-fill"></i></div>
                                </div>
                                <div class="lab-card" onclick="selectLab('4', this)">
                                    <div class="lab-icon"><i class="bi bi-camera-video"></i></div>
                                    <div class="lab-info"><span class="lab-number">Lab 4</span><span class="lab-name">Multimedia</span></div>
                                    <div class="check-mark"><i class="bi bi-check-circle-fill"></i></div>
                                </div>
                            </div>
                            <small id="labError" style="color: var(--accent-red); font-size: 0.75rem; display: none; margin-top: 0.5rem;"><i class="bi bi-exclamation-circle"></i> Please select a laboratory</small>
                        </div>

                        <div class="form-group-space">
                            <label class="form-label-space">Purpose</label>
                            <textarea class="form-control-space" name="purpose" rows="3" placeholder="Describe your purpose..." required></textarea>
                        </div>
                        <button type="submit" name="reserve" class="btn-space btn-space-primary" style="width: 100%;">
                            <i class="bi bi-check-circle"></i> Submit Reservation
                        </button>
                    </form>
                </div>
            </div>

            <!-- My Reservations -->
            <div class="glass-card fade-in-space" style="animation-delay: 0.1s;">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--space-border);">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                        <i class="bi bi-list-check" style="color: var(--accent-cyan);"></i> My Reservations
                    </h3>
                </div>
                <div style="max-height: 500px; overflow-y: auto;">
                    <?php if (mysqli_num_rows($reservations) > 0): ?>
                        <?php while ($res = mysqli_fetch_assoc($reservations)): 
                            // LOGIC: Check status lowercase
                            $status_lower = strtolower($res['status']);
                            
                            // Determine Badge Color
                            if ($status_lower === 'approved') {
                                $badgeClass = 'badge-space-success'; // Green
                            } elseif ($status_lower === 'rejected' || $status_lower === 'declined' || $status_lower === 'invalid') {
                                $badgeClass = 'badge-space-danger'; // RED
                            } else {
                                $badgeClass = 'badge-space-warning'; // Yellow
                            }
                        ?>
                        <div class="res-item">
                            <div class="res-header">
                                <div><span class="res-date"><?= htmlspecialchars($res['date']) ?></span> <span class="res-time">| <?= htmlspecialchars($res['time']) ?></span></div>
                                <span class="badge-space <?= $badgeClass ?>"><?= htmlspecialchars($res['status']) ?></span>
                            </div>
                            <p style="margin: 0.5rem 0; color: var(--text-primary);"><strong>Lab <?= htmlspecialchars($res['lab']) ?>:</strong> <?= htmlspecialchars($res['purpose']) ?></p>
                            <small style="color: var(--text-muted);"><i class="bi bi-clock"></i> Submitted: <?= date('M d, Y', strtotime($res['created_at'] ?? 'now')) ?></small>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                            <i class="bi bi-calendar-x" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4;"></i>
                            No reservations yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectLab(labId, element) {
            document.querySelectorAll('.lab-card').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('selectedLab').value = labId;
            document.getElementById('labError').style.display = 'none';
        }

        const dateInput = document.getElementById('dateInput');
        const dateError = document.getElementById('dateError');
        const form = document.getElementById('reservationForm');
        
        dateInput.addEventListener('change', function(e) {
            const value = e.target.value;
            const today = new Date();
            const selected = new Date(value);
            const maxDate = new Date();
            maxDate.setDate(today.getDate() + 30);
            
            today.setHours(0,0,0,0); selected.setHours(0,0,0,0); maxDate.setHours(0,0,0,0);
            
            if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) { showError('Invalid date format'); e.target.value = ''; return; }
            if (selected < today) { showError('Date cannot be in the past'); e.target.value = ''; return; }
            if (selected > maxDate) { showError('You can only book up to 30 days in advance'); e.target.value = ''; return; }
            hideError();
        });

        form.addEventListener('submit', function(e) {
            const value = dateInput.value;
            const lab = document.getElementById('selectedLab').value;
            if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) { e.preventDefault(); showError('Please enter a valid date'); return false; }
            if (!lab) { e.preventDefault(); document.getElementById('labError').style.display = 'block'; alert("Please select a laboratory."); return false; }
        });

        function showError(msg) { dateError.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${msg}`; dateError.classList.add('show'); }
        function hideError() { dateError.classList.remove('show'); }
    </script>
</body>
</html>