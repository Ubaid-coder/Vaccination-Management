<?php
session_start();
include("../config/db.php");
include("./check_auth.php"); // hospital auth

// ADD VACCINE
if(isset($_POST['add_vaccine'])){
    $vaccine_name = $_POST['vaccine_name'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO vaccines (vaccine_name, status) VALUES (?,?)");
    $stmt->execute([$vaccine_name, $status]);

    echo "<div class='alert alert-success'>Vaccine Added Successfully</div>";
}


// UPDATE VACCINE
if(isset($_POST['update_vaccine'])){
    $id = $_POST['id'];
    $vaccine_name = $_POST['vaccine_name'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE vaccines SET vaccine_name=?, status=? WHERE id=?");
    $stmt->execute([$vaccine_name, $status, $id]);

    echo "<div class='alert alert-success'>Vaccine Updated</div>";
}


// FETCH ALL VACCINES
$stmt = $conn->prepare("SELECT * FROM vaccines");
$stmt->execute();
$vaccines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Vaccines</title>
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
<h2>Manage Vaccines</h2>

<!-- ADD FORM -->
<form method="post" class="row g-2 mb-4">
    <div class="col-md-5">
        <input type="text" name="vaccine_name" class="form-control" placeholder="Vaccine Name" required>
    </div>
    <div class="col-md-4">
        <select name="status" class="form-select" required>
            <option value="">Select Status</option>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
        </select>
    </div>
    <div class="col-md-3">
        <button name="add_vaccine" class="btn btn-primary w-100">Add Vaccine</button>
    </div>
</form>


<!-- TABLE -->
<?php if(count($vaccines) > 0): ?>
<table class="table table-bordered">
<tr>
    <th>ID</th>
    <th>Vaccine Name</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php foreach($vaccines as $v): ?>
<tr>
    <td><?= $v['id'] ?></td>
    <td><?= htmlspecialchars($v['vaccine_name']) ?></td>
    <td><?= $v['status'] ?></td>
    <td>
        <!-- UPDATE FORM -->
        <form method="post" class="d-inline-flex gap-2">
            <input type="hidden" name="id" value="<?= $v['id'] ?>">
            <input type="text" name="vaccine_name" value="<?= $v['vaccine_name'] ?>" class="form-control" required>
            <select name="status" class="form-select">
                <option value="available" <?= $v['status']=="available"?"selected":"" ?>>Available</option>
                <option value="unavailable" <?= $v['status']=="unavailable"?"selected":"" ?>>Unavailable</option>
            </select>
            <button name="update_vaccine" class="btn btn-success btn-sm">Update</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php else: ?>
<p class="alert alert-info">No vaccines found.</p>
<?php endif; ?>

</div>

</body>
</html>