<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /CCS_SITIN/index.php");
    exit();
}
include __DIR__ . "/../config/database.php";

// Fetch all students
$result = mysqli_query($conn, "SELECT * FROM students ORDER BY last_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Students List - CCS Sit-in</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/CCS_SITIN/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-wrapper {
            padding: 30px;
        }

        .card-table {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            color: #fff;
        }

        .card-table h3 {
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .table {
            color: #fff;
        }

        .table thead {
            background: rgba(255, 255, 255, 0.2);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .table td, .table th {
            border-color: rgba(255, 255, 255, 0.15);
            vertical-align: middle;
        }

        .badge-course {
            background: rgba(255, 126, 179, 0.8);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .btn-back {
            background: rgba(255,255,255,0.2);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            margin-bottom: 20px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.35);
            color: #fff;
        }

        .search-box {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 10px;
            color: #fff;
            padding: 8px 15px;
            width: 250px;
        }

        .search-box::placeholder {
            color: rgba(255,255,255,0.6);
        }

        .search-box:focus {
            outline: none;
            background: rgba(255,255,255,0.25);
            color: #fff;
        }

        .total-badge {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="page-wrapper">

    <!-- Back Button -->
    <a href="/ccs_sitin/admin/dashboard.php" class="btn-back">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>

    <div class="card-table">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-people-fill me-2"></i>Students List</h3>
            <span class="total-badge">
                Total: <?php echo mysqli_num_rows($result); ?> students
            </span>
        </div>

        <!-- Search -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="search-box" placeholder="Search student...">
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover" id="studentsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID Number</th>
                        <th>Full Name</th>
                        <th>Course</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Date Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['id_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name']); ?></td>
                        <td><span class="badge-course"><?php echo htmlspecialchars($row['course']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="7" class="text-center">No students registered yet.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Search Script -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    var input  = this.value.toLowerCase();
    var rows   = document.querySelectorAll("#studentsTable tbody tr");

    rows.forEach(function (row) {
        var text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>