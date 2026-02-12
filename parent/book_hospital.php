<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
}

$parent_id = $_SESSION['user_id'];

// fetch children
$children = $conn->prepare("SELECT * FROM children WHERE parent_id=?");
$children->execute([$parent_id]);
$children = $children->fetchAll();

// fetch hospitals
$hospitals = $conn->query("SELECT * FROM hospitals")->fetchAll();

// fetch vaccines
$vaccines = $conn->query("SELECT * FROM vaccines WHERE status='available'")->fetchAll();

if(isset($_POST['book'])){

    $child_id = $_POST['child_id'];
    $hospital_id = $_POST['hospital_id'];
    $vaccine_id = $_POST['vaccine_id'];
    $date = $_POST['appointment_date'];

    $stmt = $conn->prepare("INSERT INTO bookings (parent_id, child_id, hospital_id, vaccine_id, appointment_date)
                             VALUES (?,?,?,?,?)");
    $stmt->execute([$parent_id,$child_id,$hospital_id,$vaccine_id,$date]);

    echo "Booking Request Sent (Pending Approval)";
}
?>

<h2>Book Hospital</h2>

<form method="post">

<select name="child_id" required>
<option value="">Select Child</option>
<?php foreach($children as $c): ?>
<option value="<?= $c['id'] ?>"><?= $c['child_name'] ?></option>
<?php endforeach; ?>
</select><br><br>

<select name="hospital_id" required>
<option value="">Select Hospital</option>
<?php foreach($hospitals as $h): ?>
<option value="<?= $h['id'] ?>"><?= $h['hospital_name'] ?></option>
<?php endforeach; ?>
</select><br><br>

<select name="vaccine_id" required>
<option value="">Select Vaccine</option>
<?php foreach($vaccines as $v): ?>
<option value="<?= $v['id'] ?>"><?= $v['vaccine_name'] ?></option>
<?php endforeach; ?>
</select><br><br>

<input type="date" name="appointment_date" required><br><br>

<button name="book">Book Appointment</button>
</form>
