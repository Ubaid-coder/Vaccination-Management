<?php
include("../config/db.php");

if(isset($_POST['register'])){

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // insert into users
    $stmt = $conn->prepare("INSERT INTO users (email,password,role) VALUES (?,?,?)");
    $stmt->execute([$email,$password,$role]);

    $user_id = $conn->lastInsertId();

    if($role == "parent"){
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        $stmt = $conn->prepare("INSERT INTO parents (user_id,name,phone,address) VALUES (?,?,?,?)");
        $stmt->execute([$user_id,$name,$phone,$address]);
        header("Location: ../index.php");
    }

    else if($role == "hospital"){
        $hospital_name = $_POST['hospital_name'];
        $address = $_POST['address'];
        $location = $_POST['location'];

        $stmt = $conn->prepare("INSERT INTO hospitals (user_id,hospital_name,address,location) VALUES (?,?,?,?)");
        $stmt->execute([$user_id,$hospital_name,$address,$location]);
        header("Location: ../index.php");
    }

    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Signup - VaxManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --parent-color: #667eea;
            --hospital-color: #00b09b;
            --transition-speed: 0.8s;
        }

        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; overflow-x: hidden; margin: 0; }

        /* The Main Sliding Container */
        .viewport {
            display: flex;
            width: 200vw;
            height: 100vh;
            transition: transform var(--transition-speed) cubic-bezier(0.7, 0, 0.3, 1);
        }

        .page-half {
            width: 100vw;
            display: flex;
            height: 100vh;
        }

        /* Branding Side */
        .brand-panel {
            width: 40%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            z-index: 2;
        }

        .parent-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .hospital-bg { background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); }

        /* Form Side */
        .form-panel {
            width: 60%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            overflow-y: auto;
        }

        .form-card { width: 100%; max-width: 550px; }

        /* Role Switcher Pill */
        .role-pill {
            background: #eee;
            padding: 5px;
            border-radius: 50px;
            display: inline-flex;
            margin-bottom: 20px;
        }

        .role-btn {
            border: none;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-active-parent { background: var(--parent-color); color: white; }
        .btn-active-hospital { background: var(--hospital-color); color: white; }

        /* Input Styling */
        .form-control {
            background: #f8f9fa;
            border: 2px solid transparent;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 5px;
        }

        .form-control:focus {
            border-color: #667eea;
            background: white;
            box-shadow: none;
        }

        /* Animation Classes */
        .slide-to-hospital { transform: translateX(-100vw); }

        .glass-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        @media (max-width: 992px) {
            .brand-panel { display: none; }
            .form-panel { width: 100%; }
        }
    </style>
</head>
<body>

<div class="viewport" id="mainSlider">
    
    <div class="page-half">
        <div class="brand-panel parent-bg">
            <div class="glass-box mx-4">
                <i class="bi bi-people-fill display-3"></i>
                <h2 class="fw-bold mt-3">Parent Registration</h2>
                <p>Secure your family's health with VaxManager.</p>
            </div>
        </div>
        <div class="form-panel">
            <div class="form-card">
                <div class="text-center">
                    <div class="role-pill">
                        <button class="role-btn btn-active-parent">Parent</button>
                        <button class="role-btn" onclick="goToHospital()">Hospital</button>
                    </div>
                </div>
                <h3 class="fw-bold mb-4">Create Parent Account</h3>
                <form method="POST">
                    <input type="hidden" name="role" value="parent">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="name@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+92..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Street/City" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Confirm Password</label>
                            <input type="password" name="confirmpassword" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button name="register" type="submit" class="btn btn-primary w-100 py-3 mt-4 rounded-3 fw-bold shadow">Register as Parent</button>
                </form>
            </div>
        </div>
    </div>

    <div class="page-half">
        <div class="form-panel">
            <div class="form-card">
                <div class="text-center">
                    <div class="role-pill">
                        <button class="role-btn" onclick="goToParent()">Parent</button>
                        <button class="role-btn btn-active-hospital text-white">Hospital</button>
                    </div>
                </div>
                <h3 class="fw-bold mb-4 text-success">Hospital Registration</h3>
                <form  method="POST">
                    <input type="hidden" name="role" value="hospital">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="small fw-bold">Hospital Name</label>
                            <input type="text" name="hospital_name" class="form-control border-success-subtle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Official Email</label>
                            <input type="email" name="email" class="form-control border-success-subtle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Location (City)</label>
                            <input type="text" name="location" class="form-control border-success-subtle" placeholder="e.g. Karachi" required>
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold">Full Address</label>
                            <input type="text" name="address" class="form-control border-success-subtle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control border-success-subtle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Confirm Password</label>
                            <input type="password" name="confirmpassword" class="form-control border-success-subtle" required>
                        </div>
                    </div>
                    <button name="register" type="submit" class="btn btn-success w-100 py-3 mt-4 rounded-3 fw-bold shadow">Register Hospital</button>
                </form>
            </div>
        </div>
        <div class="brand-panel hospital-bg">
            <div class="glass-box mx-4">
                <i class="bi bi-hospital-fill display-3"></i>
                <h2 class="fw-bold mt-3">Healthcare Partner</h2>
                <p>Manage vaccines and schedules with our advanced hub.</p>
            </div>
        </div>
    </div>

</div>

<script>
    const slider = document.getElementById('mainSlider');

    /**
     * Slide to Hospital registration
     */
    function goToHospital() {
        slider.classList.add('slide-to-hospital');
    }

    /**
     * Slide back to Parent registration
     */
    function goToParent() {
        slider.classList.remove('slide-to-hospital');
    }
</script>

</body>
</html>