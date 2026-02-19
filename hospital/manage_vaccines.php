
<?php include("../config/db.php"); ?>

<h2>Manage Vaccines</h2>

<?php
if(isset($_POST['add'])){
    $conn->prepare("INSERT INTO vaccines (vaccine_name,status) VALUES (?, 'available')")
         ->execute([$_POST['vaccine_name']]);
}

if(isset($_GET['delete'])){
    $conn->prepare("DELETE FROM vaccines WHERE id=?")
         ->execute([$_GET['delete']]);
}

$vaccines = $conn->query("SELECT * FROM vaccines")->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="post">
<input type="text" name="vaccine_name" required placeholder="Vaccine Name">
<button class="btn btn-primary" name="add">Add Vaccine</button>
</form>

<br>

<?php if(count($vaccines)>0): ?>
<table class="table table-bordered">
<tr>
<th>ID</th><th>Name</th><th>Status</th><th>Action</th>
</tr>
<?php foreach($vaccines as $v): ?>
<tr>
<td><?= $v['id'] ?></td>
<td><?= $v['vaccine_name'] ?></td>
<td><?= $v['status'] ?></td>
<td>
<a href="?delete=<?= $v['id'] ?>" class="btn btn-danger btn-sm"
   onclick="return confirm('Delete vaccine?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>No vaccines found.</p>
<?php endif; ?>
