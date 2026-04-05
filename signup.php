<!DOCTYPE html>
<html >
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
<input type="name" id="name" name="name" maxlenth="3" placeholder="name" required>
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
<a href="index.php">have an acconte</a>
</form>
<?php 
include 'db.php';
if(isset($_POST["signup"])){
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$sql = "insert into users (name,email,password) values('$name','$email','$password')";
if(mysqli_query($conn,$sql)){
    echo 'signup succesfly';
}else{
    echo 'unsuccesfly signup';
}
mysqli_close($conn);
}
?>
</body>
</html>
<?php 

?>