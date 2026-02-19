<?php
include("layout.php");
include("../config/db.php");
?>

<h2>Manage Parents</h2>

<?php
if (isset($_GET['delete'])) {

    $parent_id = $_GET['delete'];

    // get parent record
    $stmt = $conn->prepare("SELECT user_id FROM parents WHERE id=?");
    $stmt->execute([$parent_id]);
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($parent) {

        // delete children
        $conn->prepare("DELETE FROM children WHERE parent_id=?")
             ->execute([$parent_id]);

        // delete parent
        $conn->prepare("DELETE FROM parents WHERE id=?")
             ->execute([$parent_id]);

        // delete user account
        $conn->prepare("DELETE FROM users WHERE id=?")
             ->execute([$parent['user_id']]);

        echo "<div class='alert alert-success'>Parent deleted successfully</div>";

    } else {
        echo "<div class='alert alert-danger'>Parent not found</div>";
    }
}
?>

<?php
$parents = $conn->query("
    SELECT parents.id, parents.name, users.email 
    FROM parents 
    JOIN users ON parents.user_id = users.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($parents) > 0): ?>
<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

    <?php foreach ($parents as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['email']) ?></td>
        <td>
            <a href="?delete=<?= $p['id'] ?>"
               onclick="return confirm('Are you sure you want to delete this parent?')"
               class="btn btn-danger btn-sm">
               Delete
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>No parents found.</p>
<?php endif; ?>
