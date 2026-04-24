<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$id = $_SESSION['user_id'];

// Fetch student info for name display
$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name, last_name, profile_photo FROM students WHERE id_number = '$id'"));

// Handle reservation submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $lab = mysqli_real_escape_string($conn, $_POST['lab']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    
    // Validate date format server-side
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $message = '<div class="alert-space alert-space-danger"><i class="bi bi-exclamation-triangle"></i> Invalid date format.</div>';
    } else {
        // Check for conflicts
        $check = mysqli_query($conn, "SELECT * FROM reservations WHERE id_number='$id' AND date='$date' AND time='$time'");
        if (mysqli_num_rows($check) > 0) {
            $message = '<div class="alert-space alert-space-danger"><i class="bi bi-exclamation-triangle"></i> You already have a reservation at this time!</div>';
        } else {
            $sql = "INSERT INTO reservations (id_number, name, date, time, lab, purpose, status) 
                    VALUES ('$id', '{$student['first_name']} {$student['last_name']}', '$date', '$time', '$lab', '$purpose', 'Pending')";
            if (mysqli_query($conn, $sql)) {
                $message = '<div class="alert-space alert-space-success"><i class="bi bi-check-circle"></i> Reservation submitted! Waiting for approval.</div>';
            } else {
                $message = '<div class="alert-space alert-space-danger"><i class="bi bi-x-circle"></i> Error: ' . mysqli_error($conn) . '</div>';
            }
        }
    }
}

// Fetch user's reservations
$reservations = mysqli_query($conn, "SELECT * FROM reservations WHERE id_number='$id' ORDER BY date DESC, time ASC");

// Profile photo for navbar
$profile_photo = $student['profile_photo'] ?? 'default.jpg';
$photo_path = "/ccs_sitin/uploads/profiles/" . $profile_photo;
$photo_exists = file_exists(__DIR__ . '/../uploads/profiles/' . $profile_photo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations | CCS Sit-In</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Space Theme CSS -->
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; }
        @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }
        
        /* Reservation Items */
        .res-item { 
            padding: 1rem; 
            border-bottom: 1px solid var(--space-border); 
            transition: background 0.2s;
        }
        .res-item:hover { background: rgba(0, 212, 255, 0.05); }
        .res-item:last-child { border-bottom: none; }
        .res-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
        .res-date { font-weight: 700; color: var(--text-primary); font-family: 'JetBrains Mono', monospace; }
        .res-time { color: var(--text-secondary); font-size: 0.85rem; }
        
        /* Status Badges */
        .badge-pending { background: rgba(245, 158, 11, 0.2); color: var(--accent-gold); padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; }
        .badge-approved { background: rgba(16, 185, 129, 0.2); color: var(--accent-green); padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; }
        .badge-rejected { background: rgba(239, 68, 68, 0.2); color: var(--accent-red); padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; }
        
        /* Date Input Fix */
        #dateInput::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
        .date-error { color: var(--accent-red); font-size: 0.75rem; margin-top: 0.25rem; display: none; }
        .date-error.show { display: block; }
    </style>
</head>
<body>
    <!-- 🔹 Space Theme Navbar -->
    <nav class="navbar-space">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="navbar-brand-space">
                <i class="bi bi-shield-lock" style="color: var(--accent-cyan);"></i>
                CCS Sit-In System
            </div>
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
            <!-- Reservation Form -->
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
                            <input type="date" 
                                   class="form-control-space" 
                                   name="date" 
                                   id="dateInput"
                                   min="<?= date('Y-m-d') ?>" 
                                   max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                                   required
                                   pattern="\d{4}-\d{2}-\d{2}"
                                   title="Please enter a valid date (YYYY-MM-DD)">
                            <small id="dateError" class="date-error">
                                <i class="bi bi-exclamation-circle"></i> Please enter a valid date
                            </small>
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
                            <label class="form-label-space">Laboratory</label>
                            <select class="form-control-space" name="lab" required>
                                <option value="">Select lab...</option>
                                <option value="1">Lab 1 - Programming</option>
                                <option value="2">Lab 2 - Web Development</option>
                                <option value="3">Lab 3 - Database Systems</option>
                                <option value="4">Lab 4 - Multimedia</option>
                            </select>
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
                        <?php while ($res = mysqli_fetch_assoc($reservations)): ?>
                        <div class="res-item">
                            <div class="res-header">
                                <div>
                                    <span class="res-date"><?= htmlspecialchars($res['date']) ?></span> 
                                    <span class="res-time">| <?= htmlspecialchars($res['time']) ?></span>
                                </div>
                                <span class="badge-<?= strtolower($res['status']) ?>">
                                    <?= htmlspecialchars($res['status']) ?>
                                </span>
                            </div>
                            <p style="margin: 0.5rem 0; color: var(--text-primary);">
                                <strong>Lab <?= htmlspecialchars($res['lab']) ?>:</strong> 
                                <?= htmlspecialchars($res['purpose']) ?>
                            </p>
                            <small style="color: var(--text-muted);">
                                <i class="bi bi-clock"></i> Submitted: <?= date('M d, Y', strtotime($res['created_at'] ?? 'now')) ?>
                            </small>
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
        // 🔹 Date Validation - Prevent invalid dates like "222222"
        const dateInput = document.getElementById('dateInput');
        const dateError = document.getElementById('dateError');
        const form = document.getElementById('reservationForm');
        
        // Validate on input
        dateInput.addEventListener('input', function(e) {
            const value = e.target.value;
            // Only allow numbers and hyphens in correct format
            if (!/^\d{0,4}-?\d{0,2}-?\d{0,2}$/.test(value)) {
                e.target.value = value.replace(/[^\d-]/g, '');
            }
        });
        
        // Validate on change
        dateInput.addEventListener('change', function(e) {
            const value = e.target.value;
            const today = new Date();
            const selected = new Date(value);
            const maxDate = new Date();
            maxDate.setDate(today.getDate() + 30);
            
            // Reset to midnight for accurate comparison
            today.setHours(0,0,0,0);
            selected.setHours(0,0,0,0);
            maxDate.setHours(0,0,0,0);
            
            // Check format
            if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                showError('Invalid date format (YYYY-MM-DD)');
                e.target.value = '';
                return;
            }
            
            // Check if in past
            if (selected < today) {
                showError('Date cannot be in the past');
                e.target.value = '';
                return;
            }
            
            // Check if more than 30 days ahead
            if (selected > maxDate) {
                showError('You can only book up to 30 days in advance');
                e.target.value = '';
                return;
            }
            
            // Valid date
            hideError();
        });
        
        // Form submit validation
        form.addEventListener('submit', function(e) {
            const value = dateInput.value;
            if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                e.preventDefault();
                showError('Please enter a valid date');
                dateInput.focus();
                return false;
            }
        });
        
        function showError(msg) {
            dateError.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${msg}`;
            dateError.classList.add('show');
        }
        
        function hideError() {
            dateError.classList.remove('show');
        }
    </script>
</body>
</html>