<?php
session_start();
include("../config/db.php");
include("./check_auth.php"); // hospital auth

$hospital_user_id = $_SESSION['user_id'];


// DELETE REPORT
if(isset($_GET['delete'])){
    $report_id = $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE vaccination_reports 
        FROM vaccination_reports
        JOIN bookings ON vaccination_reports.booking_id = bookings.id
        JOIN hospitals ON bookings.hospital_id = hospitals.id
        WHERE vaccination_reports.id = ? AND hospitals.user_id = ?
    ");
    $stmt->execute([$report_id, $hospital_user_id]);

    echo "<div class='alert alert-success'>Report Deleted Successfully</div>";
}


// FETCH REPORTS
$stmt = $conn->prepare("
SELECT 
    vaccination_reports.*,
    parents.name AS parent_name,
    children.child_name,
    vaccines.vaccine_name
FROM vaccination_reports
JOIN bookings ON vaccination_reports.booking_id = bookings.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN parents ON bookings.parent_id = parents.id
JOIN children ON bookings.child_id = children.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
WHERE hospitals.user_id = ?
ORDER BY vaccination_reports.report_date DESC
");
$stmt->execute([$hospital_user_id]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Hospital Reports</title>
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
    <a href="dashboard.php " class="nav-link "><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="manage_vaccines.php" class="nav-link "><i class="bi bi-calendar-event me-2"></i> Vaccines</a>
    <a href="appointments.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i> Appointments</a>
    <a class="nav-link active" href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a>
    <a href="../auth/logout.php" class="nav-link text-danger mt-auto"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="container mt-5">
<h2>Vaccination Reports</h2>

<?php if(count($reports) > 0): ?>
<table class="table table-bordered">
<tr>
    <th>Parent</th>
    <th>Child</th>
    <th>Vaccine</th>
    <th>Status</th>
    <th>Remarks</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php foreach($reports as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['parent_name']) ?></td>
    <td><?= htmlspecialchars($r['child_name']) ?></td>
    <td><?= htmlspecialchars($r['vaccine_name']) ?></td>
    <td><?= htmlspecialchars($r['status']) ?></td>
    <td><?= htmlspecialchars($r['remarks']) ?></td>
    <td><?= $r['report_date'] ?></td>
    <td>
        <a href="?delete=<?= $r['id'] ?>" 
           onclick="return confirm('Delete this report?')" 
           class="btn btn-danger btn-sm">
           Delete
        </a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php else: ?>
<p class="alert alert-info">No reports found.</p>
<?php endif; ?>

</div>

</body>
</html>