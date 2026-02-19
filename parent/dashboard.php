<?php
session_start();
include("../config/db.php");
include("./check_auth.php");
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
    $diff = $birthDate->diff($today);
    
    if ($diff->y > 0) return $diff->y . " Years";
    if ($diff->m > 0) return $diff->m . " Months";
    return $diff->d . " Days";
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
            --bg-light: #f8fafc;
        }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-light); 
        }
        
        /* Sidebar (Maintained as per requirement) */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-color); position: fixed; color: white; transition: 0.3s; z-index: 1000; }
        .nav-link { color: #bdc3c7; padding: 15px 25px; transition: 0.3s; border-radius: 10px; margin: 5px 15px; }
        .nav-link:hover, .nav-link.active { background: var(--accent-color); color: white; }
        
        /* Main Content */
        .main-content { margin-left: 260px; padding: 40px; transition: all 0.3s; }
        
        /* Profile Card UI Improvements */
        .child-card { 
            border: none; 
            border-radius: 20px; 
            transition: all 0.3s ease; 
            background: white; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.03); 
        }
        
        .child-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 15px 35px rgba(0,0,0,0.08); 
        }
        
        .profile-img { 
            width: 100px; 
            height: 100px; 
            object-fit: cover; 
            border: 4px solid #fff; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .upload-btn-wrapper { position: relative; overflow: hidden; display: inline-block; }
        .upload-btn-wrapper input[type=file] { position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; }

        .btn-view-history {
            background-color: #f1f5f9;
            color: var(--accent-color);
            border: none;
            font-weight: 500;
        }

        .btn-view-history:hover {
            background-color: var(--accent-color);
            color: white;
        }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebar">
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
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold text-dark mb-1">My Children</h2>
                <p class="text-muted mb-0">Manage vaccination profiles for your family.</p>
            </div>
            <a href="./add_child.php" class="btn btn-primary rounded-pill px-4 d-none d-sm-block">
                <i class="bi bi-plus-lg me-2"></i>Add New
            </a>
        </div>
        
        <div class="row g-4">
            <?php if (count($children) > 0): ?>
                <?php foreach ($children as $child): ?>
                    <div class="col-sm-12 col-md-6 col-xl-4">
                        <div class="card child-card p-4 text-center">
                            <div class="position-relative d-inline-block mx-auto mb-3">
                                <img src="<?= !empty($child['photo']) ? '../uploads/'.$child['photo'] : 'https://ui-avatars.com/api/?name='.urlencode($child['child_name']).'&background=random&size=128' ?>" 
                                     class="rounded-circle profile-img" alt="profile">
                                
                                <form action="update_photo.php" method="POST" enctype="multipart/form-data" class="upload-btn-wrapper position-absolute bottom-0 end-0">
                                    <input type="hidden" name="child_id" value="<?= $child['id'] ?>">
                                    <button type="button" class="btn btn-sm btn-primary rounded-circle shadow-sm" style="width:32px; height:32px; padding:0; border: 2px solid white;">
                                        <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
                                    </button>
                                    <input type="file" name="child_photo" onchange="this.form.submit()">
                                </form>
                            </div>

                            <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($child['child_name']) ?></h5>
                            <div class="mb-3">
                                <span class="badge bg-soft-primary text-primary rounded-pill px-3" style="background-color: #eef2ff;">
                                    <?= calculateAge($child['dob']) ?> Old
                                </span>
                            </div>
                            
                            <div class="d-flex justify-content-around text-center border-top pt-3 mt-2">
                                <div>
                                    <small class="text-muted d-block">Gender</small>
                                    <span class="fw-semibold text-dark"><?= $child['gender'] ?></span>
                                </div>
                                <div style="border-left: 1px solid #eee;"></div>
                                <div>
                                    <small class="text-muted d-block">Birth Date</small>
                                    <span class="fw-semibold text-dark"><?= date('M d, Y', strtotime($child['dob'])) ?></span>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <a href="child_details.php?id=<?= $child['id'] ?>" class="btn btn-view-history py-2 rounded-pill">
                                    View Vaccination History
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="bg-white rounded-4 p-5 shadow-sm border">
                        <i class="bi bi-person-heart text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-dark mt-3 fw-bold">No Children Registered</h4>
                        <p class="text-muted mx-auto" style="max-width: 400px;">You haven't added any child profiles yet. Register your child now to start tracking their vaccinations.</p>
                        <a href="./add_child.php" class="btn btn-primary rounded-pill px-5 py-2 mt-2">
                            Add Your First Child
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>