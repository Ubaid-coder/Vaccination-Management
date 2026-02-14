<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "hospital"){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// get hospital_id from hospitals table
$stmt = $conn->prepare("SELECT id FROM hospitals WHERE user_id=?");
$stmt->execute([$user_id]);
$hospital = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$hospital){
    die("Hospital record not found");
}

$hospital_id = $hospital['id'];

// fetch approved bookings
$stmt = $conn->prepare("
SELECT 
    bookings.*, 
    parents.name AS parent_name,
    children.child_name,
    vaccines.vaccine_name
FROM bookings
JOIN parents ON bookings.parent_id = parents.id
JOIN children ON bookings.child_id = children.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
WHERE bookings.hospital_id = ? AND bookings.status = 'approved'
");
$stmt->execute([$hospital_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Approved Appointments</h2>

<table border="1">
<tr>
    <th>Parent</th>
    <th>Child</th>
    <th>Vaccine</th>
    <th>Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach($appointments as $a): ?>
<tr>
    <td><?= $a['parent_name'] ?></td>
    <td><?= $a['child_name'] ?></td>
    <td><?= $a['vaccine_name'] ?></td>
    <td><?= $a['appointment_date'] ?></td>
    <td><?= $a['status'] ?></td>
    <td>
        <a href="update_status.php?id=<?= $a['id'] ?>">Update Status</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
