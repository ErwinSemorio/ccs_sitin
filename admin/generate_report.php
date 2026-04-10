<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ccs_sitin/login.php");
    exit();
}
include __DIR__ . "/../config/database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report | CCS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        :root {
            --navy: #0f2044; --blue: #1a56db; --blue-lt: #e8f0fe;
            --green: #057a55; --green-lt: #def7ec; --amber: #d97706; --amber-lt: #fef3c7;
            --red: #e02424; --gold: #f59e0b;
            --bg: #f1f5fb; --surface: #ffffff; --border: #e2e8f4;
            --text: #0f2044; --muted: #64748b; --shadow: 0 4px 20px rgba(15,32,68,.10);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; }
        .topnav { background: var(--navy); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; height: 60px; box-shadow: 0 2px 20px rgba(0,0,0,.25); }
        .topnav-brand { color: #fff; font-weight: 800; font-size: 16px; display: flex; align-items: center; gap: 10px; }
        .topnav-links { display: flex; gap: 4px; list-style: none; }
        .topnav-links a { color: rgba(255,255,255,.7); text-decoration: none; padding: 8px 14px; font-size: 13px; font-weight: 600; border-radius: 8px; transition: .2s; }
        .topnav-links a:hover, .topnav-links a.active { color: #fff; background: rgba(255,255,255,.15); }
        .btn-logout { background: var(--gold); color: var(--navy); font-weight: 800; border-radius: 8px; padding: 7px 16px; text-decoration: none; font-size: 13px; }
        .page { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .section-title { font-size: 22px; font-weight: 800; color: var(--navy); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .section-title::before { content: ''; width: 5px; height: 24px; background: var(--blue); border-radius: 3px; }
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); padding: 30px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
        .form-control, .form-select { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 14px; outline: none; }
        .form-control:focus, .form-select:focus { border-color: var(--blue); }
        .btn { padding: 10px 20px; border-radius: 10px; border: none; font-weight: 700; font-size: 14px; cursor: pointer; transition: .2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--blue); color: #fff; }
        .btn-primary:hover { background: #1347c0; }
        .btn-success { background: var(--green); color: #fff; }
        .btn-success:hover { background: #046343; }
        .btn-danger { background: var(--red); color: #fff; }
        .btn-danger:hover { background: #c01d1d; }
        .btn-info { background: #17a2b8; color: #fff; }
        .btn-info:hover { background: #138496; }
        .report-preview { background: var(--bg); border: 1.5px solid var(--border); border-radius: 12px; padding: 20px; margin-top: 20px; min-height: 200px; }
    </style>
</head>
<body>
    <nav class="topnav">
        <div class="topnav-brand">🎓 CCS Admin</div>
        <ul class="topnav-links">
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="students.php">Students</a></li>
            <li><a href="sitin.php">Sit-in</a></li>
            <li><a href="generate_report.php" class="active">Generate Report</a></li>
            <li><a href="/ccs_sitin/logout.php" class="btn-logout">Log out</a></li>
        </ul>
    </nav>

    <div class="page">
        <div class="section-title">📊 Generate Report</div>
        
        <div class="card">
            <h5 class="fw-bold mb-4">Report Configuration</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Report Type</label>
                        <select class="form-select" id="reportType">
                            <option value="sitin">Sit-in Records</option>
                            <option value="students">Student List</option>
                            <option value="feedback">Feedback Reports</option>
                            <option value="attendance">Attendance Summary</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="d-flex gap-2">
                            <input type="date" class="form-control" id="dateFrom">
                            <input type="date" class="form-control" id="dateTo">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Export Format</label>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="exportCSV()"><i class="bi bi-file-earmark-spreadsheet"></i> CSV</button>
                    <button class="btn btn-success" onclick="exportDOC()"><i class="bi bi-file-earmark-word"></i> DOC</button>
                    <button class="btn btn-danger" onclick="exportPDF()"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                </div>
            </div>
        </div>

        <div class="card">
            <h5 class="fw-bold mb-3">Report Preview</h5>
            <div id="reportPreview" class="report-preview">
                <p class="text-muted text-center">Select report type and click generate to preview</p>
            </div>
        </div>
    </div>

    <script>
        function exportCSV() {
            const type = document.getElementById('reportType').value;
            let csv = 'ID,Name,Course,Date,Details\n';
            
            if (type === 'sitin') {
                csv += '1,John Doe,BSIT,2024-01-15,Programming\n';
                csv += '2,Jane Smith,BSCS,2024-01-16,Research\n';
            } else if (type === 'students') {
                csv += '2021-001,John Doe,BSIT,2021-06-01\n';
                csv += '2021-002,Jane Smith,BSCS,2021-06-01\n';
            }
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `report_${type}_${new Date().toISOString().slice(0,10)}.csv`;
            a.click();
        }

        function exportDOC() {
            const type = document.getElementById('reportType').value;
            const content = `
                <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word'>
                <head><meta charset='utf-8'><title>Report</title></head>
                <body>
                    <h1>CCS Sit-in Report - ${type.toUpperCase()}</h1>
                    <p>Generated: ${new Date().toLocaleDateString()}</p>
                    <table border='1'><tr><th>ID</th><th>Name</th><th>Details</th></tr>
                    <tr><td>1</td><td>John Doe</td><td>Sample Data</td></tr>
                    </table>
                </body>
                </html>
            `;
            
            const blob = new Blob(['\ufeff', content], { type: 'application/msword' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `report_${type}_${new Date().toISOString().slice(0,10)}.doc`;
            a.click();
        }

        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const type = document.getElementById('reportType').value;
            
            doc.setFontSize(20);
            doc.text(`CCS Sit-in Report - ${type.toUpperCase()}`, 20, 20);
            doc.setFontSize(12);
            doc.text(`Generated: ${new Date().toLocaleDateString()}`, 20, 30);
            doc.text('Sample Report Data', 20, 40);
            
            doc.save(`report_${type}_${new Date().toISOString().slice(0,10)}.pdf`);
        }

        // Preview report when type changes
        document.getElementById('reportType').addEventListener('change', function() {
            const preview = document.getElementById('reportPreview');
            preview.innerHTML = '<div class="text-center text-muted">Loading preview...</div>';
            
            setTimeout(() => {
                preview.innerHTML = `
                    <table class="table table-striped">
                        <thead><tr><th>#</th><th>ID Number</th><th>Name</th><th>Course</th><th>Date</th></tr></thead>
                        <tbody>
                            <tr><td>1</td><td>2021-001</td><td>John Doe</td><td>BSIT</td><td>2024-01-15</td></tr>
                            <tr><td>2</td><td>2021-002</td><td>Jane Smith</td><td>BSCS</td><td>2024-01-16</td></tr>
                            <tr><td>3</td><td>2021-003</td><td>Mike Johnson</td><td>ACT</td><td>2024-01-17</td></tr>
                        </tbody>
                    </table>
                `;
            }, 500);
        });
    </script>
</body>
</html>