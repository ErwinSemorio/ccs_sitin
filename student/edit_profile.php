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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $course_level = mysqli_real_escape_string($conn, $_POST['course_level']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $update_query = "UPDATE students SET 
        last_name='$last_name',
        first_name='$first_name',
        middle_name='$middle_name',
        year='$course_level',
        email='$email',
        course='$course',
        address='$address'
        WHERE id_number='$id'";
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Profile updated successfully!";
        // Refresh student data
        $result = mysqli_query($conn, "SELECT * FROM students WHERE id_number='$id'");
        $student = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Error updating profile: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Profile | CCS Student</title>
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
    margin-bottom: 30px;
}
.nav-link {
    color: white !important;
    margin: 0 10px;
}
.nav-link:hover {
    text-decoration: underline;
}
.profile-container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 40px;
}
.form-title {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
    font-weight: bold;
}
.form-label {
    font-weight: 500;
    color: #555;
    margin-top: 10px;
}
.form-control {
    margin-bottom: 15px;
}
.form-control[readonly] {
    background-color: #e9ecef;
}
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}
.illustration {
    text-align: center;
    margin-top: 30px;
}
.illustration img {
    max-width: 300px;
    height: auto;
}
.btn-save {
    background-color: #0d47a1;
    color: white;
    padding: 10px 30px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    margin-top: 20px;
}
.btn-save:hover {
    background-color: #0a367a;
}
.alert {
    margin-bottom: 20px;
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
<div class="container mt-4">
    <div class="profile-container">
        <h2 class="form-title">Edit Profile</h2>
        
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-7">
                    <!-- ID Number (Readonly) -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person"></i> ID Number</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student['id_number']) ?>" readonly>
                    </div>
                    
                    <!-- Last Name -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Last Name</label>
                        <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required>
                    </div>
                    
                    <!-- First Name -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> First Name</label>
                        <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required>
                    </div>
                    
                    <!-- Middle Name -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
                    </div>
                    
                    <!-- Course Level -->
                   <div class="mb-3">
                    <label class="form-label"><i class="bi bi-mortarboard"></i> Course Level</label>
                    <select class="form-select" name="course_level" required>
                        <option value="">Select Year Level</option>
                        <option value="1" <?= (isset($student['year']) && $student['year'] == '1') ? 'selected' : '' ?>>1st Year</option>
                        <option value="2" <?= (isset($student['year']) && $student['year'] == '2') ? 'selected' : '' ?>>2nd Year</option>
                        <option value="3" <?= (isset($student['year']) && $student['year'] == '3') ? 'selected' : '' ?>>3rd Year</option>
                        <option value="4" <?= (isset($student['year']) && $student['year'] == '4') ? 'selected' : '' ?>>4th Year</option>
                    </select>
                </div>
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                    </div>
                    
                    <!-- Course -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Course</label>
                        <input type="text" class="form-control" name="course" value="<?= htmlspecialchars($student['course']) ?>" required>
                    </div>
                    
                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Address</label>
                        <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($student['address']) ?>" required>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-save">Save</button>
                        <a href="student_dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <!-- Illustration -->
                    <div class="illustration">
                        <svg width="300" height="350" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                            <!-- Person illustration -->
                            <circle cx="200" cy="100" r="50" fill="#667eea"/>
                            <ellipse cx="200" cy="200" rx="70" ry="90" fill="#764ba2"/>
                            <rect x="150" y="280" width="30" height="80" rx="10" fill="#f093fb"/>
                            <rect x="220" y="280" width="30" height="80" rx="10" fill="#f093fb"/>
                            
                            <!-- Phone/Device -->
                            <rect x="280" y="80" width="90" height="140" rx="10" fill="#4facfe" stroke="#00f2fe" stroke-width="2"/>
                            <circle cx="325" cy="130" r="25" fill="#fff"/>
                            <circle cx="325" cy="125" r="15" fill="#667eea"/>
                            <rect x="300" y="165" width="50" height="8" rx="4" fill="#fff"/>
                            <rect x="300" y="178" width="50" height="8" rx="4" fill="#fff"/>
                            <rect x="310" y="200" width="30" height="12" rx="6" fill="#f093fb"/>
                            
                            <!-- Decorative elements -->
                            <circle cx="100" cy="300" r="20" fill="#43e97b" opacity="0.3"/>
                            <circle cx="350" cy="350" r="30" fill="#fa709a" opacity="0.3"/>
                            <circle cx="50" cy="150" r="15" fill="#4facfe" opacity="0.3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>