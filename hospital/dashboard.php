<?php
session_start();
include("../config/db.php");
include("./check_auth.php"); // hospital auth

$hospital_user_id = $_SESSION['user_id'];


// TOTAL BOOKINGS (for this hospital)
$stmt = $conn->prepare("
SELECT COUNT(*) 
FROM bookings
JOIN hospitals ON bookings.hospital_id = hospitals.id
WHERE hospitals.user_id = ?
");
$stmt->execute([$hospital_user_id]);
$totalBookings = $stmt->fetchColumn();


// PENDING BOOKINGS
$stmt = $conn->prepare("
SELECT COUNT(*) 
FROM bookings
JOIN hospitals ON bookings.hospital_id = hospitals.id
WHERE hospitals.user_id = ? AND bookings.status = 'pending'
");
$stmt->execute([$hospital_user_id]);
$pendingBookings = $stmt->fetchColumn();


// VACCINATED TODAY
$stmt = $conn->prepare("
SELECT COUNT(*) 
FROM vaccination_reports
JOIN bookings ON vaccination_reports.booking_id = bookings.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
WHERE hospitals.user_id = ?
AND vaccination_reports.status = 'vaccinated'
AND vaccination_reports.report_date = CURDATE()
");
$stmt->execute([$hospital_user_id]);
$vaccinatedToday = $stmt->fetchColumn();


// TOTAL VACCINES (global table)
$stmt = $conn->prepare("SELECT COUNT(*) FROM vaccines");
$stmt->execute();
$totalVaccines = $stmt->fetchColumn();


// RECENT BOOKINGS (last 5)
$stmt = $conn->prepare("
SELECT 
    bookings.*,
    parents.name AS parent_name,
    children.child_name,
    vaccines.vaccine_name
FROM bookings
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN parents ON bookings.parent_id = parents.id
JOIN children ON bookings.child_id = children.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
WHERE hospitals.user_id = ?
ORDER BY bookings.id DESC
LIMIT 5
");
$stmt->execute([$hospital_user_id]);
$recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Hospital Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <a href="dashboard.php " class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="manage_vaccines.php" class="nav-link "><i class="bi bi-calendar-event me-2"></i> Vaccines</a>
    <a href="appointments.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i> Appointments</a>
    <a class="nav-link " href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a>
    <a href="../auth/logout.php" class="nav-link text-danger mt-auto"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="main-content">
    <h2 class="mb-4">Hospital Dashboard</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3">
                <h5>Total Bookings</h5>
                <h2><?= $totalBookings ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3">
                <h5>Pending Bookings</h5>
                <h2><?= $pendingBookings ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white p-3">
                <h5>Vaccinated Today</h5>
                <h2><?= $vaccinatedToday ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white p-3">
                <h5>Total Vaccines</h5>
                <h2><?= $totalVaccines ?></h2>
            </div>
        </div>
    </div>

    <h4>Recent Appointments</h4>

    <?php if(count($recentBookings) > 0): ?>
    <table class="table table-bordered">
        <tr>
            <th>Parent</th>
            <th>Child</th>
            <th>Vaccine</th>
            <th>Date</th>
            <th>Status</th>
        </tr>

        <?php foreach($recentBookings as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['parent_name']) ?></td>
            <td><?= htmlspecialchars($b['child_name']) ?></td>
            <td><?= htmlspecialchars($b['vaccine_name']) ?></td>
            <td><?= $b['appointment_date'] ?></td>
            <td><?= $b['status'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p class="alert alert-info">No recent bookings.</p>
    <?php endif; ?>

</div>

</body>
</html>