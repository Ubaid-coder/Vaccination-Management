<?php
session_start();
if($_SESSION['role'] != "hospital"){
    header("Location: ../auth/login.php");
}
?>
<h2>Hospital Dashboard</h2>
<a href="../auth/logout.php">Logout</a>
