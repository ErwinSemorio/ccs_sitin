<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
$id = $_SESSION['user_id'];

// Fetch current student data
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id_number = ?");
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['profile_photo'])) {
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

// Get profile photo
$profile_photo = $student['profile_photo'] ?? 'default.jpg';
$photo_path = "/ccs_sitin/uploads/profiles/" . $profile_photo;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | CCS Sit-In</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Space Theme CSS -->
    <link rel="stylesheet" href="/ccs_sitin/space-theme.css">
    <style>
        .page-container { max-width: 800px; margin: 0 auto; padding: 2rem; }
        .section-title { font-size: 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; gap: 0.75rem; }
        .section-title::before { content: ''; width: 4px; height: 24px; background: var(--accent-cyan); border-radius: 2px; }
        
        /* Photo Upload Area */
        .photo-container {
            text-align: center; margin-bottom: 2rem; padding: 2rem;
            background: rgba(10, 15, 30, 0.4); border-radius: var(--radius);
            border: 1px solid var(--space-border);
        }
        .photo-preview {
            width: 150px; height: 150px; border-radius: 50%; object-fit: cover;
            border: 4px solid var(--accent-cyan); margin-bottom: 1rem;
            box-shadow: var(--shadow-glow);
        }
        .photo-label {
            display: inline-block; padding: 0.7rem 1.8rem; background: var(--accent-blue);
            color: #fff; border-radius: var(--radius-sm); cursor: pointer; font-weight: 600;
            transition: var(--transition);
        }
        .photo-label:hover { background: var(--accent-purple); transform: translateY(-2px); }
        #photoInput { display: none; }
        .photo-hint { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem; }
        
        /* Form Grid */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        @media(max-width: 700px) { .form-grid { grid-template-columns: 1fr; } }
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
                <a href="edit_profile.php" class="nav-link-space active">Edit Profile</a>
                <a href="history.php" class="nav-link-space">History</a>
                <a href="reservation.php" class="nav-link-space">Reservation</a>
                <a href="/ccs_sitin/logout.php" class="btn-space btn-space-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Log out</a>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <h2 class="section-title">✏️ Edit Profile</h2>
        
        <div class="glass-card fade-in-space">
            <div style="padding: 1.5rem;">
                <?php if($success_message): ?>
                    <div class="alert-space alert-space-success"><i class="bi bi-check-circle-fill"></i> <?= $success_message ?></div>
                <?php endif; ?>
                <?php if($error_message): ?>
                    <div class="alert-space alert-space-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error_message ?></div>
                <?php endif; ?>

                <!-- Profile Photo Upload -->
                <div class="photo-container">
                    <img src="<?= file_exists(__DIR__ . '/../uploads/profiles/' . $profile_photo) ? $photo_path : 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\' viewBox=\'0 0 150 150\'%3E%3Crect fill=\'%231e293b\' width=\'150\' height=\'150\'/%3E%3Ccircle cx=\'75\' cy=\'60\' r=\'30\' fill=\'%2300d4ff\'/%3E%3Cpath d=\'M75 100 Q40 100 40 115 L40 125 L110 125 L110 115 Q110 100 75 100\' fill=\'%2300d4ff\'/%3E%3C/svg%3E' ?>" 
                         alt="Profile" 
                         class="photo-preview" 
                         id="photoPreview">
                    <div>
                        <label for="photoInput" class="photo-label"><i class="bi bi-camera me-2"></i>Change Photo</label>
                        <input type="file" id="photoInput" accept="image/*">
                    </div>
                    <p class="photo-hint"><i class="bi bi-info-circle me-1"></i> JPG, PNG or GIF (Max 5MB)</p>
                    <div id="uploadStatus"></div>
                </div>

                <!-- Edit Form -->
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group-space" style="grid-column: 1 / -1;">
                            <label class="form-label-space"><i class="bi bi-hash me-1"></i>ID Number</label>
                            <input type="text" class="form-control-space" value="<?= htmlspecialchars($student['id_number']) ?>" readonly style="opacity: 0.6; cursor: not-allowed;">
                        </div>
                        <div class="form-group-space">
                            <label class="form-label-space"><i class="bi bi-person me-1"></i>First Name</label>
                            <input type="text" class="form-control-space" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required>
                        </div>
                        <div class="form-group-space">
                            <label class="form-label-space"><i class="bi bi-person me-1"></i>Last Name</label>
                            <input type="text" class="form-control-space" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required>
                        </div>
                        <div class="form-group-space">
                            <label class="form-label-space"><i class="bi bi-person me-1"></i>Middle Name</label>
                            <input type="text" class="form-control-space" name="middle_name" value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
                        </div>
                        <div class="form-group-space">
                            <label class="form-label-space"><i class="bi bi-mortarboard me-1"></i>Course</label>
                            <input type="text" class="form-control-space" name="course" value="<?= htmlspecialchars($student['course']) ?>" required>
                        </div>
                        <div class="form-group-space">
                            <label class="form-label-space"><i class="bi bi-book me-1"></i>Year Level</label>
                            <select class="form-control-space" name="course_level" required>
                                <option value="">Select...</option>
                                <option value="1" <?= ($student['year'] ?? '') == '1' ? 'selected' : '' ?>>1st Year</option>
                                <option value="2" <?= ($student['year'] ?? '') == '2' ? 'selected' : '' ?>>2nd Year</option>
                                <option value="3" <?= ($student['year'] ?? '') == '3' ? 'selected' : '' ?>>3rd Year</option>
                                <option value="4" <?= ($student['year'] ?? '') == '4' ? 'selected' : '' ?>>4th Year</option>
                            </select>
                        </div>
                        <div class="form-group-space" style="grid-column: 1 / -1;">
                            <label class="form-label-space"><i class="bi bi-envelope me-1"></i>Email</label>
                            <input type="email" class="form-control-space" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                        </div>
                        <div class="form-group-space" style="grid-column: 1 / -1;">
                            <label class="form-label-space"><i class="bi bi-geo-alt me-1"></i>Address</label>
                            <input type="text" class="form-control-space" name="address" value="<?= htmlspecialchars($student['address']) ?>" required>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                        <a href="dashboard.php" class="btn-space btn-space-secondary">Cancel</a>
                        <button type="submit" class="btn-space btn-space-primary"><i class="bi bi-check-lg"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 🔹 Profile Photo Upload Logic
        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');
        const uploadStatus = document.getElementById('uploadStatus');

        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            if (!file.type.startsWith('image/')) {
                uploadStatus.innerHTML = '<div class="alert-space alert-space-danger mt-3"><i class="bi bi-exclamation-triangle"></i> Invalid file type. Only images allowed.</div>';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                uploadStatus.innerHTML = '<div class="alert-space alert-space-danger mt-3"><i class="bi bi-exclamation-triangle"></i> File too large. Max 5MB.</div>';
                return;
            }
            
            // Preview
            const reader = new FileReader();
            reader.onload = (ev) => photoPreview.src = ev.target.result;
            reader.readAsDataURL(file);
            
            // Upload via AJAX
            const formData = new FormData();
            formData.append('profile_photo', file);
            
            uploadStatus.innerHTML = '<div class="alert-space alert-space-info mt-3"><i class="bi bi-hourglass-split"></i> Uploading...</div>';
            
            fetch('/ccs_sitin/process/add_profile.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    uploadStatus.innerHTML = '<div class="alert-space alert-space-success mt-3"><i class="bi bi-check-circle"></i> ' + data.message + '</div>';
                    setTimeout(() => location.reload(), 1500);
                } else {
                    uploadStatus.innerHTML = '<div class="alert-space alert-space-danger mt-3"><i class="bi bi-exclamation-triangle"></i> ' + data.message + '</div>';
                }
            })
            .catch(() => {
                uploadStatus.innerHTML = '<div class="alert-space alert-space-danger mt-3"><i class="bi bi-exclamation-triangle"></i> Upload failed.</div>';
            });
        });
    </script>
</body>
</html>