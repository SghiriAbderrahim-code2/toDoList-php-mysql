<?php
session_start();
include 'db.php';

$error_message = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    
    // Check if user exists
    if ($res->num_rows == 1) {
        $row = $res->fetch_assoc();
        // Verify password hash
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['name'];
            $_SESSION['userid'] = $row['user_id'];
            
            // Redirect before any HTML is sent
            header("location: home.php");
            exit();
        } else {
            $error_message = "Email or password is not correct";
        }
    } else {
        $error_message = "Email or password is not correct";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>PHP Starter - Login</title>
    <link rel="stylesheet" href="style/login-signin.css">
</head>

<body>

    <form method="POST" >
        <label>
            Email
            <br>
            <input type="email" id="email" name="email" placeholder="email" required>
        </label>
        <label>
            Password
            <br>
            <input type="password" id="password" name="password" placeholder="password" required>
        </label>
        <input type="submit" value="login" name="login">
        <a href="signup.php">Create a new account</a>
        
        <?php if (!empty($error_message)): ?>
            <p style="color: red; margin-top: 10px;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </form>

</body>

</html>