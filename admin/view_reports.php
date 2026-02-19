<?php include("layout.php"); ?>
<?php include("../config/db.php"); ?>

<h2>Vaccination Reports</h2>

<?php
$stmt = $conn->prepare("
SELECT parents.name as parent_name, child_name,vaccine_name, hospital_name,status, appointment_date AS date 
FROM bookings
JOIN parents ON bookings.parent_id = parents.id
JOIN children ON parents.id = children.parent_id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN vaccination_reports ON bookings.id = vaccinations_reports.booking_id
");
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if(count($reports) > 0): ?>
<table class="table table-bordered">
<tr>
<th>ParentName</th>
<th>ChildName</th>
<th>Vaccine</th>
<th>Hospital</th>
<th>Status</th>
<th>Date</th>
</tr>

<?php foreach($reports as $r): ?>
<tr>
<td><?= htmlspecialchars($r['parent_name']) ?></td>
<td><?= htmlspecialchars($r['child_name']) ?></td>
<td><?= htmlspecialchars($r['vaccine_name']) ?></td>
<td><?= htmlspecialchars($r['hospital_name']) ?></td>
<td><?= htmlspecialchars($r['status']) ?></td>
<td><?= htmlspecialchars($r['date']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<div class="alert alert-info">No vaccination records found.</div>
<?php endif; ?>
