<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "hospital"){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get hospital_id
$stmt = $conn->prepare("SELECT id FROM hospitals WHERE user_id=?");
$stmt->execute([$user_id]);
$hospital = $stmt->fetch(PDO::FETCH_ASSOC);
$hospital_id = $hospital['id'];

// Fetch approved bookings
$stmt = $conn->prepare("
    SELECT bookings.*, parents.name, children.child_name, vaccines.vaccine_name
    FROM bookings
    JOIN parents ON bookings.parent_id = parents.id
    JOIN children ON bookings.child_id = children.id
    JOIN vaccines ON bookings.vaccine_id = vaccines.id
    WHERE bookings.hospital_id = ?
");
$stmt->execute([$hospital_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments - VaxManager</title>
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
    <a href="dashboard.php" class="nav-link "><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="view_appointments.php" class="nav-link active"><i class="bi bi-calendar-event me-2"></i> Appointments</a>
    <a class="nav-link" href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a>
    <a href="../auth/logout.php" class="nav-link text-danger mt-auto"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="main-content">
    <h2 class="fw-bold mb-4">Approved Appointments</h2>
    <div class="card p-4">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Parent</th>
                    <th>Child</th>
                    <th>Vaccine</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($appointments as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['name']) ?></td>
                    <td><?= htmlspecialchars($a['child_name']) ?></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($a['vaccine_name']) ?></span></td>
                    <td><?= $a['appointment_date'] ?></td>
                    <td><span class="badge bg-warning text-dark"><?= $a['status'] ?></span></td>
                    <td>
                        <a href="update_status.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-primary">Update Status</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>