<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "hospital"){
    header("Location: ../auth/login.php");
    exit();
}

$booking_id = $_GET['id'];

if(isset($_POST['update'])){
    $status = $_POST['status']; 
    $remarks = $_POST['remarks'];
    $date = date("Y-m-d");

    // Update booking status
    $stmt = $conn->prepare("UPDATE bookings SET status='vaccinated' WHERE id=?");
    $stmt->execute([$booking_id]);

    // Insert report
    $stmt = $conn->prepare("INSERT INTO vaccination_reports (booking_id,status,remarks,report_date) VALUES (?,?,?,?)");
    $stmt->execute([$booking_id,$status,$remarks,$date]);

    header("Location: view_appointments.php?msg=Updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Status - VaxManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .main-content { padding: 40px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="card p-5">
        <h3 class="fw-bold mb-4 text-center">Update Vaccination Status</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Select Status</label>
                <select name="status" class="form-select" required>
                    <option value="">Select Status</option>
                    <option value="vaccinated">Vaccinated</option>
                    <option value="not_vaccinated">Not Vaccinated</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Remarks</label>
                <textarea name="remarks" class="form-control" rows="4" placeholder="Enter details about the vaccination..."></textarea>
            </div>

            <button name="update" type="submit" class="btn btn-primary w-100 py-2">Submit Report</button>
            <a href="view_appointments.php" class="btn btn-link w-100 mt-2 text-muted">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>