<?php
session_start();
include("../config/db.php");
include('./check_auth.php');

$hospital_id = $_SESSION['user_id'];


$bookings = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE hospital_id=?");
$bookings->execute([$hospital_id]);

$vaccines = $conn->prepare("SELECT COUNT(*) FROM vaccines ");
$vaccines->execute();

$reports = $conn->prepare("SELECT COUNT(*) FROM vaccination_reports");
$reports->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Dashboard - VaxManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --sidebar-color: #1e293b; --accent-color: #3b82f6; }
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-color); position: fixed; color: white; }
        .nav-link { color: #94a3b8; padding: 15px 25px; margin: 5px 15px; border-radius: 10px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: var(--accent-color); color: white; }
        .main-content { margin-left: 260px; padding: 40px; }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h3 class="text-center fw-bold mb-4">VaxManager</h3>
    <a href="dashboard.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="appointments.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i> Appointments</a>
    <a class="nav-link" href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a>
    <a href="../auth/logout.php" class="nav-link text-danger mt-auto"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="main-content">
    <h2 class="fw-bold mb-4">Hospital Overview</h2>
    <p class="text-muted">Welcome back, <strong><?= htmlspecialchars($hospital['hospital_name'] ?? 'Hospital') ?></strong></p>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card stat-card bg-primary text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Approved Bookings</h6>
                        <h2 class="mb-0 fw-bold"><?= $appointment_count ?></h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
                <a href="view_appointments.php" class="text-white mt-3 d-inline-block text-decoration-none small">View all →</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>