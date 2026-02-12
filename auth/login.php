<?php
session_start();
include("../config/db.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if($stmt->rowCount() == 1){

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] == "admin"){
                header("Location: ../admin/dashboard.php");
            }
            elseif($user['role'] == "parent"){
                header("Location: ../parent/dashboard.php");
            }
            elseif($user['role'] == "hospital"){
                header("Location: ../hospital/dashboard.php");
            }

        } else {
            echo "Wrong Password";
        }

    } else {
        echo "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>

<h2>Login</h2>

<form method="post">
<input type="email" name="email" placeholder="Email" required><br><br>
<input type="password" name="password" placeholder="Password" required><br><br>

<button name="login">Login</button>
</form>

</body>
</html>
