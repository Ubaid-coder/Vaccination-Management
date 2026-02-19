<?php include("layout.php"); ?>
<?php include("../config/db.php"); ?>

<h2>Admin Dashboard</h2>

<?php
$parents = $conn->query("SELECT COUNT(*) FROM parents")->fetchColumn();
$hospitals = $conn->query("SELECT COUNT(*) FROM hospitals")->fetchColumn();
$reports = $conn->query("SELECT COUNT(*) FROM vaccination_reports")->fetchColumn();
?>

<div class="row">

<div class="col-md-4">
<div class="card p-3 bg-primary text-white">
<h4>Total Parents</h4>
<h2><?= $parents ?? 0 ?></h2>
</div>
</div>

<div class="col-md-4">
<div class="card p-3 bg-success text-white">
<h4>Total Hospitals</h4>
<h2><?= $hospitals ?? 0 ?></h2>
</div>
</div>

<div class="col-md-4">
<div class="card p-3 bg-warning text-white">
<h4>Total Reports</h4>
<h2><?= $reports ?? 0 ?></h2>
</div>
</div>

</div>
