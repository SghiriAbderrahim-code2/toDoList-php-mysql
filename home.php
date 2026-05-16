<?php
session_start();
$user_id = $_SESSION['userid'];
if (!isset($_SESSION['userid'])) {
    header('location: index.php');
    exit();
}



include 'db.php';



if (isset($_FILES["img"])) {
    $image = $_FILES['img']['name'];
    move_uploaded_file($_FILES['img']['tmp_name'],'img/'.$image);
    $stmt = $conn->prepare("UPDATE users set image=? where user_id=?");
    $stmt->bind_param("si", $image, $user_id);
    if ($stmt->execute()) {
        header('location: home.php');
        exit();
    }
    $stmt->close();
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: index.php');
    exit();
}

if (isset($_POST["add"])) {
    $title = $_POST['title'];

    $stmt = $conn->prepare("INSERT INTO todos (title, status, user_id) VALUES (?, 0, ?)");
    $stmt->bind_param("si", $title, $user_id);
    if ($stmt->execute()) {
        header('location: home.php');
        exit();
    }
    $stmt->close();
}

if (isset($_POST['deletedTodoId'])) {
    $todoId = $_POST['deletedTodoId'];
    
    $stmt = $conn->prepare("DELETE FROM todos WHERE todo_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $todoId, $user_id);
    $stmt->execute();
    $stmt->close();
    header('location: home.php');
    exit();
}

if (isset($_POST['editTodoId'])) {
    $todoId = $_POST['editTodoId'];
    $title = $_POST['newTitle'];

    $stmt = $conn->prepare("UPDATE todos SET title = ? WHERE todo_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $title, $todoId, $user_id);
    $stmt->execute();
    $stmt->close();
    header('location: home.php');
    exit();
}

if (isset($_GET['statusTodoId']) && isset($_GET['newStatus'])) {
    $todoId = $_GET['statusTodoId'];
    $status = $_GET['newStatus'];

    $stmt = $conn->prepare("UPDATE todos SET status = ? WHERE todo_id = ? AND user_id = ?");
    $stmt->bind_param("iii", $status, $todoId, $user_id);
    $stmt->execute();
    $stmt->close();
    header('location: home.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/home.css">
</head>

<body>
    <main>
        <header class="slide" >
            <div id="avatar">
<img 
            id="avatarImg" 
            src="
            <?php
            $stmt = $conn->prepare("SELECT image FROM users WHERE user_id = ? ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result){
                $row = $result->fetch_assoc();
                if ($row['image'] == null) {
                    $src='simple-user-default-icon-free-png.webp';
                }else{
$src = $row['image'];
                }
               echo "img/".$src; 
            }else{
                echo 'img/simple-user-default-icon-free-png.webp';
            }
            ?>
            "
            alt="avatar">
            <button onclick="document.getElementById('avatar-img-changer').style.display='flex'">Edit</button>
            </div>
            
            <h1>Hi <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <a href="?logout=true"><button id="logout">Logout</button></a>
        </header>
        <nav class="slide">
            <form action="" method="POST">
                <input type="text" name="title" id="title" required>
                <input type="submit" name="add" value="Add">
            </form>
        </nav>
        <section>
            <?php
            $stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY todo_id DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<div class='container'>";
            while ($row = $result->fetch_assoc()) {
                $id = $row["todo_id"];
                $title = htmlspecialchars($row["title"]);
                $statusHtml = $row["status"] == 1 ? "checked" : "";
                $jsTitle = htmlspecialchars($row["title"], ENT_QUOTES, 'UTF-8');
                
                echo "
                <div class='todo'>
                   <input type='checkbox' onchange='changeStatus($id," . $row["status"] . ")' name='status' $statusHtml >
                   <p>$title</p>
                   <div class='actions'>
                   <button onclick='edit($id,`$jsTitle`)'>Edit</button>
                   <button onclick='delet($id)'>Delete</button>
                   </div>
                </div>
                ";
            }
            echo "</div>";
            $stmt->close();
            ?>
        </section>

    </main>
    <form class="checker" id="delet-checker" action="home.php" method="post">
        <input type="hidden" id='deletInput' name="deletedTodoId" value="">
        <p>
            Are you sure?
        </p>
        <div class="buttons">
            <div class="btn" onclick="hidde('delet-checker')">Cancel</div>
            <button class="btn" type="submit">Ok</button>
        </div>
    </form>
    <form class="checker" id="edit-checker" action="home.php" method="post">
        <input type="hidden" id='editInput' name="editTodoId" value="">
        <input type="text" name="newTitle" id="newTitle" value="">
        <div class="buttons">
            <div class="btn" onclick="hidde('edit-checker')">Cancel</div>
            <button class="btn" type="submit">Ok</button>
        </div>
    </form>
<form class="checker" id="avatar-img-changer" action="home.php" method="post" enctype="multipart/form-data">
        <img 
            id="avatarImgChanger" src="img/simple-user-default-icon-free-png.webp" alt="avatar">

            <input type="file" name="img" id="changeAvatarInput" accept="image/*">
            <div class="buttons">
            <div class="btn" onclick="hidde('avatar-img-changer')">Cancel</div>
            <button class="btn" type="submit">Ok</button>
        </div>
    </form>
    <script>
        
        function delet(id) {
            document.getElementById('delet-checker').style.display = 'flex';
            document.getElementById('deletInput').value = id;
        }

        function edit(id, title) {
            document.getElementById('edit-checker').style.display = 'flex';
            document.getElementById('editInput').value = id;
            document.getElementById('newTitle').value = String(title);
        }

        function changeStatus(id, status){
            let newStatus = status == 1 ? 0 : 1;
            window.location.href = `home.php?statusTodoId=${id}&newStatus=${newStatus}`;    
        }

        function hidde(id) {
            document.getElementById(id).style.display = 'none';
        }

        document.getElementById('changeAvatarInput').addEventListener('change',(event)=>{
            const file = event.target.files[0];
            if (!file) return;
            const imgUrl = URL.createObjectURL(file);
            document.getElementById('avatarImgChanger').src = imgUrl;
        });
    </script>
</body>

</html>