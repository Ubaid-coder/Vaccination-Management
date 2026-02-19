<?php
session_start();
include("../config/db.php");
include("./check_auth.php");

$where = "";
$params = [];

if(isset($_POST['filter'])){
    $date = $_POST['date'];
    $where = "WHERE vaccination_reports.report_date = ?";
    $params[] = $date;
}

$stmt = $conn->prepare("
SELECT 
    vaccination_reports.status AS report_status,
    vaccination_reports.remarks,
    vaccination_reports.report_date,
    parents.name AS parent_name,
    children.child_name,
    hospitals.hospital_name,
    vaccines.vaccine_name
FROM vaccination_reports
JOIN bookings ON vaccination_reports.booking_id = bookings.id
JOIN parents ON bookings.parent_id = parents.id
JOIN children ON bookings.child_id = children.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
$where
ORDER BY vaccination_reports.report_date DESC
");

$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Vaccination Reports (Admin)</h2>

<form method="post">
    <input type="date" name="date" required>
    <button name="filter">Filter by Date</button>
</form>

<br>

<table border="1">
<tr>
    <th>Parent</th>
    <th>Child</th>
    <th>Hospital</th>
    <th>Vaccine</th>
    <th>Status</th>
    <th>Remarks</th>
    <th>Date</th>
</tr>

<?php if(count($reports) > 0): ?>
    <?php foreach($reports as $r): ?>
    <tr>
        <td><?= $r['parent_name'] ?></td>
        <td><?= $r['child_name'] ?></td>
        <td><?= $r['hospital_name'] ?></td>
        <td><?= $r['vaccine_name'] ?></td>
        <td><?= $r['report_status'] ?></td>
        <td><?= $r['remarks'] ?></td>
        <td><?= $r['report_date'] ?></td>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
<tr>
    <td colspan="7">No reports found</td>
</tr>
<?php endif; ?>
</table>
