<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">VaxManager</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="register.php">Register Patient</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h2 class="h4 mb-0 text-center">Patient Registration Form</h2>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted text-center mb-4">Please fill in your details accurately.</p>
                        
                        <form action="submit_data.php" method="POST">
                            
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required placeholder="Enter full name">
                            </div>

                            <div class="mb-3">
                                <label for="nid" class="form-label">National ID / Passport Number</label>
                                <input type="text" class="form-control" id="nid" name="nid" required placeholder="e.g., 12345-1234567-1">
                            </div>

                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="vaccine" class="form-label">Preferred Vaccine</label>
                                    <select class="form-select" id="vaccine" name="vaccine">
                                        <option value="Pfizer">Pfizer</option>
                                        <option value="Moderna">Moderna</option>
                                        <option value="Sinopharm">Sinopharm</option>
                                        <option value="AstraZeneca">AstraZeneca</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="contact" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="contact" name="contact" required placeholder="0300-1234567">
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Submit Registration</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; 2024 Vaccination Management System Project</p>
        </div>
    </footer>

</body>
</html>