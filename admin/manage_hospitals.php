<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit();
}

$hospitals = $conn->query("SELECT * FROM hospitals")->fetchAll();
?>

<h2>Hospitals</h2>

<table border="1">
<tr>
<th>ID</th>
<th>Hospital</th>
<th>Address</th>
<th>Location</th>
</tr>

<?php foreach($hospitals as $h): ?>
<tr>
<td><?= $h['id'] ?></td>
<td><?= $h['hospital_name'] ?></td>
<td><?= $h['address'] ?></td>
<td><?= $h['location'] ?></td>
</tr>
<?php endforeach; ?>
</table>
