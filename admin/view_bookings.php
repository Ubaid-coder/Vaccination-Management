<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $conn->prepare("
SELECT 
    bookings.*, 
    parents.name AS parent_name,
    children.child_name,
    hospitals.hospital_name,
    vaccines.vaccine_name
FROM bookings
JOIN parents ON bookings.parent_id = parents.id
JOIN children ON bookings.child_id = children.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
ORDER BY bookings.id DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>All Booking Requests</h2>

<table border="1">
<tr>
    <th>Parent</th>
    <th>Child</th>
    <th>Hospital</th>
    <th>Vaccine</th>
    <th>Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach($bookings as $b): ?>
<tr>
    <td><?= $b['parent_name'] ?></td>
    <td><?= $b['child_name'] ?></td>
    <td><?= $b['hospital_name'] ?></td>
    <td><?= $b['vaccine_name'] ?></td>
    <td><?= $b['appointment_date'] ?></td>
    <td><?= $b['status'] ?></td>
    <td>
        <?php if($b['status']=="pending"): ?>
            <a href="approve_booking.php?id=<?= $b['id'] ?>">Approve</a> |
            <a href="reject_booking.php?id=<?= $b['id'] ?>">Reject</a>
        <?php else: ?>
            Done
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
