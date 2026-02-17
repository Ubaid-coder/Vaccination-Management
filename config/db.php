<?php
$host = "localhost";
$db   = "vaccination_system";
$user = "root";
$pass = "";
$port = 3307;

try {
    $conn = new PDO("mysql:host=$host; port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
