<?php
session_start();
include("../config/db.php");

if($_SESSION['role'] != "parent"){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// get parent_id from parents table using user_id
$stmt = $conn->prepare("SELECT * FROM parents WHERE user_id = ?");
$stmt->execute([$user_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$parent){
    die("Parent record not found.");
}

$parent_id = $parent['id'];

if(isset($_POST['add_child'])){

    $child_name = $_POST['child_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    $stmt = $conn->prepare(
        "INSERT INTO children (parent_id, child_name, dob, gender)
         VALUES (?,?,?,?)"
    );

    $stmt->execute([$parent_id, $child_name, $dob, $gender]);

    echo "Child Added Successfully";
}
?>

<h2>Add Child</h2>

<form method="post">
<input type="text" name="child_name" placeholder="Child Name" required><br><br>
<input type="date" name="dob" required><br><br>

<select name="gender" required>
<option value="">Select Gender</option>
<option value="male">Male</option>
<option value="female">Female</option>
</select><br><br>

<button name="add_child">Add Child</button>
</form>
