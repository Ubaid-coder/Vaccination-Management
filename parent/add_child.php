<?php
session_start();
include("../config/db.php");
include("./check_auth.php");

$user_id = $_SESSION['user_id'];
$success_msg = "";

// get parent_id from parents table using user_id
$stmt = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$parent){
    die("Parent record not found.");
}

$parent_id = $parent['id'];

if(isset($_POST['add_child'])){
    $child_name = $_POST['child_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    $stmt = $conn->prepare("INSERT INTO children (parent_id, child_name, dob, gender) VALUES (?,?,?,?)");
    if($stmt->execute([$parent_id, $child_name, $dob, $gender])) {
        $success_msg = "Child Added Successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Child - VaxManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-color: #2c3e50;
            --accent-color: #667eea;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
        }

        /* Responsive Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--sidebar-color);
            position: fixed;
            left: 0;
            top: 0;
            color: white;
            transition: all 0.3s;
            z-index: 1000;
        }

        .nav-link {
            color: #bdc3c7;
            padding: 12px 25px;
            transition: 0.3s;
            border-radius: 8px;
            margin: 4px 15px;
            display: flex;
            align-items: center;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--accent-color);
            color: white;
        }

        /* Main Content Adjustment */
        .main-content {
            margin-left: 260px;
            padding: 30px;
            transition: all 0.3s;
        }

        .form-card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: var(--accent-color);
            border: none;
            padding: 12px;
            font-weight: 500;
            border-radius: 10px;
        }

        .form-control, .form-select {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="p-4">
            <h4 class="fw-bold"><i class="bi bi-shield-check me-2"></i>VaxManager</h4>
        </div>
        <ul class="nav flex-column mt-2">
            <li class="nav-item"><a class="nav-link" href="./dashboard.php"><i class="bi bi-people me-3"></i>My Children</a></li>
            <li class="nav-item"><a class="nav-link active" href="./add_child.php"><i class="bi bi-person-plus me-3"></i>Add Child</a></li>
            <li class="nav-item"><a class="nav-link" href="./book_hospital.php"><i class="bi bi-hospital me-3"></i>Book Hospital</a></li>
            <li class="nav-item"><a class="nav-link" href="./my_booking.php"><i class="bi bi-calendar-check me-3"></i>My Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a></li>
            <li class="nav-item mt-5"><a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i>Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
       

        <div class="container-fluid">
            <div class="page-header">
                <h3 class="fw-bold text-dark">Add Your Child</h3>
                <p class="text-muted">Register your child to manage their vaccination schedule.</p>
            </div>

            <?php if($success_msg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> <?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xl-6 col-lg-8">
                    <div class="card form-card p-4">
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Child's Full Name</label>
                                <input type="text" class="form-control" name="child_name" required placeholder="John Doe Jr.">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Date of Birth</label>
                                    <input type="date" class="form-control" name="dob" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Gender</label>
                                    <select class="form-select" name="gender">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button name="add_child" type="submit" class="btn btn-primary w-100 shadow-sm">
                                    <i class="bi bi-plus-circle me-2"></i> Register Child
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>