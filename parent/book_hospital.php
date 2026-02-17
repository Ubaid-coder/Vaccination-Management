<?php
session_start();
include("../config/db.php");

// Logic: Check if parent is logged in
if ($_SESSION['role'] != "parent") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logic: Fetch Parent ID linked to User
$stmt = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);
$parent_id = $parent['id'];

// Logic: Fetch dropdown data (Children, Hospitals, Available Vaccines)
$children = $conn->prepare("SELECT * FROM children WHERE parent_id=?");
$children->execute([$parent_id]);
$children_list = $children->fetchAll();

$hospitals = $conn->query("SELECT * FROM hospitals")->fetchAll();
$vaccines = $conn->query("
    SELECT * FROM vaccines 
    WHERE status='available' AND approval_status='approved'
")->fetchAll(PDO::FETCH_ASSOC);


// Logic: Handle Booking Submission
if (isset($_POST['book'])) {
    $child_id = $_POST['child_id'];
    $hospital_id = $_POST['hospital_id'];
    $vaccine_id = $_POST['vaccine_id'];
    $date = $_POST['appointment_date'];

    $stmt = $conn->prepare("INSERT INTO bookings (parent_id, child_id, hospital_id, vaccine_id, appointment_date, status)
                             VALUES (?,?,?,?,?,'pending')");
    if($stmt->execute([$parent_id, $child_id, $hospital_id, $vaccine_id, $date])) {
        $success_msg = "Booking Request Sent! Wait for hospital approval.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Vaccination - VaxManager</title>
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
        
        /* Modern Form Styling */
        .booking-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .form-label { font-weight: 500; color: #4a5568; font-size: 0.9rem; }
        .form-select, .form-control { border-radius: 10px; padding: 12px; border: 1px solid #e2e8f0; background-color: #fdfdfd; }
        .form-select:focus, .form-control:focus { border-color: var(--accent-color); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .btn-book { background: var(--accent-color); border: none; padding: 12px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-book:hover { background: #5a67d8; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="p-4"><h4 class="fw-bold"><i class="bi bi-shield-check me-2"></i>VaxManager</h4></div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="./dashboard.php"><i class="bi bi-people me-3"></i>My Children</a></li>
            <li class="nav-item"><a class="nav-link" href="./add_child.php"><i class="bi bi-person-plus me-3"></i>Add Child</a></li>
            <li class="nav-item"><a class="nav-link active" href="./book_hospital.php"><i class="bi bi-hospital me-3"></i>Book Hospital</a></li>
            <li class="nav-item"><a class="nav-link" href="./my_booking.php"><i class="bi bi-calendar-check me-3"></i>My Booking</a></li>
            <li class="nav-item"><a class="nav-link" href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a></li>
            <li class="nav-item mt-5"><a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i>Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <div class="mb-4">
                        <h2 class="fw-bold">Book Vaccination</h2>
                        <p class="text-muted">Schedule an appointment at your preferred hospital.</p>
                    </div>

                    <?php if(isset($success_msg)): ?>
                        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg ?>
                        </div>
                    <?php endif; ?>

                    <div class="card booking-card">
                        <div class="card-body p-5">
                            <form method="post">
                                <div class="row g-4">
                                    
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="bi bi-person me-2"></i>Select Child</label>
                                        <select name="child_id" class="form-select" required>
                                            <option value="">Choose...</option>
                                            <?php foreach ($children_list as $c): ?>
                                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['child_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label"><i class="bi bi-building me-2"></i>Select Hospital</label>
                                        <select name="hospital_id" class="form-select" required>
                                            <option value="">Choose...</option>
                                            <?php foreach ($hospitals as $h): ?>
                                                <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['hospital_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label"><i class="bi bi-patch-check me-2"></i>Select Vaccine</label>
                                        <select name="vaccine_id" class="form-select" required>
                                            <option value="">Choose...</option>
                                            <?php foreach ($vaccines as $v): ?>
                                                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['vaccine_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label"><i class="bi bi-calendar-event me-2"></i>Appointment Date</label>
                                        <input type="date" name="appointment_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                                    </div>

                                    <div class="col-12 mt-5">
                                        <button name="book" class="btn btn-primary btn-book w-100 text-white">
                                            Confirm Booking Request
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-4 bg-light border d-flex align-items-center">
                        <i class="bi bi-info-circle-fill text-primary me-3 fs-4"></i>
                        <small class="text-muted">Apki request hospital ko bhej di jayegi. Hospital approval ke baad aap status "My Booking" tab mein dekh sakte hain.</small>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>