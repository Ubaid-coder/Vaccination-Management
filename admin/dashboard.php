<?php
session_start();
if($_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit();
}
?>

<h2>Admin Dashboard</h2>

<ul>
    <li><a href="view_bookings.php">View Booking Requests</a></li>
    <li><a href="manage_hospitals.php">Manage Hospitals</a></li>
    <li><a href="manage_vaccines.php">Manage Vaccines</a></li>
    <li><a href="reports.php">View Reports</a></li>
    <li><a href="../auth/logout.php">Logout</a></li>
</ul>
