<?php
session_start();
include("../config/db.php");

if ($_SESSION['role'] != "parent") {
    header("Location: ../auth/login.php");
}

$user_id = $_SESSION['user_id'];

// get parent_id from parents table
$stmt = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

$parent_id = $parent['id'];

// fetch children
$children = $conn->prepare("SELECT * FROM children WHERE parent_id=?");
$children->execute([$parent_id]);
$children = $children->fetchAll();

// fetch hospitals
$hospitals = $conn->query("SELECT * FROM hospitals")->fetchAll();

// fetch vaccines
$vaccines = $conn->query("SELECT * FROM vaccines WHERE status='available'")->fetchAll();

if (isset($_POST['book'])) {

    $child_id = $_POST['child_id'];
    $hospital_id = $_POST['hospital_id'];
    $vaccine_id = $_POST['vaccine_id'];
    $date = $_POST['appointment_date'];

    $stmt = $conn->prepare("INSERT INTO bookings (parent_id, child_id, hospital_id, vaccine_id, appointment_date)
                             VALUES (?,?,?,?,?)");
    $stmt->execute([$parent_id, $child_id, $hospital_id, $vaccine_id, $date]);

    echo "Booking Request Sent (Pending Approval)";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - VaxManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-color: #2c3e50;
            --accent-color: #667eea;
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
            transition: 0.3s;
        }

        .nav-link {
            color: #bdc3c7;
            padding: 15px 25px;
            transition: 0.3s;
            border-radius: 10px;
            margin: 5px 15px;
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

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        /* Animation Logic for smooth interface transition [cite: 39] */
        .tab-pane {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="p-4">
            <h4 class="fw-bold"><i class="bi bi-shield-check me-2"></i>VaxManager</h4>
        </div>
        <ul class="nav flex-column mt-3" id="dashboardTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="children-tab" href="./dashboard.php"><i
                        class="bi bi-people me-3"></i>My Children</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="add-child-tab" href="./add_child.php" role="tab"><i
                        class="bi bi-person-plus me-3"></i>Add Child</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="book-tab" href="./book_hospital.php" role="tab"><i
                        class="bi bi-hospital me-3"></i>Book Hospital</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reports-tab" href="./my_booking.php" role="tab"><i
                        class="bi bi-file-earmark-medical me-3"></i>My Booking</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reports-tab" href="./reports.php" role="tab"><i
                        class="bi bi-file-earmark-medical me-3"></i>Reports</a>
            </li>
            <li class="nav-item mt-5">
                <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i>Logout</a>
            </li>
        </ul>
    </div>

    <h2>Book Hospital</h2>

    <form method="post">

        <select name="child_id" required>
            <option value="">Select Child</option>
            <?php foreach ($children as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['child_name'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <select name="hospital_id" required>
            <option value="">Select Hospital</option>
            <?php foreach ($hospitals as $h): ?>
                <option value="<?= $h['id'] ?>"><?= $h['hospital_name'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <select name="vaccine_id" required>
            <option value="">Select Vaccine</option>
            <?php foreach ($vaccines as $v): ?>
                <option value="<?= $v['id'] ?>"><?= $v['vaccine_name'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="date" name="appointment_date" required><br><br>

        <button name="book">Book Appointment</button>
    </form>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>