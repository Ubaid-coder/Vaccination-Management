<?php
session_start();
include("../config/db.php");

if ($_SESSION['role'] != "parent") {
    header("Location: ../auth/login.php");
}

$user_id = $_SESSION['user_id'];

// get parent_id from parents table using user_id
$stmt = $conn->prepare("SELECT * FROM parents WHERE user_id = ?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parent) {
    die("Parent record not found.");
}

$parent_id = $parent['id'];

if (isset($_POST['add_child'])) {

    $child_name = $_POST['child_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    $stmt = $conn->prepare(
        "INSERT INTO children (parent_id, child_name, dob, gender)
         VALUES (?,?,?,?)"
    );

    $stmt->execute([$parent_id, $child_name, $dob, $gender]);

    echo "Child Added Successfully";


    // get parent_id from parents table
    $stmt = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parent) {
        die("Parent record not found.");
    }

    $parent_id = $parent['id'];

    // now fetch children
    $stmt = $conn->prepare("SELECT * FROM children WHERE parent_id = ?");
    $stmt->execute([$parent_id]);
    $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<?php
require_once 'sidebar.php';
?>

    <div class="main-content">
        <div class="tab-content" id="myTabContent">

            <div class="tab-pane fade show active" id="children" role="tabpanel">
                <h2 class="fw-bold mb-4">Registered Children</h2>
                <?php if (count($children) > 0): ?>
                    <?php foreach ($children as $child): ?>
                        <tr>
                            <td><?= $child['id'] ?></td>
                            <td><?= $child['child_name'] ?></td>
                            <td><?= $child['dob'] ?></td>
                            <td><?= $child['gender'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No children found</td>
                    </tr>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="addChild" role="tabpanel">
                <h2 class="fw-bold mb-4 text-primary">Add New Child</h2>
                <div class="card p-5" style="max-width: 600px;">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Child Name</label>
                            <input type="text" name="child_name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Date of Birth</label><input
                                    type="date" name="dob" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                        </div>
                        <button name="add_child" type="submit" class="btn btn-primary w-100 py-2 mt-3">Register
                            Child</button>
                    </form>
                </div>
            </div>

            <div class="tab-pane fade" id="book" role="tabpanel">
                <h2 class="fw-bold mb-4">Book Vaccination Slot</h2>
                <div class="card p-4">
                    <form action="process_booking.php" method="POST">
                        <div class="mb-3"><label class="form-label">Select Child</label>
                            <select class="form-select">
                                <option>Junior Doe</option>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Select Hospital</label>
                            <select class="form-select">
                                <option>City Medical Center</option>
                                <option>Metro Health</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Preferred Date</label><input
                                    type="date" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Vaccine Type</label>
                                <select class="form-select">
                                    <option>Polio</option>
                                    <option>Hepatitis B</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-success w-100 py-2 shadow">Confirm Booking</button>
                    </form>
                </div>
            </div>

            <div class="tab-pane fade" id="reports" role="tabpanel">
                <h2 class="fw-bold mb-4">Vaccination History & Reports</h2>
                <div class="card p-4 overflow-hidden">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Child</th>
                                <th>Vaccine</th>
                                <th>Date</th>
                                <th>Hospital</th>
                                <th>Status</th>
                                <th>Report</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Junior Doe</td>
                                <td>BCG</td>
                                <td>2023-10-12</td>
                                <td>City Med</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td><button class="btn btn-sm btn-link"><i class="bi bi-download"></i> PDF</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>