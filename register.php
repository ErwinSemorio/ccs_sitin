<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CCS Sit-In System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ccs_sitin/style.css">
    <style>
        /* Override login container for register (wider) */
        .login-container {
            max-width: 1100px;
            min-height: 650px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
        }

        .password-strength {
            height: 4px;
            background: rgba(100, 120, 160, 0.2);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
            display: none;
        }
        .password-strength.active { display: block; }
        .password-strength-bar { height: 100%; width: 0; transition: all 0.3s; border-radius: 2px; }
        .password-strength-bar.weak   { width: 33%; background: #ef4444; }
        .password-strength-bar.medium { width: 66%; background: #f59e0b; }
        .password-strength-bar.strong { width: 100%; background: #10b981; }

        .feature-list {
            list-style: none;
            width: 100%;
            margin-top: 2rem;
        }
        .feature-list li {
            padding: 0.6rem 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: rgba(255,255,255,0.85);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .feature-list li:last-child { border-bottom: none; }
        .feature-list i {
            width: 24px; height: 24px;
            background: rgba(0, 212, 255, 0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; color: var(--accent-cyan);
            flex-shrink: 0;
        }

        /* Scrollable right side for long form */
        .login-left {
            justify-content: center;
            padding: 50px 40px;
        }
        .login-right {
            flex: 1.3;
            overflow-y: auto;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .select-space {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
        }

        .loading {
            display: inline-block; width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%; border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="login-container">

        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="right-content" style="text-align: center;">
                <img src="/ccs_sitin/11.png" alt="CCS Logo" class="logo-image">
                <div class="right-text">
                    <h2>College of Computer Studies</h2>
                    <p>Sit-in Monitoring System</p>
                </div>
            </div>

            <ul class="feature-list">
                <li><i class="bi bi-check-lg"></i><span>Track your laboratory usage</span></li>
                <li><i class="bi bi-check-lg"></i><span>Reserve computer stations</span></li>
                <li><i class="bi bi-check-lg"></i><span>Monitor your session history</span></li>
                <li><i class="bi bi-check-lg"></i><span>Earn rewards and points</span></li>
                <li><i class="bi bi-check-lg"></i><span>Access learning resources</span></li>
            </ul>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="login-right">
            <div class="login-header">
                <h1 class="login-title">Create Account</h1>
                <p class="login-subtitle">Fill in your information to get started</p>
            </div>

            <?php
            if (isset($_GET['error'])) {
                $error_msg = match($_GET['error']) {
                    'exists'            => 'ID Number or Email already registered!',
                    'missing'           => 'Please fill in all required fields.',
                    'password_mismatch' => 'Passwords do not match!',
                    default             => 'Registration failed. Please try again.'
                };
                echo '<div class="alert"><i class="bi bi-exclamation-circle-fill"></i> ' . htmlspecialchars($error_msg) . '</div>';
            }
            if (isset($_GET['success'])) {
                echo '<div class="alert" style="background: rgba(16,185,129,0.15); border-color: rgba(16,185,129,0.3); color: #6ee7b7;"><i class="bi bi-check-circle-fill"></i> Registration successful! Please login.</div>';
            }
            ?>

            <form action="/ccs_sitin/process/register_process.php" method="POST" id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">ID Number <span style="color:#ef4444">*</span></label>
                        <input type="text" class="form-control" name="id_number" placeholder="e.g. 2021-00124" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color:#ef4444">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="your.email@example.com" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name <span style="color:#ef4444">*</span></label>
                        <input type="text" class="form-control" name="first_name" placeholder="Juan" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span style="color:#ef4444">*</span></label>
                        <input type="text" class="form-control" name="last_name" placeholder="Dela Cruz" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control" name="middle_name" placeholder="Santos (optional)">
                </div>

                <div class="form-group">
                    <label class="form-label">Course <span style="color:#ef4444">*</span></label>
                    <select class="form-control select-space" name="course" required>
                        <option value="">Select your course...</option>
                        <option value="BSIT">BSIT - Bachelor of Science in Information Technology</option>
                        <option value="BSCS">BSCS - Bachelor of Science in Computer Science</option>
                        <option value="BSIS">BSIS - Bachelor of Science in Information Systems</option>
                        <option value="ACT">ACT - Associate in Computer Technology</option>
                        <option value="BSCpE">BSCpE - Bachelor of Science in Computer Engineering</option>
                        <option value="BSECE">BSECE - Bachelor of Science in Electronics Engineering</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Address <span style="color:#ef4444">*</span></label>
                    <input type="text" class="form-control" name="address" placeholder="Complete address" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password <span style="color:#ef4444">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required minlength="6">
                            <button type="button" class="toggle-password" onclick="togglePass('password', 'eye1')">
                                <i id="eye1" class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password <span style="color:#ef4444">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="••••••••" required>
                            <button type="button" class="toggle-password" onclick="togglePass('confirm_password', 'eye2')">
                                <i id="eye2" class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    <i class="bi bi-person-plus"></i> Create Account
                </button>

                <div class="login-footer" style="margin-top: 1rem;">
                    Already have an account? <a href="/ccs_sitin/login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const val = this.value;
            const indicator = document.getElementById('passwordStrength');
            const bar = document.getElementById('strengthBar');

            if (!val.length) { indicator.classList.remove('active'); return; }
            indicator.classList.add('active');

            let strength = 0;
            if (val.length >= 6)  strength++;
            if (val.length >= 10) strength++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) strength++;
            if (/\d/.test(val)) strength++;
            if (/[^a-zA-Z0-9]/.test(val)) strength++;

            bar.className = 'password-strength-bar';
            if (strength <= 2) bar.classList.add('weak');
            else if (strength <= 4) bar.classList.add('medium');
            else bar.classList.add('strong');
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm  = document.getElementById('confirm_password').value;

            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters!');
                return false;
            }

            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<span class="loading"></span> Creating Account...';
            btn.disabled = true;
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(a => {
                a.style.transition = 'opacity 0.5s';
                a.style.opacity = '0';
                setTimeout(() => a.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>