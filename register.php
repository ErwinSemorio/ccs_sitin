<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CCS Sit-In System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary: #0d47a1;
            --primary-dark: #08347a;
            --primary-light: #1a56db;
            --bg-gradient-start: #1e3a8a;
            --bg-gradient-end: #0f172a;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.2);
            --radius-xl: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background circles */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            animation: float 20s infinite ease-in-out;
        }

        .bg-circle:nth-child(1) {
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 300px;
            height: 300px;
            bottom: -50px;
            right: -50px;
            animation-delay: 5s;
        }

        .bg-circle:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 50%;
            right: 10%;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }

        .register-container {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 1100px;
            overflow: hidden;
            display: flex;
            position: relative;
            z-index: 1;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .register-left::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            top: -50%;
            left: -50%;
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255,255,255,0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .brand-logo i {
            font-size: 60px;
        }

        .register-left h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 15px;
            text-align: center;
        }

        .register-left p {
            font-size: 15px;
            opacity: 0.9;
            text-align: center;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .feature-list {
            list-style: none;
            width: 100%;
            max-width: 300px;
        }

        .feature-list li {
            padding: 12px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }

        .feature-list i {
            width: 24px;
            height: 24px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .register-right {
            flex: 1.2;
            padding: 60px 50px;
            background: white;
        }

        .form-header {
            margin-bottom: 35px;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: #ef4444;
            margin-left: 2px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 13px 16px 13px 48px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(13, 71, 161, 0.1);
        }

        .form-control:focus + i,
        .input-wrapper:focus-within i {
            color: var(--primary);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .password-strength {
            height: 4px;
            background: var(--border-color);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
            display: none;
        }

        .password-strength.active {
            display: block;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .password-strength-bar.weak { width: 33%; background: #ef4444; }
        .password-strength-bar.medium { width: 66%; background: #f59e0b; }
        .password-strength-bar.strong { width: 100%; background: #10b981; }

        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(13, 71, 161, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 71, 161, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .login-link a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
        }

        .login-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        @media (max-width: 900px) {
            .register-left {
                display: none;
            }
            
            .register-right {
                padding: 40px 30px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .register-right {
                padding: 30px 20px;
            }
            
            .form-header h2 {
                font-size: 24px;
            }
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Background decorative circles -->
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>

    <div class="register-container">
        <!-- Left Side - Branding -->
        <div class="register-left">
            <div class="brand-logo">
                <i class="bi bi-laptop"></i>
            </div>
            <h1>CCS Sit-In System</h1>
            <p>Join our community and manage your laboratory sessions efficiently</p>
            
            <ul class="feature-list">
                <li>
                    <i class="bi bi-check-lg"></i>
                    <span>Track your laboratory usage</span>
                </li>
                <li>
                    <i class="bi bi-check-lg"></i>
                    <span>Reserve computer stations</span>
                </li>
                <li>
                    <i class="bi bi-check-lg"></i>
                    <span>Monitor your session history</span>
                </li>
                <li>
                    <i class="bi bi-check-lg"></i>
                    <span>Earn rewards and points</span>
                </li>
                <li>
                    <i class="bi bi-check-lg"></i>
                    <span>Access learning resources</span>
                </li>
            </ul>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="register-right">
            <div class="form-header">
                <h2>Create Account</h2>
                <p>Fill in your information to get started</p>
            </div>

            <?php
            // Show error/success messages from URL parameters
            if (isset($_GET['error'])) {
                $error_msg = '';
                switch($_GET['error']) {
                    case 'exists':
                        $error_msg = 'ID Number or Email already registered!';
                        break;
                    case 'missing':
                        $error_msg = 'Please fill in all required fields.';
                        break;
                    case 'password_mismatch':
                        $error_msg = 'Passwords do not match!';
                        break;
                    default:
                        $error_msg = 'Registration failed. Please try again.';
                }
                echo '<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill"></i> ' . htmlspecialchars($error_msg) . '</div>';
            }
            
            if (isset($_GET['success'])) {
                echo '<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> Registration successful! Please login.</div>';
            }
            ?>

            <form action="/ccs_sitin/process/register_process.php" method="POST" id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>ID Number <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control" name="id_number" placeholder="e.g. 2021-00124" required>
                            <i class="bi bi-hash"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" class="form-control" name="email" placeholder="your.email@example.com" required>
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>First Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control" name="first_name" placeholder="Juan" required>
                            <i class="bi bi-person"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Last Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control" name="last_name" placeholder="Dela Cruz" required>
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Middle Name</label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="middle_name" placeholder="Santos">
                        <i class="bi bi-person"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Course <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select class="form-control" name="course" required style="cursor: pointer;">
                            <option value="">Select your course</option>
                            <option value="BSIT">BSIT - Bachelor of Science in Information Technology</option>
                            <option value="BSCS">BSCS - Bachelor of Science in Computer Science</option>
                            <option value="BSIS">BSIS - Bachelor of Science in Information Systems</option>
                            <option value="ACT">ACT - Associate in Computer Technology</option>
                            <option value="BSCpE">BSCpE - Bachelor of Science in Computer Engineering</option>
                            <option value="BSECE">BSECE - Bachelor of Science in Electronics Engineering</option>
                        </select>
                        <i class="bi bi-mortarboard"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Address <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="address" placeholder="Complete address" required>
                        <i class="bi bi-geo-alt"></i>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required minlength="6">
                            <i class="bi bi-lock"></i>
                        </div>
                        <div class="password-strength" id="passwordStrength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" class="form-control" name="confirm_password" placeholder="••••••••" required>
                            <i class="bi bi-lock-fill"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-register" id="submitBtn">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>

                <div class="login-link">
                    Already have an account? <a href="/ccs_sitin/login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthIndicator = document.getElementById('passwordStrength');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                strengthIndicator.classList.remove('active');
                return;
            }
            
            strengthIndicator.classList.add('active');
            
            let strength = 0;
            
            // Length check
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            
            // Character variety
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            // Update UI
            strengthBar.className = 'password-strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<span class="loading"></span> Creating Account...';
            submitBtn.disabled = true;
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>