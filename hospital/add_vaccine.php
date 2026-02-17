<?php
session_start();
include("../config/db.php");

if ($_SESSION['role'] != 'hospital') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['add'])) {
    $name = $_POST['vaccine_name'];

    $stmt = $conn->prepare("
        INSERT INTO vaccines (vaccine_name,status,added_by,approval_status)
        VALUES (?, 'unavailable', 'hospital', 'pending')
    ");
    $stmt->execute([$name]);

    echo "<script>alert('Vaccine sent for admin approval');</script>";
}
?>

<h2>Add Vaccine (Hospital)</h2>

<form method="post">
    <input type="text" name="vaccine_name" required>
    <button name="add">Submit Vaccine</button>
</form>