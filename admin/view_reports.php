<?php include("layout.php"); ?>
<?php include("../config/db.php"); ?>

<h2>Vaccination Reports</h2>

<?php
$stmt = $conn->prepare("
SELECT 
    parents.name AS parent_name,
    children.child_name,
    vaccines.vaccine_name,
    hospitals.hospital_name,
    vaccination_reports.status,
    vaccination_reports.remarks,
    vaccination_reports.report_date AS date
FROM vaccination_reports
JOIN bookings ON vaccination_reports.booking_id = bookings.id
JOIN parents ON bookings.parent_id = parents.id
JOIN children ON bookings.child_id = children.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
ORDER BY vaccination_reports.report_date DESC
");

$stmt->execute(); // ✅ VERY IMPORTANT
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if(count($reports) > 0): ?>
<table class="table table-bordered">
<tr>
<th>Parent Name</th>
<th>Child Name</th>
<th>Vaccine</th>
<th>Hospital</th>
<th>Status</th>
<th>Remarks</th>
<th>Date</th>
</tr>

<?php foreach($reports as $r): ?>
<tr>
<td><?= htmlspecialchars($r['parent_name']) ?></td>
<td><?= htmlspecialchars($r['child_name']) ?></td>
<td><?= htmlspecialchars($r['vaccine_name']) ?></td>
<td><?= htmlspecialchars($r['hospital_name']) ?></td>
<td><?= htmlspecialchars($r['status']) ?></td>
<td><?= htmlspecialchars($r['remarks']) ?></td>
<td><?= htmlspecialchars($r['date']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<div class="alert alert-info">No vaccination records found.</div>
<?php endif; ?>