<?php
if ($_SESSION['role'] != 'hospital') {
    header("Location: ../index.php");
    exit();
}