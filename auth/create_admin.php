<?php
include("../config/db.php");

$email = "admin@gmail.com";
$password = "12345";   // you can change this
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = "admin";

// insert into users table
$stmt = $conn->prepare("INSERT INTO users (email,password,role) VALUES (?,?,?)");
$stmt->execute([$email,$hashed_password,$role]);

$user_id = $conn->lastInsertId();

// insert into admins table
$stmt = $conn->prepare("INSERT INTO admins (user_id,name) VALUES (?,?)");
$stmt->execute([$user_id,"Main Admin"]);

echo "Admin created successfully!";
?>
