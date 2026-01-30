<html>

<head>
    <title>PHP Starter</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <form method="POST">
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
        <a href="signup.php">creact a new acconte</a>
    </form>
    <?php
    include 'db.php';
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $sql = "select * from users where email='$email' and password='$password'";
        $res = mysqli_query($conn,$sql);
        if (mysqli_num_rows($res) == 1) {
            echo "successefly login";
        } else {
            
            echo "email or password is not correct";
        }
    }



    ?>
</body>

</html>