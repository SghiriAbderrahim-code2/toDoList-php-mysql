<?php 
session_start();
include 'db.php';

$message = "";

if(isset($_POST["signup"])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Encrypt the password using PHP's built-in securely hashed algorithm
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Use prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    
    if($stmt->execute()){
        $message = "Signup successful. You can now <a href='index.php'>login</a>.";
    } else {
        $message = "Unsuccessful signup.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style/login-signin.css">
</head>
<body>
    <form method="POST" >
        <label >
            Name
            <br>
            <input type="text" id="name" name="name" placeholder="name" required>
        </label>
        <label >
            Email
            <br>
            <input type="email" id="email" name="email" placeholder="email" required>
        </label>
        <label >
            Password
            <br>
            <input type="password" id="password" name="password" placeholder="password" required>
        </label>
        <input type="submit" value="signup" name="signup">
        <a href="index.php">Already have an account?</a>
        
        <?php if (!empty($message)): ?>
            <p style="margin-top: 10px;"><?php echo $message; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>