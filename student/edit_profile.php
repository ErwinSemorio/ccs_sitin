<?php
// Prevent session_start() errors
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}

include __DIR__ . "/../config/database.php";

$id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM students WHERE id_number='$id'");
$student = mysqli_fetch_assoc($result);

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $last_name = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $middle_name = trim(mysqli_real_escape_string($conn, $_POST['middle_name']));
    $course_level = trim($_POST['course_level']);
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $course = trim(mysqli_real_escape_string($conn, $_POST['course']));
    $address = trim(mysqli_real_escape_string($conn, $_POST['address']));

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
        $result = mysqli_query($conn, "SELECT * FROM students WHERE id_number='$id'");
        $student = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Error updating profile: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | CCS Sit-In</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary: #0d47a1;
            --primary-dark: #08347a;
            --bg-body: #f3f4f8;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --radius-lg: 16px;
        }
        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
        }
        .navbar-custom {
            background-color: var(--primary);
            box-shadow: 0 4px 12px rgba(13,71,161,0.2);
            padding: 0.8rem 0;
        }
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem !important;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
        }
        .btn-logout {
            background: #ffc107;
            color: var(--primary-dark);
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 0.4rem 1rem;
            transition: transform 0.2s;
        }
        .btn-logout:hover {
            background: #ffca2c;
            transform: translateY(-2px);
        }
        .custom-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }
        .card-header-custom {
            padding: 1.25rem;
            font-weight: 700;
            font-size: 1.1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #eff6ff;
            color: var(--primary);
        }
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.6rem 1rem;
            border: 1px solid var(--border-color);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13,71,161,0.1);
        }
        .form-control[readonly] {
            background-color: #f8fafc;
        }
        .btn-save {
            background: var(--primary);
            color: white;
            border-radius: 10px;
            padding: 0.7rem 2rem;
            font-weight: 600;
            border: none;
            transition: all 0.2s;
        }
        .btn-save:hover {
            background: var(--primary-dark);
            color: white;
        }
        .alert {
            border-radius: 10px;
            padding: 0.9rem 1.25rem;
        }
        .illustration {
            text-align: center;
            padding: 2rem;
        }
        .illustration svg {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <!-- Single Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">🎓 CCS Sit-In</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell me-1"></i>Notification</a></li>
                    <li class="nav-item"><a class="nav-link active" href="edit_profile.php"><i class="bi bi-pencil-square me-1"></i>Edit Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="history.php"><i class="bi bi-clock-history me-1"></i>History</a></li>
                    <li class="nav-item"><a class="nav-link" href="reservation.php"><i class="bi bi-calendar-check me-1"></i>Reservation</a></li>
                    <li class="nav-item ms-2"><a href="/ccs_sitin/logout.php" class="btn-logout btn">Log out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="custom-card">
                    <div class="card-header-custom">
                        <i class="bi bi-pencil-square"></i> Edit Profile
                    </div>
                    <div class="p-4">
                        <?php if($success_message): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i><?= $success_message ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($error_message): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error_message ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label"><i class="bi bi-hash me-1"></i>ID Number</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['id_number']) ?>" readonly>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label"><i class="bi bi-person me-1"></i>First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label"><i class="bi bi-person me-1"></i>Last Name</label>
                                    <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label"><i class="bi bi-person me-1"></i>Middle Name</label>
                                    <input type="text" class="form-control" name="middle_name" value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-mortarboard me-1"></i>Course</label>
                                    <input type="text" class="form-control" name="course" value="<?= htmlspecialchars($student['course']) ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-book me-1"></i>Year Level</label>
                                    <select class="form-select" name="course_level" required>
                                        <option value="">Select Year Level</option>
                                        <option value="1" <?= (isset($student['year']) && $student['year'] == '1') ? 'selected' : '' ?>>1st Year</option>
                                        <option value="2" <?= (isset($student['year']) && $student['year'] == '2') ? 'selected' : '' ?>>2nd Year</option>
                                        <option value="3" <?= (isset($student['year']) && $student['year'] == '3') ? 'selected' : '' ?>>3rd Year</option>
                                        <option value="4" <?= (isset($student['year']) && $student['year'] == '4') ? 'selected' : '' ?>>4th Year</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label"><i class="bi bi-geo-alt me-1"></i>Address</label>
                                    <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($student['address']) ?>" required>
                                </div>
                                
                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-save">
                                        <i class="bi bi-check-lg me-1"></i>Save Changes
                                    </button>
                                    <a href="dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>