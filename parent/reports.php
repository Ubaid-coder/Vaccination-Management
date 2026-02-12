<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// get parent_id
$stmt = $conn->prepare("SELECT id FROM parents WHERE user_id=?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$parent){
    die("Parent record not found");
}

$parent_id = $parent['id'];

// fetch reports
$stmt = $conn->prepare("
SELECT 
    vaccination_reports.status AS report_status,
    vaccination_reports.remarks,
    vaccination_reports.report_date,
    children.child_name,
    hospitals.hospital_name,
    vaccines.vaccine_name
FROM vaccination_reports
JOIN bookings ON vaccination_reports.booking_id = bookings.id
JOIN children ON bookings.child_id = children.id
JOIN hospitals ON bookings.hospital_id = hospitals.id
JOIN vaccines ON bookings.vaccine_id = vaccines.id
WHERE bookings.parent_id = ?
ORDER BY vaccination_reports.report_date DESC
");
$stmt->execute([$parent_id]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Vaccination Reports</h2>

<table border="1">
<tr>
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
    <td colspan="6">No reports found</td>
</tr>
<?php endif; ?>
</table>
