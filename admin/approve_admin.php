<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// approve
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->prepare("
        UPDATE vaccines 
        SET approval_status='approved', status='available'
        WHERE id=?
    ")->execute([$id]);
}

// reject
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $conn->prepare("
        DELETE FROM vaccines WHERE id=?
    ")->execute([$id]);
}

$vaccines = $conn->query("
    SELECT * FROM vaccines WHERE added_by='hospital' AND approval_status='pending'
")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Approve Hospital Vaccines</h2>

<table border="1">
<tr>
<th>ID</th><th>Name</th><th>Action</th>
</tr>

<?php foreach($vaccines as $v): ?>
<tr>
<td><?= $v['id'] ?></td>
<td><?= $v['vaccine_name'] ?></td>
<td>
<a href="?approve=<?= $v['id'] ?>">Approve</a>
<a href="?reject=<?= $v['id'] ?>">Reject</a>
</td>
</tr>
<?php endforeach; ?>
</table>
