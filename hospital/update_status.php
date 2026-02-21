<?php
session_start();
include("../config/db.php");
include("./check_auth.php"); // hospital auth check

$hospital_id = $_SESSION['user_id'];

if(isset($_POST['update_status'])){

    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    // Update booking status
    $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
    $stmt->execute([$status, $booking_id]);

    // Insert into vaccination_reports
    $stmt2 = $conn->prepare("
        INSERT INTO vaccination_reports (booking_id, status, remarks, report_date)
        VALUES (?,?,?,?)
    ");
    $stmt2->execute([
        $booking_id,
        $status,
        $remarks,
        date('Y-m-d')
    ]);

    echo "<div class='alert alert-success'>Status Updated Successfully</div>";
}

// Fetch bookings of this hospital
$stmt = $conn->prepare("
SELECT 
    bookings.*,
    parents.name AS parent_name,
    children.child_name,
    vaccines.vaccine_name,
    hospitals.hospital_name
FROM bookings
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN parents ON bookings.parent_id = parents.id
JOIN children ON bookings.child_id = children.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
JOIN users ON hospitals.user_id = users.id
WHERE hospitals.user_id = ?
");
$stmt->execute([$hospital_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<head>
<title>Update Vaccination Status</title>
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
    <a href="manage_vaccines.php" class="nav-link active"><i class="bi bi-calendar-event me-2"></i> Vaccines</a>
    <a href="appointments.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i> Appointments</a>
    <a class="nav-link " href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a>
    <a href="../auth/logout.php" class="nav-link text-danger mt-auto"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="container mt-5">
<h2>Update Vaccination Status</h2>

<?php if(count($bookings) > 0): ?>
<table class="table table-bordered">
<tr>
    <th>Parent</th>
    <th>Child</th>
    <th>Vaccine</th>
    <th>Appointment Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach($bookings as $b): ?>
<tr>
    <td><?= $b['parent_name'] ?></td>
    <td><?= $b['child_name'] ?></td>
    <td><?= $b['vaccine_name'] ?></td>
    <td><?= $b['appointment_date'] ?></td>
    <td><?= $b['status'] ?></td>
    <td>
        <form method="post" class="d-flex gap-2">
            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">

            <select name="status" class="form-select" required>
                <option value="">Select</option>
                <option value="vaccinated">Vaccinated</option>
                <option value="not_vaccinated">Not Vaccinated</option>
                <option value="rejected">Rejected</option>
            </select>

            <input type="text" name="remarks" class="form-control" placeholder="Remarks">

            <button name="update_status" class="btn btn-success btn-sm">Update</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php else: ?>
<p class="alert alert-info">No bookings found.</p>
<?php endif; ?>

</div>

