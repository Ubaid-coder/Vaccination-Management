<?php
include("../config/db.php");

if(isset($_POST['register'])){

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // insert into users
    $stmt = $conn->prepare("INSERT INTO users (email,password,role) VALUES (?,?,?)");
    $stmt->execute([$email,$password,$role]);

    $user_id = $conn->lastInsertId();

    if($role == "parent"){
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        $stmt = $conn->prepare("INSERT INTO parents (user_id,name,phone,address) VALUES (?,?,?,?)");
        $stmt->execute([$user_id,$name,$phone,$address]);
    }

    if($role == "hospital"){
        $hospital_name = $_POST['hospital_name'];
        $address = $_POST['address'];
        $location = $_POST['location'];

        $stmt = $conn->prepare("INSERT INTO hospitals (user_id,hospital_name,address,location) VALUES (?,?,?,?)");
        $stmt->execute([$user_id,$hospital_name,$address,$location]);
    }

    echo "Registration Successful <a href='login.php'>Login</a>";
}
?>

<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>

<h2>Register</h2>

<form method="post">

<select name="role" required>
<option value="">Select Role</option>
<option value="parent">Parent</option>
<option value="hospital">Hospital</option>
</select><br><br>

<input type="email" name="email" placeholder="Email" required><br><br>
<input type="password" name="password" placeholder="Password" required><br><br>

<h4>Parent Info</h4>
<input type="text" name="name" placeholder="Name"><br><br>
<input type="text" name="phone" placeholder="Phone"><br><br>
<textarea name="address" placeholder="Address"></textarea><br><br>

<h4>Hospital Info</h4>
<input type="text" name="hospital_name" placeholder="Hospital Name"><br><br>
<input type="text" name="location" placeholder="Location"><br><br>

<button name="register">Register</button>

</form>

</body>
</html>
