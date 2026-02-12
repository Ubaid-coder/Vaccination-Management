<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "hospital"){
    header("Location: ../auth/login.php");
    exit();
}

$booking_id = $_GET['id'];

if(isset($_POST['update'])){

    $status = $_POST['status']; // vaccinated / not_vaccinated
    $remarks = $_POST['remarks'];
    $date = date("Y-m-d");

    // update booking status
    $stmt = $conn->prepare("UPDATE bookings SET status='vaccinated' WHERE id=?");
    $stmt->execute([$booking_id]);

    // insert report
    $stmt = $conn->prepare("
        INSERT INTO vaccination_reports (booking_id,status,remarks,report_date)
        VALUES (?,?,?,?)
    ");
    $stmt->execute([$booking_id,$status,$remarks,$date]);

    echo "Vaccination status updated successfully";
}
?>

<h2>Update Vaccination Status</h2>

<form method="post">

<select name="status" required>
    <option value="">Select Status</option>
    <option value="vaccinated">Vaccinated</option>
    <option value="not_vaccinated">Not Vaccinated</option>
</select><br><br>

<textarea name="remarks" placeholder="Remarks"></textarea><br><br>

<button name="update">Update</button>
</form>
