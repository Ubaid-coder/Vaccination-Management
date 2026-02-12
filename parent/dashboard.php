<?php
session_start();
if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
}
?>
<h2>Parent Dashboard</h2>
<a href="../auth/logout.php">Logout</a>
