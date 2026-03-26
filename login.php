<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/admin/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CCS Sit-in Monitoring System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/CCS_SITIN/style.css">
</head>
<body>

<div class="container mt-5" style="max-width: 450px;">
    <div class="card p-4 shadow-sm">
        <h2 class="mb-4 text-center">CCS Sit-in Login</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                    if ($_GET['error'] == 'invalid_password') echo "Invalid password. Please try again.";
                    if ($_GET['error'] == 'not_found') echo "Student ID not found.";
                ?>
            </div>
        <?php endif; ?>

        <form action="/ccs_sitin/process/login_process.php" method="POST">

            <div class="mb-3">
                <label class="form-label">ID Number</label>
                <input type="text" name="id_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordInput" class="form-control" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                        <i id="eyeIcon" class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="d-grid">
                <button class="btn btn-primary">Login</button>
            </div>

        </form>

        <div class="text-center mt-3">
            <a href="/CCS_SITIN/register.php">Don't have an account? Register</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<!DOCTYPE html>
<html>
<head>

<title>CCS Sit-in Monitoring System</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="/CCS_SITIN/style.css">


</head>

<body>

<div class="container mt-5">

<h2>Login</h2>

<form action="process/login_process.php" method="POST">

    <label>ID Number</label>
    <input type="text" name="id_number" class="form-control mb-3">

    <label>Password</label>
    <div class="input-group">
        <input type="password" name="password" id="passwordInput" class="form-control">
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
            <i id="eyeIcon" class="bi bi-eye"></i>
        </button>
    </div>

    <br>

    <button class="btn btn-primary">Login</button>

</form>

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

<br>

<a href="register.php">Register</a>

</div>

</body>
</html>