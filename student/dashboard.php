<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Fetch student info
$id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM students WHERE id_number='$id'");
$student = mysqli_fetch_assoc($result);

// Fetch announcements
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY date DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard | CCS</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
body {
    background-color: #f8f9fa;
}
.header {
    background-color: #0d47a1;
    color: white;
    padding: 15px 0;
    margin-bottom: 20px;
}
.card {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    margin-bottom: 20px;
}
.card-header {
    background-color: #0d47a1;
    color: white;
    font-weight: bold;
    border-radius: 5px 5px 0 0 !important;
}
.profile-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    margin: 20px auto;
    display: block;
    border: 3px solid #0d47a1;
}
.student-info {
    padding: 15px;
}
.student-info p {
    margin: 10px 0;
    font-size: 14px;
}
.student-info strong {
    color: #333;
}
.announcement-item {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
}
.announcement-item:last-child {
    border-bottom: none;
}
.announcement-header {
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}
.announcement-date {
    color: #666;
    font-size: 13px;
}
.rules-content {
    max-height: 500px;
    overflow-y: auto;
    padding: 15px;
}
.rules-header {
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #0d47a1;
}
.rules-header h5 {
    color: #0d47a1;
    font-weight: bold;
    margin: 0;
}
.rules-header h6 {
    color: #333;
    margin: 10px 0 0 0;
    font-size: 14px;
}
.nav-link {
    color: white !important;
    margin: 0 10px;
}
.nav-link:hover {
    text-decoration: underline;
}
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
                <a href="reservation.php" class="nav-link d-inline">Reservation</a>
                <a href="/ccs_sitin/logout.php" class="btn btn-warning btn-sm ms-2">Log out</a>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    <div class="row">
        <!-- Student Information -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-circle"></i> Student Information
                </div>
                <div class="student-info text-center">
                    <img src="/CCS_SITIN/uploads/<?= htmlspecialchars($student['profile_image'] ?? 'default.png') ?>" 
                         alt="Profile" class="profile-img">
                    <hr>
                    <p><strong><i class="bi bi-person"></i> Name:</strong> <?= htmlspecialchars($student['first_name'] . " " . $student['last_name']) ?></p>
                    <p><strong><i class="bi bi-mortarboard"></i> Course:</strong> <?= htmlspecialchars($student['course']) ?></p>
                    <p><strong><i class="bi bi-calendar"></i> Year:</strong> <?= htmlspecialchars($student['year'] ?? 'N/A') ?></p>
                    <p><strong><i class="bi bi-envelope"></i> Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
                    <p><strong><i class="bi bi-geo-alt"></i> Address:</strong> <?= htmlspecialchars($student['address']) ?></p>
                    <p><strong><i class="bi bi-clock"></i> Session:</strong> <?= htmlspecialchars($student['session'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <!-- Announcement -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-megaphone"></i> Announcement
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto; padding: 0;">
                    <?php if($announcements && mysqli_num_rows($announcements) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($announcements)): ?>
                            <div class="announcement-item">
                                <div class="announcement-header">
                                    CCS Admin | <?= date('Y-M-d', strtotime($row['date'])) ?>
                                </div>
                                <div class="announcement-content">
                                    <?= nl2br(htmlspecialchars($row['message'])) ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="announcement-item">
                            <p class="text-muted mb-0">No announcements yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Rules and Regulation -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    Rules and Regulation
                </div>
                <div class="rules-content">
                    <div class="rules-header">
                        <h5>University of Cebu</h5>
                        <h6>COLLEGE OF INFORMATION & COMPUTER STUDIES</h6>
                    </div>
                    <h6 class="mb-3"><strong>Laboratory Rules and Regulations</strong></h6>
                    <p style="font-size: 14px; text-align: justify;">
                        To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, 
                        please observe the following:
                    </p>
                    <ol style="font-size: 14px; padding-left: 20px;">
                        <li class="mb-2">Maintain silence, proper decorum, and discipline inside the laboratory. 
                        Mobile phones, walkmans and other personal pieces of equipment must be switched off.</li>
                        
                        <li class="mb-2">Games are not allowed inside the lab. This includes computer-related games, 
                        card games and other games that may disturb the operation of the lab.</li>
                        
                        <li class="mb-2">Surfing the Internet is allowed only with the permission of the instructor. 
                        Downloading and installing of software are strictly prohibited.</li>
                        
                        <li class="mb-2">Students must wear proper attire and ID while inside the laboratory.</li>
                        
                        <li class="mb-2">No eating and drinking inside the laboratory.</li>
                        
                        <li class="mb-2">Follow all instructions from the laboratory instructor or proctor.</li>
                        
                        <li class="mb-2">Be responsible and respectful at all times.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>