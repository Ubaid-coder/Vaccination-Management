<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// get parent_id from parents table
$stmt = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$parent){
    die("Parent record not found.");
}

$parent_id = $parent['id'];

// now fetch children
$stmt = $conn->prepare("SELECT * FROM children WHERE parent_id = ?");
$stmt->execute([$parent_id]);
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

<?php if(count($children) > 0): ?>
    <?php foreach($children as $child): ?>
        <tr>
            <td><?= $child['id'] ?></td>
            <td><?= $child['child_name'] ?></td>
            <td><?= $child['dob'] ?></td>
            <td><?= $child['gender'] ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="4">No children found</td>
    </tr>
<?php endif; ?>
</table>
