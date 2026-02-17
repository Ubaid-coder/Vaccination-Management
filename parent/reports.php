<?php
session_start();
include("../config/db.php");

// Logic: Check if parent is logged in
if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logic: Get parent_id from parents table using session user_id
$stmt = $conn->prepare("SELECT id FROM parents WHERE user_id=?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$parent){
    die("Parent record not found");
}

$parent_id = $parent['id'];

// Logic: Fetch detailed reports with JOINs to get Child, Hospital, and Vaccine names
$stmt = $conn->prepare("
SELECT 
    vaccination_reports.status AS report_status,
    vaccination_reports.remarks,
    vaccination_reports.report_date,
    children.child_name,
    hospitals.hospital_name,
    vaccines.vaccine_name
FROM vaccination_reports
JOIN bookings ON vaccination_reports.booking_id = bookings.id
JOIN children ON bookings.child_id = children.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
WHERE bookings.parent_id = ?
ORDER BY vaccination_reports.report_date DESC
");
$stmt->execute([$parent_id]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Reports - VaxManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root { --sidebar-color: #2c3e50; --accent-color: #667eea; }
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-color); position: fixed; color: white; transition: 0.3s; }
        .nav-link { color: #bdc3c7; padding: 15px 25px; transition: 0.3s; border-radius: 10px; margin: 5px 15px; text-decoration: none; display: block;}
        .nav-link:hover, .nav-link.active { background: var(--accent-color); color: white; }
        .main-content { margin-left: 260px; padding: 40px; }
        
        /* UI Enhancements */
        .report-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .table thead { background-color: #f8fafc; }
        .table thead th { color: #64748b; font-weight: 600; font-size: 0.85rem; border: none; padding: 1.25rem; }
        .table tbody td { padding: 1.25rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .status-badge { padding: 6px 12px; border-radius: 8px; font-weight: 500; font-size: 0.85rem; }
        .remarks-box { max-width: 250px; font-size: 0.9rem; color: #475569; font-style: italic; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="p-4"><h4 class="fw-bold"><i class="bi bi-shield-check me-2"></i>VaxManager</h4></div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="./dashboard.php"><i class="bi bi-people me-3"></i>My Children</a></li>
            <li class="nav-item"><a class="nav-link" href="./add_child.php"><i class="bi bi-person-plus me-3"></i>Add Child</a></li>
            <li class="nav-item"><a class="nav-link" href="./book_hospital.php"><i class="bi bi-hospital me-3"></i>Book Hospital</a></li>
            <li class="nav-item"><a class="nav-link" href="./my_booking.php"><i class="bi bi-calendar-check me-3"></i>My Booking</a></li>
            <li class="nav-item"><a class="nav-link active" href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a></li>
            <li class="nav-item mt-5"><a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i>Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold">Vaccination Reports</h2>
            <p class="text-muted">Detailed history of administered vaccines and doctor remarks.</p>
        </div>

        <div class="card report-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table hover mb-0">
                        <thead>
                            <tr>
                                <th>Child Name</th>
                                <th>Hospital</th>
                                <th>Vaccine</th>
                                <th>Status</th>
                                <th>Doctor Remarks</th>
                                <th>Report Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($reports) > 0): ?>
                                <?php foreach($reports as $r): ?>
                                <tr>
                                    <td class="fw-semibold text-primary"><?= htmlspecialchars($r['child_name']) ?></td>
                                    <td><i class="bi bi-building me-2 text-muted"></i><?= htmlspecialchars($r['hospital_name']) ?></td>
                                    <td><span class="badge bg-blue-subtle text-primary border border-primary-subtle px-2 py-1"><?= htmlspecialchars($r['vaccine_name']) ?></span></td>
                                    <td>
                                        <?php 
                                            $status = strtolower($r['report_status']);
                                            $badgeClass = ($status == 'vaccinated') ? 'bg-success' : 'bg-info';
                                        ?>
                                        <span class="badge <?= $badgeClass ?> status-badge">
                                            <?= ucfirst($r['report_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="remarks-box text-truncate" title="<?= htmlspecialchars($r['remarks']) ?>">
                                            "<?= htmlspecialchars($r['remarks']) ?>"
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('M d, Y', strtotime($r['report_date'])) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-clipboard-x fs-1 mb-3"></i>
                                            <p>No vaccination reports available yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 alert alert-info border-0 rounded-4 d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
            <div>
                <strong>Medical Record:</strong> Ye reports aapke bache ki vaccination history hain. Aap inka print out hospital mein dikha sakte hain.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>