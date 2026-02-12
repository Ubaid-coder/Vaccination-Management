<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit();
}

// Add vaccine
if(isset($_POST['add'])){
    $name = $_POST['vaccine_name'];
    $stmt = $conn->prepare("INSERT INTO vaccines (vaccine_name,status) VALUES (?, 'available')");
    $stmt->execute([$name]);
}

// Toggle status
if(isset($_GET['toggle'])){
    $id = $_GET['toggle'];
    $stmt = $conn->prepare("
        UPDATE vaccines 
        SET status = IF(status='available','unavailable','available') 
        WHERE id=?
    ");
    $stmt->execute([$id]);
}

$vaccines = $conn->query("SELECT * FROM vaccines")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Vaccines</h2>

<form method="post">
    <input type="text" name="vaccine_name" placeholder="Vaccine Name" required>
    <button name="add">Add Vaccine</button>
</form>

<table border="1">
<tr>
    <th>ID</th>
    <th>Vaccine</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach($vaccines as $v): ?>
<tr>
    <td><?= $v['id'] ?></td>
    <td><?= $v['vaccine_name'] ?></td>
    <td><?= $v['status'] ?></td>
    <td>
        <a href="manage_vaccines.php?toggle=<?= $v['id'] ?>">Toggle Status</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
