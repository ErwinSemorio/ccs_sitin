<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: /ccs_sitin/admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'student') {
        header("Location: /ccs_sitin/student/dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Login Form -->
        <div class="login-left">
            <div class="login-content">
                <div class="login-header">
                    <h1 class="login-title">Welcome Back</h1>
                    <p class="login-subtitle">Enter your credentials to access the system</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                <div class="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?php
                    if ($_GET['error'] == 'invalid_password') echo "Invalid password.";
                    if ($_GET['error'] == 'not_found') echo "ID number not found.";
                    ?>
                </div>
                <?php endif; ?>

                <form action="/ccs_sitin/process/login_process.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">ID Number</label>
                        <input type="text" name="id_number" class="form-control" placeholder="Enter your ID" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Enter your password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i id="eyeIcon" class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">Sign In</button>
                </form>

                <div class="login-footer">
                    <p>Don't have an account? <a href="/ccs_sitin/register.php">Register here</a></p>
                </div>
            </div>
        </div>

        <!-- Right Side - Logo/Illustration -->
        <div class="login-right">
            <div class="right-content">
                <!-- Make sure 11.png is in the same folder or correct path -->
                <img src="11.png" alt="CCS Logo" class="logo-image">
                <div class="right-text">
                    <h2>College of Computer Studies</h2>
                    <p>Sit-in Monitoring System</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        var input = document.getElementById("passwordInput");
        var icon  = document.getElementById("eyeIcon");
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("bi-eye", "bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("bi-eye-slash", "bi-eye");
        }
    }
    </script>
</body>
</html>