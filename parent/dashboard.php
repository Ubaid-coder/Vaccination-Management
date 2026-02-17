<?php
session_start();
include("../config/db.php");

// Logic: Verify parent session [cite: 38, 39]
if ($_SESSION['role'] != "parent") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logic: Fetch parent ID 
$stmt = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parent) {
    die("Parent record not found.");
}

$parent_id = $parent['id'];

// Logic: Fetch all children for this parent 
$stmt = $conn->prepare("SELECT * FROM children WHERE parent_id = ?");
$stmt->execute([$parent_id]);
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper Logic: Calculate age from DOB
function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
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
        :root { --sidebar-color: #2c3e50; --accent-color: #667eea; }
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-color); position: fixed; color: white; transition: 0.3s; }
        .nav-link { color: #bdc3c7; padding: 15px 25px; transition: 0.3s; border-radius: 10px; margin: 5px 15px; }
        .nav-link:hover, .nav-link.active { background: var(--accent-color); color: white; }
        
        .main-content { margin-left: 260px; padding: 40px; }
        
        /* Children Profile Card UI */
        .child-card { border: none; border-radius: 20px; transition: transform 0.3s; background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .child-card:hover { transform: translateY(-5px); }
        .profile-img { width: 100px; height: 100px; object-fit: cover; border: 4px solid #f8fafc; }
        
        .upload-btn-wrapper { position: relative; overflow: hidden; display: inline-block; }
        .upload-btn-wrapper input[type=file] { position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="p-4 text-center">
            <h4 class="fw-bold"><i class="bi bi-shield-check me-2"></i>VaxManager</h4>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link active" href="./dashboard.php"><i class="bi bi-people me-3"></i>My Children</a></li>
            <li class="nav-item"><a class="nav-link" href="./add_child.php"><i class="bi bi-person-plus me-3"></i>Add Child</a></li>
            <li class="nav-item"><a class="nav-link" href="./book_hospital.php"><i class="bi bi-hospital me-3"></i>Book Hospital</a></li>
            <li class="nav-item"><a class="nav-link" href="./my_booking.php"><i class="bi bi-calendar-check me-3"></i>My Booking</a></li>
            <li class="nav-item"><a class="nav-link" href="./reports.php"><i class="bi bi-file-earmark-medical me-3"></i>Reports</a></li>
            <li class="nav-item mt-5"><a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i>Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2 class="fw-bold mb-4">My Children Profiles</h2>
        
        <div class="row g-4">
            <?php if (count($children) > 0): ?>
                <?php foreach ($children as $child): ?>
                    <div class="col-md-4">
                        <div class="card child-card p-4 text-center">
                            <div class="position-relative d-inline-block mx-auto mb-3">
                                <img src="<?= !empty($child['photo']) ? '../uploads/'.$child['photo'] : 'https://ui-avatars.com/api/?name='.$child['child_name'].'&background=random' ?>" 
                                     class="rounded-circle profile-img shadow-sm" alt="profile">
                                
                                <form action="update_photo.php" method="POST" enctype="multipart/form-data" class="upload-btn-wrapper position-absolute bottom-0 end-0">
                                    <input type="hidden" name="child_id" value="<?= $child['id'] ?>">
                                    <button type="button" class="btn btn-sm btn-primary rounded-circle shadow-sm" style="width:30px; height:30px; padding:0;">
                                        <i class="bi bi-camera" style="font-size: 12px;"></i>
                                    </button>
                                    <input type="file" name="child_photo" onchange="this.form.submit()">
                                </form>
                            </div>

                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($child['child_name']) ?></h5>
                            <p class="text-muted small mb-3">
                                <span class="badge bg-light text-dark rounded-pill px-3">
                                    <?= calculateAge($child['dob']) ?> Years Old
                                </span>
                            </p>
                            
                            <div class="d-flex justify-content-between text-start border-top pt-3 mt-2">
                                <div>
                                    <small class="text-muted d-block">Gender</small>
                                    <span class="fw-semibold small"><?= $child['gender'] ?></span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">DOB</small>
                                    <span class="fw-semibold small"><?= date('M d, Y', strtotime($child['dob'])) ?></span>
                                </div>
                            </div>
                            
                            <a href="child_details.php?id=<?= $child['id'] ?>" class="btn btn-outline-primary btn-sm mt-4 rounded-pill">View Full History</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <img src="https://illustrations.popsy.co/gray/family.svg" style="width: 200px;" class="mb-4">
                    <h4 class="text-muted">No children found</h4>
                    <p>Start by adding your child to manage their vaccinations.</p>
                    <a href="./add_child.php" class="btn btn-primary rounded-pill px-4">Add Child Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>