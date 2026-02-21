<?php
session_start();
include("../config/db.php");
include('./check_auth.php');

// convert user_id -> hospital_id
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM hospitals WHERE user_id=?");
$stmt->execute([$user_id]);
$hospital = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$hospital){
    die("Hospital not found.");
}

$hospital_id = $hospital['id'];

// insert report
if (isset($_POST['add_report'])) {
    $conn->prepare("
        INSERT INTO vaccination_reports (booking_id,status,remarks,report_date)
        VALUES (?,?,?,?)
    ")->execute([
        $_POST['booking_id'],
        $_POST['status'],
        $_POST['remarks'],
        date('Y-m-d')
    ]);
}

// fetch reports
$stmt = $conn->prepare("
SELECT vr.*, c.child_name, v.vaccine_name
FROM vaccination_reports vr
JOIN bookings b ON vr.booking_id = b.id
JOIN children c ON b.child_id = c.id
JOIN vaccines v ON b.vaccine_id = v.id
WHERE b.hospital_id = ?
");
$stmt->execute([$hospital_id]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports - VaxManager</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --sidebar-color: #1e293b;
    --accent-color: #3b82f6;
}
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8fafc;
}
.sidebar {
    width: 260px;
    height: 100vh;
    background: var(--sidebar-color);
    position: fixed;
    color: white;
}
.nav-link {
    color: #94a3b8;
    padding: 15px 25px;
    margin: 5px 15px;
    border-radius: 10px;
    transition: 0.3s;
}
.nav-link:hover,
.nav-link.active {
    background: var(--accent-color);
    color: white;
}
.main-content {
    margin-left: 260px;
    padding: 40px;
}
</style>
</head>

<body>

<div class="sidebar d-flex flex-column p-3">
    <h3 class="text-center fw-bold mb-4">VaxManager</h3>
    <a href="dashboard.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="view_appointments.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i> Appointments</a>
    <a class="nav-link active" href="reports.php"><i class="bi bi-file-earmark-medical me-3"></i> Reports</a>
    <a href="../auth/logout.php" class="nav-link text-danger mt-auto">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
</div>

<div class="main-content">
    <h2 class="mb-4">Vaccination Reports</h2>

    <?php if (count($reports) > 0): ?>
        <table class="table table-bordered bg-white shadow-sm">
            <tr>
                <th>Child</th>
                <th>Vaccine</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php foreach ($reports as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['child_name']) ?></td>
                    <td><?= htmlspecialchars($r['vaccine_name']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td><?= htmlspecialchars($r['report_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No reports found.</div>
    <?php endif; ?>
</div>

</body>
</html>