<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
}

$parent_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT bookings.*, children.child_name, hospitals.hospital_name, vaccines.vaccine_name
FROM bookings
JOIN children ON bookings.child_id = children.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
WHERE bookings.parent_id = ?
");
$stmt->execute([$parent_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Bookings</h2>

<table border="1">
<tr>
<th>Child</th>
<th>Hospital</th>
<th>Vaccine</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php foreach($bookings as $b): ?>
<tr>
<td><?= $b['child_name'] ?></td>
<td><?= $b['hospital_name'] ?></td>
<td><?= $b['vaccine_name'] ?></td>
<td><?= $b['appointment_date'] ?></td>
<td><?= $b['status'] ?></td>
</tr>
<?php endforeach; ?>
</table>
