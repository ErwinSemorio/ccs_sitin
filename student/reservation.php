<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

$id = $_SESSION['user_id'];
$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id_number='$id'"));

// Handle reservation submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $lab = mysqli_real_escape_string($conn, $_POST['lab']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    
    // Check for conflicts
    $check = mysqli_query($conn, "SELECT * FROM reservations WHERE id_number='$id' AND date='$date' AND time='$time'");
    if (mysqli_num_rows($check) > 0) {
        $message = '<div class="alert alert-danger">You already have a reservation at this time!</div>';
    } else {
        $sql = "INSERT INTO reservations (id_number, name, date, time, lab, purpose, status) 
                VALUES ('$id', '{$student['first_name']} {$student['last_name']}', '$date', '$time', '$lab', '$purpose', 'Pending')";
        if (mysqli_query($conn, $sql)) {
            $message = '<div class="alert alert-success">Reservation submitted! Waiting for approval.</div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
        }
    }
}

// Fetch user's reservations
$reservations = mysqli_query($conn, "SELECT * FROM reservations WHERE id_number='$id' ORDER BY date DESC, time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations | CCS Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .header { background-color: #0d47a1; color: white; padding: 15px 0; margin-bottom: 30px; }
        .nav-link { color: white !important; margin: 0 8px; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: white !important; background: rgba(255,255,255,0.15); border-radius: 6px; }
        .card { border: 1px solid #dee2e6; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-header { background-color: #0d47a1; color: white; font-weight: bold; border-radius: 12px 12px 0 0 !important; padding: 15px 20px; }
        .form-label { font-weight: 500; color: #495057; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 15px; }
        .reservation-item { padding: 15px; border-bottom: 1px solid #dee2e6; }
        .reservation-item:last-child { border-bottom: none; }
        .badge-pending { background-color: #fff3cd; color: #856404; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-approved { background-color: #d1fae5; color: #065f46; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Dashboard</h4>
                <nav>
                    <a href="notifications.php" class="nav-link d-inline">Notification</a>
                    <a href="dashboard.php" class="nav-link d-inline">Home</a>
                    <a href="edit_profile.php" class="nav-link d-inline">Edit Profile</a>
                    <a href="history.php" class="nav-link d-inline">History</a>
                    <a href="reservation.php" class="nav-link d-inline active">Reservation</a>
                    <a href="/ccs_sitin/logout.php" class="btn btn-warning btn-sm ms-2">Log out</a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <?= $message ?>
        
        <div class="row">
            <!-- Reservation Form -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header"><i class="bi bi-calendar-plus me-2"></i>Make a Reservation</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Time Slot</label>
                                <select class="form-select" name="time" required>
                                    <option value="">Select time...</option>
                                    <option value="08:00-10:00">08:00 AM - 10:00 AM</option>
                                    <option value="10:00-12:00">10:00 AM - 12:00 PM</option>
                                    <option value="13:00-15:00">01:00 PM - 03:00 PM</option>
                                    <option value="15:00-17:00">03:00 PM - 05:00 PM</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Laboratory</label>
                                <select class="form-select" name="lab" required>
                                    <option value="">Select lab...</option>
                                    <option value="1">Lab 1 - Programming</option>
                                    <option value="2">Lab 2 - Web Development</option>
                                    <option value="3">Lab 3 - Database Systems</option>
                                    <option value="4">Lab 4 - Multimedia</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Purpose</label>
                                <textarea class="form-control" name="purpose" rows="3" placeholder="Describe your purpose..." required></textarea>
                            </div>
                            <button type="submit" name="reserve" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Submit Reservation
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- My Reservations -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header"><i class="bi bi-list-check me-2"></i>My Reservations</div>
                    <div class="card-body p-0">
                        <?php if (mysqli_num_rows($reservations) > 0): ?>
                            <?php while ($res = mysqli_fetch_assoc($reservations)): ?>
                            <div class="reservation-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong><?= htmlspecialchars($res['date']) ?></strong> 
                                        <span class="text-muted">| <?= htmlspecialchars($res['time']) ?></span>
                                    </div>
                                    <span class="badge-<?= strtolower($res['status']) ?>">
                                        <?= htmlspecialchars($res['status']) ?>
                                    </span>
                                </div>
                                <p class="mb-1"><strong>Lab <?= htmlspecialchars($res['lab']) ?>:</strong> <?= htmlspecialchars($res['purpose']) ?></p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>Submitted: <?= date('M d, Y', strtotime($res['created_at'])) ?>
                                </small>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-calendar-x mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="mb-0">No reservations yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>