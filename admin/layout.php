<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">

<style>
body{margin:0;}
.sidebar{
    width:230px;
    height:100vh;
    background:#1f2937;
    color:white;
    position:fixed;
}
.sidebar h3{
    padding:15px;
    text-align:center;
    border-bottom:1px solid #374151;
}
.sidebar a{
    display:block;
    padding:12px;
    color:white;
    text-decoration:none;
}
.sidebar a:hover{
    background:#374151;
}
.topbar{
    margin-left:230px;
    background:#2563eb;
    color:white;
    padding:10px 20px;
}
.content{
    margin-left:230px;
    padding:20px;
}
</style>
</head>
<body>

<div class="sidebar">
<h3>Admin Panel</h3>
<a href="./dashboard.php">Dashboard</a>
<a href="./manage_parents.php">Manage Parents</a>
<a href="./manage_hospitals.php">Manage Hospitals</a>
<a href="./view_reports.php">View Reports</a>
<a href="../auth/logout.php">Logout</a>
</div>

<div class="topbar">
Welcome Admin
</div>

<div class="content">
