<?php
session_start();
if($_SESSION['role'] != "hospital"){
    header("Location: ../auth/login.php");
    exit();
}
?>

<h2>Hospital Dashboard</h2>

<ul>
    <li><a href="view_appointments.php">View Appointments</a></li>
    <li><a href="../auth/logout.php">Logout</a></li>
</ul>
