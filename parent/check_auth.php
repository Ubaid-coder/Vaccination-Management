<?php
if ($_SESSION['role'] != "parent") {
    header("Location: ../index.php");
    exit();
}
