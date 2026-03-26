
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ccs_sitin/login.php");
    exit();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CCS Admin | Sit-In Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:      #0f2044;
      --navy2:     #162b58;
      --navy3:     #1e3a70;
      --blue:      #1a56db;
      --blue-lt:   #e8f0fe;
      --teal:      #0694a2;
      --teal-lt:   #e0f5f5;
      --green:     #057a55;
      --green-lt:  #def7ec;
      --amber:     #d97706;
      --amber-lt:  #fef3c7;
      --red:       #e02424;
      --red-lt:    #fde8e8;
      --gold:      #f59e0b;
      --bg:        #f1f5fb;
      --surface:   #ffffff;
      --border:    #e2e8f4;
      --text:      #0f2044;
      --muted:     #64748b;
      --shadow:    0 4px 20px rgba(15,32,68,.10);
      --shadow-sm: 0 2px 8px rgba(15,32,68,.07);
    }

    html, body { min-height: 100%; background: var(--bg); color: var(--text); font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── TOPNAV ── */
    .topnav {
      position: sticky; top: 0; z-index: 100;
      background: var(--navy);
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 28px;
      height: 58px;
      box-shadow: 0 2px 20px rgba(0,0,0,.25);
    }
    .topnav-brand {
      font-size: 15px; font-weight: 800;
      color: #fff; letter-spacing: .3px;
      display: flex; align-items: center; gap: 10px;
    }
    .topnav-brand .dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: var(--gold);
      box-shadow: 0 0 8px var(--gold);
    }
    .topnav-links {
      display: flex; align-items: center; gap: 2px;
      list-style: none;
    }
    .topnav-links a {
      display: block; padding: 8px 13px;
      color: rgba(255,255,255,.7);
      text-decoration: none;
      font-size: 13px; font-weight: 600;
      border-radius: 8px;
      transition: all .18s;
      cursor: pointer;
    }
    .topnav-links a:hover, .topnav-links a.active {
      color: #fff;
      background: rgba(255,255,255,.12);
    }
    .topnav-links a.active { color: #fff; }
    .logout-btn-nav {
      padding: 7px 16px !important;
      background: var(--gold) !important;
      color: var(--navy) !important;
      font-weight: 800 !important;
      border-radius: 8px !important;
      margin-left: 6px;
    }
    .logout-btn-nav:hover { opacity: .88 !important; }

    /* ── PAGE WRAPPER ── */
    .page { padding: 28px 32px; max-width: 1300px; margin: 0 auto; animation: fadeUp .4s ease both; }

    /* ── SECTION HEADING ── */
    .section-heading {
      font-size: 22px; font-weight: 800;
      color: var(--text); margin-bottom: 22px;
      display: flex; align-items: center; gap: 10px;
    }
    .section-heading::before {
      content: '';
      width: 5px; height: 24px;
      background: var(--blue);
      border-radius: 3px;
    }

    /* ── CARDS ── */
    .card {
      background: var(--surface);
      border: 1.5px solid var(--border);
      border-radius: 16px;
      box-shadow: var(--shadow-sm);
    }
    .card-head {
      display: flex; align-items: center; gap: 9px;
      padding: 16px 20px;
      border-bottom: 1.5px solid var(--border);
      font-size: 14px; font-weight: 700;
      color: var(--navy);
    }
    .card-head .chip {
      width: 28px; height: 28px; border-radius: 8px;
      background: var(--blue-lt); color: var(--blue);
      display: flex; align-items: center; justify-content: center;
      font-size: 14px;
    }
    .card-body { padding: 20px; }

    /* ── HOME PAGE ── */
    .home-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }

    /* Stats block */
    .stat-row { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }
    .stat-item {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 16px;
      background: var(--bg);
      border-radius: 10px;
      border: 1.5px solid var(--border);
    }
    .stat-item .label { font-size: 13px; font-weight: 600; color: var(--muted); }
    .stat-item .val   { font-size: 20px; font-weight: 800; color: var(--navy); font-family: 'JetBrains Mono', monospace; }

    .chart-wrap { height: 220px; position: relative; }

    /* Announcement */
    .announce-form textarea {
      width: 100%; min-height: 80px;
      border: 1.5px solid var(--border);
      border-radius: 10px; padding: 10px 14px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px; color: var(--text);
      resize: vertical; outline: none;
      transition: border-color .18s;
      background: var(--bg);
    }
    .announce-form textarea:focus { border-color: var(--blue); }
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 9px 20px;
      border-radius: 9px; border: none;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px; font-weight: 700;
      cursor: pointer; transition: all .18s;
    }
    .btn-primary { background: var(--blue); color: #fff; }
    .btn-primary:hover { background: #1347c0; transform: translateY(-1px); }
    .btn-danger  { background: var(--red);  color: #fff; }
    .btn-danger:hover  { background: #c01d1d; }
    .btn-success { background: var(--green); color: #fff; }
    .btn-success:hover { background: #046343; }
    .btn-secondary { background: var(--bg); color: var(--muted); border: 1.5px solid var(--border); }
    .btn-secondary:hover { background: var(--border); }

    .posted-list { display: flex; flex-direction: column; gap: 0; }
    .posted-item {
      padding: 13px 0;
      border-bottom: 1.5px solid var(--border);
    }
    .posted-item:last-child { border-bottom: none; }
    .posted-meta { font-size: 11px; font-weight: 700; color: var(--blue); font-family: 'JetBrains Mono', monospace; margin-bottom: 5px; }
    .posted-text { font-size: 13px; color: var(--muted); line-height: 1.55; }

    /* ── TABLE ── */
    .table-toolbar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 14px 20px;
      border-bottom: 1.5px solid var(--border);
      gap: 12px; flex-wrap: wrap;
    }
    .table-toolbar-left { display: flex; align-items: center; gap: 10px; }
    .entries-select {
      padding: 6px 10px;
      border: 1.5px solid var(--border); border-radius: 8px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 12px; color: var(--text);
      background: var(--bg); outline: none; cursor: pointer;
    }
    .search-input {
      padding: 7px 14px;
      border: 1.5px solid var(--border); border-radius: 9px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px; color: var(--text);
      background: var(--bg); outline: none;
      transition: border-color .18s; width: 200px;
    }
    .search-input:focus { border-color: var(--blue); }

    table { width: 100%; border-collapse: collapse; }
    thead th {
      padding: 11px 16px;
      text-align: left;
      font-size: 11px; font-weight: 700;
      color: var(--muted); text-transform: uppercase;
      letter-spacing: 1px;
      background: var(--bg);
      border-bottom: 1.5px solid var(--border);
      cursor: pointer; user-select: none;
    }
    thead th:hover { color: var(--blue); }
    tbody tr { transition: background .13s; }
    tbody tr:hover { background: #f7f9ff; }
    tbody td {
      padding: 13px 16px;
      font-size: 13px; font-weight: 500;
      border-bottom: 1.5px solid var(--border);
      color: var(--text);
    }
    tbody tr:last-child td { border-bottom: none; }
    .td-mono { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--muted); }

    /* Badges */
    .badge {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 3px 10px; border-radius: 20px;
      font-size: 11px; font-weight: 700;
    }
    .badge-blue   { background: var(--blue-lt);  color: var(--blue); }
    .badge-green  { background: var(--green-lt); color: var(--green); }
    .badge-amber  { background: var(--amber-lt); color: var(--amber); }
    .badge-red    { background: var(--red-lt);   color: var(--red); }

    /* Action buttons in table */
    .action-btns { display: flex; gap: 6px; }
    .btn-sm {
      padding: 5px 13px; font-size: 12px; border-radius: 7px;
      border: none; font-family: 'Plus Jakarta Sans', sans-serif;
      font-weight: 700; cursor: pointer; transition: all .15s;
    }
    .btn-sm-blue  { background: var(--blue);  color: #fff; }
    .btn-sm-blue:hover  { background: #1347c0; }
    .btn-sm-red   { background: var(--red);   color: #fff; }
    .btn-sm-red:hover   { background: #c01d1d; }
    .btn-sm-teal  { background: var(--teal);  color: #fff; }
    .btn-sm-teal:hover  { background: #047481; }

    /* Table footer */
    .table-footer {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 20px;
      border-top: 1.5px solid var(--border);
      font-size: 12px; color: var(--muted); font-weight: 600;
    }
    .pagination { display: flex; gap: 4px; }
    .page-btn {
      width: 30px; height: 30px; border-radius: 7px;
      border: 1.5px solid var(--border);
      background: var(--surface); color: var(--muted);
      font-size: 12px; font-weight: 700;
      cursor: pointer; display: flex; align-items: center; justify-content: center;
      transition: all .15s;
    }
    .page-btn:hover, .page-btn.active { background: var(--blue); color: #fff; border-color: var(--blue); }

    /* ── MODAL ── */
    .modal-overlay {
      display: none; position: fixed; inset: 0; z-index: 200;
      background: rgba(15,32,68,.45);
      align-items: center; justify-content: center;
      backdrop-filter: blur(3px);
    }
    .modal-overlay.open { display: flex; animation: fadeIn .2s ease; }
    .modal {
      background: var(--surface);
      border-radius: 18px;
      box-shadow: 0 20px 60px rgba(15,32,68,.25);
      width: 100%; max-width: 460px;
      overflow: hidden;
      animation: slideUp .25s cubic-bezier(.22,1,.36,1);
    }
    .modal-head {
      display: flex; align-items: center; justify-content: space-between;
      padding: 18px 22px;
      background: var(--navy);
      color: #fff;
    }
    .modal-head h3 { font-size: 15px; font-weight: 800; }
    .modal-close {
      width: 28px; height: 28px; border-radius: 7px;
      background: rgba(255,255,255,.15);
      border: none; color: #fff;
      font-size: 16px; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: background .15s;
    }
    .modal-close:hover { background: rgba(255,255,255,.28); }
    .modal-body { padding: 22px; display: flex; flex-direction: column; gap: 14px; }
    .form-group label {
      display: block; font-size: 11px; font-weight: 700;
      color: var(--muted); text-transform: uppercase;
      letter-spacing: 1px; margin-bottom: 5px;
    }
    .form-group input, .form-group select {
      width: 100%; padding: 9px 14px;
      border: 1.5px solid var(--border); border-radius: 9px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px; color: var(--text);
      background: var(--bg); outline: none;
      transition: border-color .18s;
    }
    .form-group input:focus, .form-group select:focus { border-color: var(--blue); }
    .modal-footer {
      display: flex; justify-content: flex-end; gap: 8px;
      padding: 16px 22px;
      border-top: 1.5px solid var(--border);
    }

    /* ── HIDDEN/SHOWN PAGES ── */
    .page-section { display: none; }
    .page-section.active { display: block; }

    /* ── NO DATA ── */
    .no-data { text-align: center; padding: 36px; color: var(--muted); font-size: 13px; font-weight: 600; }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp  { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
    @keyframes fadeIn  { from { opacity:0; } to { opacity:1; } }
    @keyframes slideUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:none; } }

    @media (max-width: 900px) {
      .home-grid { grid-template-columns: 1fr; }
      .topnav-links { display: none; }
      .page { padding: 16px; }
    }
  </style>
</head>
<body>


<!-- ══ TOPNAV ══ -->
<nav class="topnav">
  <div class="topnav-brand">
    <div class="dot"></div>
    College of Computer Studies Admin
  </div>
  <ul class="topnav-links">
    <li><a onclick="showPage('home')"      class="active" id="nav-home">Home</a></li>
    <li><a onclick="showPage('search')"    id="nav-search">Search</a></li>
    <li><a onclick="showPage('students')"  id="nav-students">Students</a></li>
    <li><a onclick="showPage('sitin')"     id="nav-sitin">Sit-in</a></li>
    <li><a onclick="showPage('records')"   id="nav-records">View Sit-in Records</a></li>
    <li><a onclick="showPage('reports')"   id="nav-reports">Sit-in Reports</a></li>
    <li><a onclick="showPage('feedback')"  id="nav-feedback">Feedback Reports</a></li>
    <li><a onclick="showPage('reserve')"   id="nav-reserve">Reservation</a></li>
    <li><a href="/ccs_sitin/logout.php" class="logout-link">Log out</a></li>
  </ul>
</nav>

<!-- ══════════════════════════════════════════
     HOME PAGE
══════════════════════════════════════════ -->
<div class="page page-section active" id="page-home">
  <div class="section-heading">Dashboard Overview</div>
  <div class="home-grid">

    <!-- Statistics -->
    <div class="card">
      <div class="card-head"><span class="chip">📊</span> Statistics</div>
      <div class="card-body">
        <div class="stat-row">
          <div class="stat-item">
            <span class="label">Students Registered</span>
            <span class="val">38</span>
          </div>
          <div class="stat-item">
            <span class="label">Currently Sit-in</span>
            <span class="val">0</span>
          </div>
          <div class="stat-item">
            <span class="label">Total Sit-in</span>
            <span class="val">15</span>
          </div>
        </div>
        <div class="chart-wrap">
          <canvas id="purposeChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Announcement -->
    <div class="card">
      <div class="card-head"><span class="chip">📢</span> Announcement</div>
      <div class="card-body">
        <div class="announce-form">
          <textarea id="announceText" placeholder="New Announcement..."></textarea>
          <div style="margin-top:10px">
            <button class="btn btn-success" onclick="postAnnouncement()">Submit</button>
          </div>
        </div>
        <div style="margin-top:20px;font-size:15px;font-weight:800;color:var(--navy);margin-bottom:12px">Posted Announcements</div>
        <div class="posted-list" id="postedList">
          <div class="posted-item">
            <div class="posted-meta">CCS Admin | 2026-Feb-11</div>
          </div>
          <div class="posted-item">
            <div class="posted-meta">CCS Admin | 2024-May-08</div>
            <div class="posted-text">Important Announcement — We are excited to announce the launch of our new website! Explore our latest products and services now!</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     SEARCH PAGE
══════════════════════════════════════════ -->
<div class="page page-section" id="page-search">
  <div class="section-heading">Search Student</div>
  <div class="card" style="max-width:520px">
    <div class="card-head"><span class="chip">🔍</span> Find a Student</div>
    <div class="card-body">
      <div style="display:flex;gap:10px;">
        <input class="search-input" style="flex:1;width:auto" id="searchStudentInput" placeholder="Enter ID number or name..." oninput="filterSearchResults()"/>
        <button class="btn btn-primary" onclick="filterSearchResults()">Search</button>
      </div>
      <div id="searchResults" style="margin-top:16px"></div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     STUDENTS PAGE
══════════════════════════════════════════ -->
<div class="page page-section" id="page-students">
  <div class="section-heading">Students Information</div>
  <div class="card">
    <div class="table-toolbar">
      <div class="table-toolbar-left">
        <button class="btn btn-primary" onclick="openModal('addStudentModal')">＋ Add Students</button>
        <button class="btn btn-danger"  onclick="confirmResetSessions()">↺ Reset All Sessions</button>
        <label style="font-size:12px;font-weight:600;color:var(--muted);display:flex;align-items:center;gap:6px">
          <select class="entries-select"><option>10</option><option>25</option><option>50</option></select>
          entries per page
        </label>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:12px;font-weight:600;color:var(--muted)">Search:</span>
        <input class="search-input" id="studentsSearch" placeholder="Search..." oninput="renderStudentsTable()"/>
      </div>
    </div>
    <div style="overflow-x:auto">
      <table id="studentsTable">
        <thead>
          <tr>
            <th>ID Number ↕</th>
            <th>Name ↕</th>
            <th>Year Level ↕</th>
            <th>Course ↕</th>
            <th>Remaining Session ↕</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="studentsTbody"></tbody>
      </table>
    </div>
    <div class="table-footer">
      <span id="studentsInfo">Showing 1 to 6 of 6 entries</span>
      <div class="pagination">
        <button class="page-btn">«</button>
        <button class="page-btn active">1</button>
        <button class="page-btn">»</button>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     SIT-IN PAGE
══════════════════════════════════════════ -->
<div class="page page-section" id="page-sitin">
  <div class="section-heading">Current Sit-in</div>
  <div class="card">
    <div class="table-toolbar">
      <div class="table-toolbar-left">
        <button class="btn btn-primary" onclick="openSitInForm()">＋ New Sit-in</button>
        <label style="font-size:12px;font-weight:600;color:var(--muted);display:flex;align-items:center;gap:6px">
          <select class="entries-select"><option>10</option><option>25</option><option>50</option></select>
          entries per page
        </label>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:12px;font-weight:600;color:var(--muted)">Search:</span>
        <input class="search-input" placeholder="Search..."/>
      </div>
    </div>
    <div style="overflow-x:auto">
      <table>
        <thead>
          <tr>
            <th>Sit ID Number ↕</th>
            <th>ID Number ↕</th>
            <th>Name ↕</th>
            <th>Purpose ↕</th>
            <th>Sit Lab ↕</th>
            <th>Session ↕</th>
            <th>Status ↕</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="sitinTbody">
          <tr><td colspan="8" class="no-data">No data available</td></tr>
        </tbody>
      </table>
    </div>
    <div class="table-footer">
      <span>Showing 1 to 1 of 1 entry</span>
      <div class="pagination">
        <button class="page-btn">«</button>
        <button class="page-btn active">1</button>
        <button class="page-btn">»</button>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     VIEW SIT-IN RECORDS
══════════════════════════════════════════ -->
<div class="page page-section" id="page-records">
  <div class="section-heading">Sit-in Records</div>
  <div class="card">
    <div class="table-toolbar">
      <div class="table-toolbar-left">
        <label style="font-size:12px;font-weight:600;color:var(--muted);display:flex;align-items:center;gap:6px">
          <select class="entries-select"><option>10</option><option>25</option><option>50</option></select>
          entries per page
        </label>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:12px;font-weight:600;color:var(--muted)">Search:</span>
        <input class="search-input" placeholder="Search..."/>
      </div>
    </div>
    <div style="overflow-x:auto">
      <table>
        <thead>
          <tr>
            <th>Sit ID ↕</th><th>ID Number ↕</th><th>Name ↕</th>
            <th>Purpose ↕</th><th>Lab ↕</th><th>Session ↕</th>
            <th>Date ↕</th><th>Time In ↕</th><th>Time Out ↕</th><th>Status ↕</th>
          </tr>
        </thead>
        <tbody id="recordsTbody">
          <tr><td colspan="10" class="no-data">No records available</td></tr>
        </tbody>
      </table>
    </div>
    <div class="table-footer">
      <span>Showing 0 entries</span>
      <div class="pagination">
        <button class="page-btn">«</button>
        <button class="page-btn active">1</button>
        <button class="page-btn">»</button>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     SIT-IN REPORTS
══════════════════════════════════════════ -->
<div class="page page-section" id="page-reports">
  <div class="section-heading">Sit-in Reports</div>
  <div class="card">
    <div class="card-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div>
          <div style="font-size:13px;font-weight:700;color:var(--muted);margin-bottom:12px">Sessions by Purpose</div>
          <canvas id="reportBarChart" height="200"></canvas>
        </div>
        <div>
          <div style="font-size:13px;font-weight:700;color:var(--muted);margin-bottom:12px">Sessions by Lab</div>
          <canvas id="reportLineChart" height="200"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     FEEDBACK REPORTS
══════════════════════════════════════════ -->
<div class="page page-section" id="page-feedback">
  <div class="section-heading">Feedback Reports</div>
  <div class="card">
    <div class="table-toolbar">
      <div class="table-toolbar-left">
        <label style="font-size:12px;font-weight:600;color:var(--muted);display:flex;align-items:center;gap:6px">
          <select class="entries-select"><option>10</option><option>25</option><option>50</option></select>
          entries per page
        </label>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:12px;font-weight:600;color:var(--muted)">Search:</span>
        <input class="search-input" placeholder="Search..."/>
      </div>
    </div>
    <div style="overflow-x:auto">
      <table>
        <thead>
          <tr><th>ID Number ↕</th><th>Name ↕</th><th>Lab ↕</th><th>Date ↕</th><th>Feedback ↕</th><th>Rating ↕</th></tr>
        </thead>
        <tbody>
          <tr><td colspan="6" class="no-data">No feedback available</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     RESERVATION
══════════════════════════════════════════ -->
<div class="page page-section" id="page-reserve">
  <div class="section-heading">Reservations</div>
  <div class="card">
    <div class="table-toolbar">
      <div class="table-toolbar-left">
        <label style="font-size:12px;font-weight:600;color:var(--muted);display:flex;align-items:center;gap:6px">
          <select class="entries-select"><option>10</option><option>25</option><option>50</option></select>
          entries per page
        </label>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:12px;font-weight:600;color:var(--muted)">Search:</span>
        <input class="search-input" placeholder="Search..."/>
      </div>
    </div>
    <div style="overflow-x:auto">
      <table>
        <thead>
          <tr><th>Reservation ID ↕</th><th>ID Number ↕</th><th>Name ↕</th><th>Lab ↕</th><th>Date ↕</th><th>Time ↕</th><th>Status ↕</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <tr><td colspan="8" class="no-data">No reservations available</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>


<!-- ══ MODAL: SIT-IN FORM ══ -->
<div class="modal-overlay" id="sitInModal">
  <div class="modal">
    <div class="modal-head">
      <h3>🖥️ Sit In Form</h3>
      <button class="modal-close" onclick="closeModal('sitInModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>ID Number</label>
        <input id="si-id" placeholder="e.g. 3677937" oninput="autoFillName()"/>
      </div>
      <div class="form-group">
        <label>Student Name</label>
        <input id="si-name" placeholder="Auto-filled" readonly style="background:#f1f5fb"/>
      </div>
      <div class="form-group">
        <label>Purpose</label>
        <select id="si-purpose">
          <option value="">Select purpose...</option>
          <option>C Programming</option>
          <option>Java</option>
          <option>Web Development</option>
          <option>Database Systems</option>
          <option>ASP.Net</option>
          <option>PHP</option>
          <option>C#</option>
        </select>
      </div>
      <div class="form-group">
        <label>Lab</label>
        <select id="si-lab">
          <option value="">Select lab...</option>
          <option>521</option><option>522</option><option>523</option>
          <option>524</option><option>525</option><option>526</option>
        </select>
      </div>
      <div class="form-group">
        <label>Remaining Session</label>
        <input id="si-remaining" placeholder="Auto-filled" readonly style="background:#f1f5fb"/>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('sitInModal')">Close</button>
      <button class="btn btn-primary" onclick="submitSitIn()">Sit In</button>
    </div>
  </div>
</div>

<!-- ══ MODAL: ADD STUDENT ══ -->
<div class="modal-overlay" id="addStudentModal">
  <div class="modal">
    <div class="modal-head">
      <h3>👤 Add Student</h3>
      <button class="modal-close" onclick="closeModal('addStudentModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label>ID Number</label><input id="as-id" placeholder="e.g. 2021-00124"/></div>
      <div class="form-group"><label>First Name</label><input id="as-fname" placeholder="First name"/></div>
      <div class="form-group"><label>Last Name</label><input id="as-lname" placeholder="Last name"/></div>
      <div class="form-group">
        <label>Course</label>
        <select id="as-course">
          <option>BSIT</option><option>BSCS</option><option>ACT</option>
        </select>
      </div>
      <div class="form-group">
        <label>Year Level</label>
        <select id="as-year"><option>1</option><option>2</option><option>3</option><option>4</option></select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('addStudentModal')">Cancel</button>
      <button class="btn btn-primary" onclick="addStudent()">Add Student</button>
    </div>
  </div>
</div>

<!-- ══ MODAL: EDIT STUDENT ══ -->
<div class="modal-overlay" id="editStudentModal">
  <div class="modal">
    <div class="modal-head">
      <h3>✏️ Edit Student</h3>
      <button class="modal-close" onclick="closeModal('editStudentModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label>ID Number</label><input id="es-id" readonly style="background:#f1f5fb"/></div>
      <div class="form-group"><label>Name</label><input id="es-name"/></div>
      <div class="form-group"><label>Course</label><select id="es-course"><option>BSIT</option><option>BSCS</option><option>ACT</option></select></div>
      <div class="form-group"><label>Year Level</label><select id="es-year"><option>1</option><option>2</option><option>3</option><option>4</option></select></div>
      <div class="form-group"><label>Remaining Sessions</label><input id="es-remaining" type="number"/></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('editStudentModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveEditStudent()">Save Changes</button>
    </div>
  </div>
</div>

<!-- ══ MODAL: SEARCH SITIN FORM ══ -->
<div class="modal-overlay" id="searchSitInModal">
  <div class="modal">
    <div class="modal-head">
      <h3>🔍 Search Student</h3>
      <button class="modal-close" onclick="closeModal('searchSitInModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Search</label>
        <input id="searchModalInput" placeholder="Search..." oninput="filterSearchModal()"/>
      </div>
      <div id="searchModalResults"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('searchSitInModal')">Close</button>
      <button class="btn btn-primary" onclick="filterSearchModal()">Search</button>
    </div>
  </div>
</div>

<script>
// ── DATA ──
let students = [
  { id:'123',    name:'Kimmy K. Nengasca',        year:3, course:'BSIT', remaining:29 },
  { id:'1234',   name:'rasil rasil. rasil',        year:1, course:'BSIT', remaining:30 },
  { id:'2000',   name:'Jude Jefferson L. Sandalo', year:4, course:'BSIT', remaining:29 },
  { id:'123123', name:'Jermaine J. Aguilar',       year:3, course:'BSIT', remaining:30 },
  { id:'123456', name:'Jan v. Senador',             year:2, course:'BSIT', remaining:30 },
  { id:'3677937',name:'Jeff Pelorina. Salimbangon', year:4, course:'BSIT', remaining:27 },
];
let sitinRecords = [];
let editingId = null;

// ── NAV ──
function showPage(name) {
  document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.topnav-links a').forEach(a => a.classList.remove('active'));
  document.getElementById('page-' + name).classList.add('active');
  document.getElementById('nav-' + name).classList.add('active');
  if(name==='students') renderStudentsTable();
  if(name==='sitin') renderSitinTable();
  if(name==='records') renderRecordsTable();
  if(name==='reports') renderReportCharts();
}

// ── CHART: Home ──
window.addEventListener('load', () => {
  const ctx = document.getElementById('purposeChart').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['C#','C','Java','ASP.Net','PHP'],
      datasets: [{ data:[4,3,3,2,3], backgroundColor:['#6366f1','#ef4444','#f59e0b','#10b981','#3b82f6'], borderWidth:0 }]
    },
    options: { plugins:{ legend:{ position:'top', labels:{ font:{ family:'Plus Jakarta Sans', size:11 }, boxWidth:12 } } }, animation:{ duration:800 } }
  });
  renderStudentsTable();
});

// ── STUDENTS TABLE ──
function renderStudentsTable() {
  const q = (document.getElementById('studentsSearch')?.value||'').toLowerCase();
  const filtered = students.filter(s => s.name.toLowerCase().includes(q) || s.id.includes(q));
  const tbody = document.getElementById('studentsTbody');
  if(!tbody) return;
  tbody.innerHTML = filtered.length ? filtered.map(s => `
    <tr>
      <td class="td-mono">${s.id}</td>
      <td><strong>${s.name}</strong></td>
      <td>${s.year}</td>
      <td><span class="badge badge-blue">${s.course}</span></td>
      <td><span class="badge ${s.remaining<10?'badge-red':s.remaining<20?'badge-amber':'badge-green'}">${s.remaining}</span></td>
      <td>
        <div class="action-btns">
          <button class="btn-sm btn-sm-blue" onclick="openEditStudent('${s.id}')">Edit</button>
          <button class="btn-sm btn-sm-red"  onclick="deleteStudent('${s.id}')">Delete</button>
        </div>
      </td>
    </tr>`).join('') : `<tr><td colspan="6" class="no-data">No students found</td></tr>`;
  document.getElementById('studentsInfo').textContent = `Showing 1 to ${filtered.length} of ${filtered.length} entries`;
}

// ── ADD STUDENT ──
function addStudent() {
  const id = document.getElementById('as-id').value.trim();
  const fname = document.getElementById('as-fname').value.trim();
  const lname = document.getElementById('as-lname').value.trim();
  const course = document.getElementById('as-course').value;
  const year = parseInt(document.getElementById('as-year').value);
  if(!id||!fname||!lname){ alert('Please fill all fields.'); return; }
  students.push({ id, name: fname+' '+lname, year, course, remaining:30 });
  closeModal('addStudentModal');
  renderStudentsTable();
}

// ── EDIT STUDENT ──
function openEditStudent(id) {
  const s = students.find(x=>x.id===id);
  if(!s) return;
  editingId = id;
  document.getElementById('es-id').value = s.id;
  document.getElementById('es-name').value = s.name;
  document.getElementById('es-course').value = s.course;
  document.getElementById('es-year').value = s.year;
  document.getElementById('es-remaining').value = s.remaining;
  openModal('editStudentModal');
}
function saveEditStudent() {
  const s = students.find(x=>x.id===editingId);
  if(!s) return;
  s.name = document.getElementById('es-name').value;
  s.course = document.getElementById('es-course').value;
  s.year = parseInt(document.getElementById('es-year').value);
  s.remaining = parseInt(document.getElementById('es-remaining').value);
  closeModal('editStudentModal');
  renderStudentsTable();
}
function deleteStudent(id) {
  if(!confirm('Delete this student?')) return;
  students = students.filter(s=>s.id!==id);
  renderStudentsTable();
}
function confirmResetSessions() {
  if(!confirm('Reset all student sessions to 30?')) return;
  students.forEach(s=>s.remaining=30);
  renderStudentsTable();
}

// ── SIT-IN ──
function openSitInForm() {
  document.getElementById('si-id').value='';
  document.getElementById('si-name').value='';
  document.getElementById('si-remaining').value='';
  openModal('sitInModal');
}
function autoFillName() {
  const id = document.getElementById('si-id').value.trim();
  const s = students.find(x=>x.id===id);
  document.getElementById('si-name').value = s ? s.name : '';
  document.getElementById('si-remaining').value = s ? s.remaining : '';
}
function submitSitIn() {
  const id = document.getElementById('si-id').value.trim();
  const s = students.find(x=>x.id===id);
  if(!s){ alert('Student not found.'); return; }
  const purpose = document.getElementById('si-purpose').value;
  const lab = document.getElementById('si-lab').value;
  if(!purpose||!lab){ alert('Please select purpose and lab.'); return; }
  if(s.remaining<=0){ alert('No remaining sessions!'); return; }
  s.remaining--;
  const rec = {
    sitId: sitinRecords.length+1,
    studentId: s.id, name: s.name,
    purpose, lab, session: s.remaining,
    status:'Active',
    date: new Date().toLocaleDateString(),
    timeIn: new Date().toLocaleTimeString(), timeOut:'—'
  };
  sitinRecords.push(rec);
  closeModal('sitInModal');
  renderSitinTable();
}
function renderSitinTable() {
  const active = sitinRecords.filter(r=>r.status==='Active');
  const tbody = document.getElementById('sitinTbody');
  if(!tbody) return;
  tbody.innerHTML = active.length ? active.map(r=>`
    <tr>
      <td class="td-mono">${r.sitId}</td>
      <td class="td-mono">${r.studentId}</td>
      <td><strong>${r.name}</strong></td>
      <td>${r.purpose}</td>
      <td>Lab ${r.lab}</td>
      <td><span class="badge badge-blue">${r.session}</span></td>
      <td><span class="badge badge-amber">Active</span></td>
      <td>
        <div class="action-btns">
          <button class="btn-sm btn-sm-teal" onclick="timeOut(${r.sitId})">Time Out</button>
        </div>
      </td>
    </tr>`).join('') : `<tr><td colspan="8" class="no-data">No data available</td></tr>`;
}
function timeOut(sitId) {
  const r = sitinRecords.find(x=>x.sitId===sitId);
  if(r){ r.status='Done'; r.timeOut=new Date().toLocaleTimeString(); }
  renderSitinTable();
  renderRecordsTable();
}

// ── RECORDS TABLE ──
function renderRecordsTable() {
  const tbody = document.getElementById('recordsTbody');
  if(!tbody) return;
  tbody.innerHTML = sitinRecords.length ? sitinRecords.map(r=>`
    <tr>
      <td class="td-mono">${r.sitId}</td>
      <td class="td-mono">${r.studentId}</td>
      <td>${r.name}</td>
      <td>${r.purpose}</td>
      <td>Lab ${r.lab}</td>
      <td>${r.session}</td>
      <td>${r.date}</td>
      <td>${r.timeIn}</td>
      <td>${r.timeOut}</td>
      <td><span class="badge ${r.status==='Done'?'badge-green':r.status==='Missed'?'badge-red':'badge-amber'}">${r.status}</span></td>
    </tr>`).join('') : `<tr><td colspan="10" class="no-data">No records available</td></tr>`;
}

// ── REPORT CHARTS ──
let reportChartsInit = false;
function renderReportCharts() {
  if(reportChartsInit) return;
  reportChartsInit = true;
  new Chart(document.getElementById('reportBarChart'), {
    type:'bar',
    data:{
      labels:['C Programming','Java','Web Dev','Database','ASP.Net','PHP','C#'],
      datasets:[{ label:'Sessions', data:[4,3,2,3,2,3,1], backgroundColor:'#1a56db', borderRadius:6 }]
    },
    options:{ plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}, animation:{duration:800} }
  });
  new Chart(document.getElementById('reportLineChart'), {
    type:'line',
    data:{
      labels:['Lab 521','Lab 522','Lab 523','Lab 524','Lab 525','Lab 526'],
      datasets:[{ label:'Sessions', data:[2,1,3,5,2,2], borderColor:'#0694a2', backgroundColor:'rgba(6,148,162,.12)', tension:.4, fill:true }]
    },
    options:{ plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}, animation:{duration:800} }
  });
}

// ── SEARCH ──
function filterSearchResults() {
  const q = document.getElementById('searchStudentInput').value.toLowerCase();
  const div = document.getElementById('searchResults');
  if(!q){ div.innerHTML=''; return; }
  const res = students.filter(s=>s.name.toLowerCase().includes(q)||s.id.includes(q));
  div.innerHTML = res.length ?
    `<table style="width:100%;border-collapse:collapse;margin-top:4px">
      <thead><tr><th style="padding:9px 12px;text-align:left;font-size:11px;color:var(--muted);border-bottom:1.5px solid var(--border)">ID</th><th style="padding:9px 12px;text-align:left;font-size:11px;color:var(--muted);border-bottom:1.5px solid var(--border)">Name</th><th style="padding:9px 12px;text-align:left;font-size:11px;color:var(--muted);border-bottom:1.5px solid var(--border)">Course</th><th style="padding:9px 12px;text-align:left;font-size:11px;color:var(--muted);border-bottom:1.5px solid var(--border)">Year</th><th style="padding:9px 12px;text-align:left;font-size:11px;color:var(--muted);border-bottom:1.5px solid var(--border)">Sessions</th><th style="padding:9px 12px;text-align:left;font-size:11px;color:var(--muted);border-bottom:1.5px solid var(--border)">Action</th></tr></thead>
      <tbody>${res.map(s=>`<tr><td style="padding:11px 12px;font-family:'JetBrains Mono',monospace;font-size:12px;border-bottom:1px solid var(--border)">${s.id}</td><td style="padding:11px 12px;font-size:13px;border-bottom:1px solid var(--border)">${s.name}</td><td style="padding:11px 12px;font-size:13px;border-bottom:1px solid var(--border)">${s.course}</td><td style="padding:11px 12px;font-size:13px;border-bottom:1px solid var(--border)">${s.year}</td><td style="padding:11px 12px;font-size:13px;border-bottom:1px solid var(--border)">${s.remaining}</td><td style="padding:11px 12px;border-bottom:1px solid var(--border)"><button class="btn-sm btn-sm-blue" onclick="selectForSitIn('${s.id}')">Sit In</button></td></tr>`).join('')}</tbody>
    </table>` : `<div style="color:var(--muted);font-size:13px;padding:12px 0">No students found.</div>`;
}
function selectForSitIn(id) {
  showPage('sitin');
  openSitInForm();
  document.getElementById('si-id').value = id;
  autoFillName();
}

// ── ANNOUNCEMENT ──
function postAnnouncement() {
  const text = document.getElementById('announceText').value.trim();
  if(!text) return;
  const now = new Date();
  const dateStr = now.toISOString().slice(0,10).replace(/-/g,'-').replace(/(\d{4})-(\d{2})-(\d{2})/,'$1-$2-$3');
  const div = document.createElement('div');
  div.className = 'posted-item';
  div.innerHTML = `<div class="posted-meta">CCS Admin | ${dateStr}</div><div class="posted-text">${text}</div>`;
  document.getElementById('postedList').prepend(div);
  document.getElementById('announceText').value='';
}

// ── MODALS ──
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); });
});
</script>
</body>
</html>