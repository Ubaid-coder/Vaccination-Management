<?php
session_start();
include("../config/db.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if($stmt->rowCount() == 1){

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] == "admin"){
                header("Location: ../admin/dashboard.php");
            }
            elseif($user['role'] == "parent"){
                header("Location: ../parent/dashboard.php");
            }
            elseif($user['role'] == "hospital"){
                header("Location: ../hospital/dashboard.php");
            }

        } else {
            echo "Wrong Password";
        }

    } else {
        echo "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VaxManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden; /* Prevent scrolling on desktop */
        }

        .login-container {
            height: 100vh;
        }

        /* --- LEFT SIDE: BRANDING & INFO --- */
        .brand-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Purple/Blue Gradient similar to image */
            color: white;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Abstract blobs for background decoration */
        .decoration-blob {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .blob-1 { width: 300px; height: 300px; top: -50px; left: -50px; }
        .blob-2 { width: 400px; height: 400px; bottom: -100px; right: -100px; }

        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            animation: float 6s ease-in-out infinite;
        }

        .glass-card:nth-child(2) { animation-delay: 1s; }
        .glass-card:nth-child(3) { animation-delay: 2s; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .back-btn {
            position: absolute;
            top: 30px;
            left: 30px;
            background: white;
            color: #333;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            z-index: 10;
        }
        .back-btn:hover { transform: scale(1.05); color: #333; }

        /* --- RIGHT SIDE: FORM --- */
        .form-section {
            background-color: #fdfdfd;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto; /* Allow scrolling if screen is short */
        }

        .form-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .form-control {
            background-color: #f0f2f5;
            border: none;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 5px;
        }
        
        .form-control:focus {
            background-color: #e8f0fe;
            box-shadow: none;
            border: 1px solid #764ba2;
        }

        .btn-primary {
            background-color: #6c5ce7;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: background 0.3s;
        }
        
        .btn-primary:hover { background-color: #5a4ad1; }

        .btn-google {
            background-color: white;
            border: 1px solid #ddd;
            color: #333;
            font-weight: 500;
        }
        .btn-google:hover { background-color: #f8f9fa; }

        .password-field { position: relative; }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 15px;
            cursor: pointer;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .brand-section { display: none; } /* Hide left side on mobile */
            body { overflow: auto; }
            .login-container { height: auto; }
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0 login-container">
        
        <div class="col-md-6 brand-section">
            <div class="decoration-blob blob-1"></div>
            <div class="decoration-blob blob-2"></div>

            <a href="index.php" class="back-btn"><i class="bi bi-arrow-left me-2"></i> Back to website</a>

            <div class="content-box text-center">
                
                <div class="glass-card d-flex align-items-center justify-content-between" style="width: 300px; margin: 0 auto 20px;">
                    <div class="text-start">
                        <h3 class="mb-0 fw-bold">10k+</h3>
                        <small>Vaccinated Patients</small>
                    </div>
                    <div class="bg-white rounded-circle p-2 text-primary">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                </div>

                <div class="glass-card" style="width: 350px; margin: 0 auto 20px;">
                    <div class="d-flex justify-content-around">
                        <div class="text-center" data-bs-toggle="tooltip" title="Secure">
                            <div class="bg-white rounded-circle p-2 mb-2 d-inline-block text-success">
                                <i class="bi bi-lock-fill"></i>
                            </div>
                            <div class="small">Secure</div>
                        </div>
                        <div class="text-center" data-bs-toggle="tooltip" title="Fast">
                            <div class="bg-white rounded-circle p-2 mb-2 d-inline-block text-warning">
                                <i class="bi bi-lightning-fill"></i>
                            </div>
                            <div class="small">Fast</div>
                        </div>
                        <div class="text-center" data-bs-toggle="tooltip" title="Reliable">
                            <div class="bg-white rounded-circle p-2 mb-2 d-inline-block text-info">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div class="small">Reliable</div>
                        </div>
                    </div>
                </div>

                <h2 class="mt-4">Register. Schedule. <br> Vaccinate.</h2>
            </div>
        </div>

        <div class="col-md-6 form-section">
            <div class="form-wrapper">
                <div class="mb-5">
                    <h3 class="fw-bold">Welcome back to VaxManager <span style="font-size: 1.2em;">👋</span></h3>
                    <p class="text-muted">Please enter your details to sign in.</p>
                </div>

                <form method="POST">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Ueremail</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Password</label>
                        <div class="password-field">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                            <i class="bi bi-eye toggle-password" onclick="togglePassword()"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                            <label class="form-check-label text-muted" for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="text-primary text-decoration-none small fw-bold">Forgot password?</a>
                    </div>

                    <button name="login" type="submit" class="btn btn-primary mb-3 shadow-sm">Sign In</button>
                    
                    <div class="text-center mb-3 text-muted small">OR</div>

                    <button type="button" class="btn btn-google w-100 shadow-sm d-flex align-items-center justify-content-center">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20" class="me-2">
                        Sign in with Google
                    </button>
                </form>

                <p class="text-center mt-5 text-muted">
                    Don't have an account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Sign up</a>
                </p>
            </div>
        </div>

    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.querySelector('.toggle-password');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>