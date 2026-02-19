<?php include("layout.php"); ?>
<?php include("../config/db.php"); ?>

<h2>Manage Bookings</h2>

<?php
$hospital_id = $_SESSION['user_id'];

if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->prepare("UPDATE bookings SET status='vaccinated' WHERE id=? AND hospital_id=?")
         ->execute([$id,$hospital_id]);
}

if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $conn->prepare("UPDATE bookings SET status='rejected' WHERE id=? AND hospital_id=?")
         ->execute([$id,$hospital_id]);
}

$stmt = $conn->prepare("
SELECT b.*, c.child_name, v.vaccine_name, p.name AS parent_name
FROM bookings b
JOIN children c ON b.child_id = c.id
JOIN vaccines v ON b.vaccine_id = v.id
JOIN parents p ON b.parent_id = p.id
WHERE b.hospital_id = ?
");
$stmt->execute([$hospital_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if(count($bookings)>0): ?>
<table class="table table-bordered">
<tr>
<th>Parent</th><th>Child</th><th>Vaccine</th><th>Date</th><th>Status</th><th>Action</th>
</tr>
<?php foreach($bookings as $b): ?>
<tr>
<td><?= $b['parent_name'] ?></td>
<td><?= $b['child_name'] ?></td>
<td><?= $b['vaccine_name'] ?></td>
<td><?= $b['appointment_date'] ?></td>
<td><?= $b['status'] ?></td>
<td>
<a href="?approve=<?= $b['id'] ?>" class="btn btn-success btn-sm">Vaccinated</a>
<a href="?reject=<?= $b['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>No bookings found.</p>
<?php endif; ?>
