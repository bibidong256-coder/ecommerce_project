<?php
session_start();
require "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        echo "Login successful";

    } else {
        echo "Invalid email or password";
    }
}
?>
 <form action="">
    <input name="email" placeholder="Email"><br>
    <input name="password" type="password"><br>
    <button>Login</button>
 </form>