<?php
include("layout.php");
include("../config/db.php");
?>

<h2>Manage Hospitals</h2>

<?php
if (isset($_GET['delete'])) {

    $hospital_id = $_GET['delete'];

    // get hospital record
    $stmt = $conn->prepare("SELECT user_id FROM hospitals WHERE id=?");
    $stmt->execute([$hospital_id]);
    $hospital = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($hospital) {
        // delete hospital
        $conn->prepare("DELETE FROM hospitals WHERE id=?")
             ->execute([$hospital_id]);

        // delete user account
        $conn->prepare("DELETE FROM users WHERE id=?")
             ->execute([$hospital['user_id']]);

        echo "<div class='alert alert-success'>Hospital deleted successfully</div>";

    } else {
        echo "<div class='alert alert-danger'>Hospital not found</div>";
    }
}
?>

<?php
$hospitals = $conn->query("
    SELECT hospitals.id, hospitals.hospital_name,hospitals.location, hospitals.address, users.email
    FROM hospitals
    JOIN users ON hospitals.user_id = users.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($hospitals) > 0): ?>
<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Address</th>
        <th>Location</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

    <?php foreach ($hospitals as $h): ?>
    <tr>
        <td><?= $h['id'] ?></td>
        <td><?= htmlspecialchars($h['hospital_name']) ?></td>
        <td><?= htmlspecialchars($h['address']) ?></td>
        <td><?= htmlspecialchars($h['location']) ?></td>
        <td><?= htmlspecialchars($h['email']) ?></td>
        <td>
            <a href="?delete=<?= $h['id'] ?>"
               onclick="return confirm('Are you sure you want to delete this hospital?')"
               class="btn btn-danger btn-sm">
               Delete
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>No hospitals found.</p>
<?php endif; ?>
