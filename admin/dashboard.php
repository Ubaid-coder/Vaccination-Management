<?php
session_start();
include("../config/db.php");
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
            width:220px;
            height:100vh;
            position:fixed;
            background:#343a40;
            color:white;
            padding:15px;
        }
        .sidebar a{
            color:white;
            display:block;
            padding:10px;
            text-decoration:none;
        }
        .sidebar a:hover{
            background:#495057;
        }
        .content{
            margin-left:230px;
            padding:20px;
        }
        .topbar{
            background:#0d6efd;
            color:white;
            padding:10px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4>Admin Panel</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_vaccines.php">Manage Vaccines</a>
    <a href="manage_hospitals.php">Hospitals</a>
    <a href="bookings.php">Bookings</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="content">
    <div class="topbar">
        Welcome Admin
    </div>

    <!-- Page Content Start -->

<h2>Admin Dashboard</h2>

<div class="row">
    <div class="col-md-3">
        <div class="card p-3 bg-info text-white">
            <h4>Total Parents</h4>
            <?php
            $q = $conn->query("SELECT COUNT(*) FROM parents");
            echo $q->fetchColumn();
            ?>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-success text-white">
            <h4>Total Hospitals</h4>
            <?php
            $q = $conn->query("SELECT COUNT(*) FROM hospitals");
            echo $q->fetchColumn();
            ?>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-warning text-white">
            <h4>Total Vaccines</h4>
            <?php
            $q = $conn->query("SELECT COUNT(*) FROM vaccines");
            echo $q->fetchColumn();
            ?>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-danger text-white">
            <h4>Total Bookings</h4>
            <?php
            $q = $conn->query("SELECT COUNT(*) FROM bookings");
            echo $q->fetchColumn();
            ?>
        </div>
    </div>
</div>