<?php include("admin_layout.php"); ?>
<?php include("../config/db.php"); ?>

<h2>Manage Vaccines</h2>

<form method="post">
    <input type="text" name="vaccine_name" required placeholder="Vaccine Name">
    <button name="add" class="btn btn-primary">Add Vaccine</button>
</form>

<?php
if(isset($_POST['add'])){
    $stmt = $conn->prepare("INSERT INTO vaccines (vaccine_name,status) VALUES (?, 'available')");
    $stmt->execute([$_POST['vaccine_name']]);
}

if(isset($_GET['toggle'])){
    $id = $_GET['toggle'];
    $conn->prepare("UPDATE vaccines SET status=IF(status='available','unavailable','available') WHERE id=?")
         ->execute([$id]);
}

$vaccines = $conn->query("SELECT * FROM vaccines")->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table table-bordered mt-3">
<tr>
<th>ID</th><th>Name</th><th>Status</th><th>Action</th>
</tr>

<?php foreach($vaccines as $v): ?>
<tr>
<td><?= $v['id'] ?></td>
<td><?= $v['vaccine_name'] ?></td>
<td><?= $v['status'] ?></td>
<td>
<a href="?toggle=<?= $v['id'] ?>" class="btn btn-sm btn-warning">Toggle</a>
</td>
</tr>
<?php endforeach; ?>
</table>

</div></body></html>
