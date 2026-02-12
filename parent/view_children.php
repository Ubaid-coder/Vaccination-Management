<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
}

$id = $_SESSION['user_id'];
echo $id;
$stmt = $conn->prepare("SELECT * FROM children WHERE id = ?");
$stmt->execute([$id]);
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<h2>My Children</h2>

<table border="1">
<tr>
<th>ID</th>
<th>Name</th>
<th>DOB</th>
<th>Gender</th>
</tr>

<?php foreach($children as $child): ?>
    
<tr>
<td><?= $child['id'] ?></td>
<td><?= $child['child_name'] ?></td>
<td><?= $child['dob'] ?></td>
<td><?= $child['gender'] ?></td>
</tr>
<?php endforeach; ?>
</table>
