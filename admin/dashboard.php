<?php
session_start();
if($_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
}
?>
<h2>Admin Dashboard</h2>
<a href="../auth/logout.php">Logout</a>
