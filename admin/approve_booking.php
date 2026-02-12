<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("UPDATE bookings SET status='approved' WHERE id=?");
$stmt->execute([$id]);

header("Location: view_bookings.php");
