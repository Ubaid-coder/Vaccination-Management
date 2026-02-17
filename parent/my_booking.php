<?php
session_start();
include("../config/db.php");

// Logic: Check if parent is logged in
if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logic: Get parent_id from parents table
$stmt = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$parent) { die("Parent record not found."); }
$parent_id = $parent['id'];

// Logic: Fetch all bookings with Joined data (Child, Hospital, and Vaccine names)
$stmt = $conn->prepare("
    SELECT bookings.*, children.child_name, hospitals.hospital_name, vaccines.vaccine_name
    FROM bookings
    JOIN children ON bookings.child_id = children.id
    JOIN hospitals ON bookings.hospital_id = hospitals.id
    JOIN vaccines ON bookings.vaccine_id = vaccines.id
    WHERE bookings.parent_id = ?
    ORDER BY bookings.appointment_date DESC
");

$stmt->execute([$parent_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - VaxManager</title>
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
        
        /* Table Styling */
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .table { vertical-align: middle; }
        .table thead { background-color: #f8fafc; }
        .table thead th { border-top: none; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; padding: 1.25rem; }
        .table tbody td { padding: 1.25rem; color: #334155; font-size: 0.95rem; }
        
        /* Badge Styling */
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-approved { background-color: #dcfce7; color: #166534; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }
        .badge-completed { background-color: #e0e7ff; color: #3730a3; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="p-4"><h4 class="fw-bold"><i class="bi bi-shield-check me-2"></i>VaxManager</h4></div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="./dashboard.php"><i class="bi bi-people me-3"></i>My Children</a></li>
            <li class="nav-item"><a class="nav-link" href="./add_child.php"><i class="bi bi-person-plus me-3"></i>Add Child</a></li>
            <li class="nav-item"><a class="nav-link" href="./book_hospital.php"><i class="bi bi-hospital me-3"></i>Book Hospital</a></li>
            <li class="nav-item"><a class="nav-link active" href="./my_booking.php"><i class="bi bi-calendar-check me-3"></i>My Booking</a></li>
            <li class="nav-item"><a class="nav-link" href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a></li>
            <li class="nav-item mt-5"><a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i>Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Appointment History</h2>
                <p class="text-muted">Track all your vaccination requests and their current status.</p>
            </div>
            <a href="./book_hospital.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i>New Booking
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Child Name</th>
                                <th>Hospital</th>
                                <th>Vaccine</th>
                                <th>Appointment Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($bookings) > 0): ?>
                                <?php foreach($bookings as $b): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($b['child_name']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-hospital text-primary me-2"></i>
                                                <?= htmlspecialchars($b['hospital_name']) ?>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($b['vaccine_name']) ?></span></td>
                                        <td>
                                            <div class="text-secondary small">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?= date('D, M d, Y', strtotime($b['appointment_date'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $statusClass = 'badge-pending';
                                                $status = strtolower($b['status']);
                                                if($status == 'approved') $statusClass = 'badge-approved';
                                                if($status == 'rejected') $statusClass = 'badge-rejected';
                                                if($status == 'completed') $statusClass = 'badge-completed';
                                            ?>
                                            <span class="badge <?= $statusClass ?> px-3 py-2 rounded-pill">
                                                <?= ucfirst($b['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-calendar-x text-muted mb-3 d-block fs-1"></i>
                                        <p class="text-muted">No bookings found. You haven't made any appointments yet.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>