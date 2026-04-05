<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('location: index.php');
    exit();
};
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
        <header class="slide">
            <h1>Hi <?php echo $_SESSION['username']; ?></h1>
        </header>
        <nav class="slide">
            <form action="" method="POST">
                <input type="text" name="title" id="title" required>
                <input type="submit" name="add" value="Add">
            </form>
        </nav>
        <section>
            <?php
            include 'db.php';
            $user_id = $_SESSION['userid'];
            $result = $conn->query("select * from todos where user_id=$user_id order by todo_id desc");
            echo "<div class='container'>";
            while ($row = $result->fetch_assoc()) {
                $id = $row["todo_id"];
                $title = $row["title"];
                $status = $row["status"] == 1 ? "cheked" : "";
                echo "
    <div class='todo'>
       <input type='checkbox' onchange='changeStatus($id," . $row["status"] . ")' name='status' $status >
       <p>$title</p>
       <div class='actions'>
       <button onclick='edit($id,`$title`)'>Edit</button>
       <button onclick='delet($id)'>Delete</button>
       </div>
    </div>
    ";
            }
            echo "</div>";
            if (isset($_POST["add"])) {
                $title = $_POST['title'];

                $sqlAdd = "insert into todos (title,status,user_id) values('$title',0,'$user_id')";
                if (mysqli_query($conn, $sqlAdd)) {
                    echo 'add succesfly';
                    header('location: home.php');
                } else {
                    echo 'unsuccesfly add';
                }
                mysqli_close($conn);
            }



            ?>
        </section>

    </main>
    <form class="checker" id="delet-checker" action="home.php" method="post">
        <input type="hidden" id='deletInput' name="deletedTodoId" value="">
        <p>
            are you sur
        </p>
        <div class="buttons">
            <div class="btn" onclick="hidde('delet-checker')">cancel</div>
            <button class="btn" type="submit">Ok</button>
        </div>
    </form>
    <form class="checker" id="edit-checker" action="home.php" method="post">
        <input type="hidden" id='editInput' name="editTodoId" value="">
        <input type="text" name="newTitle" id="newTitle" value="">
        <div class="buttons">
            <div class="btn" onclick="hidde('edit-checker')">cancel</div>
            <button class="btn" type="submit">Ok</button>
        </div>
    </form>
    <form id="status-checker" action="home.php" method="post" style="display: none;">
        <input type="hidden" id='statusIdInput' name="statusTodoId" value="">
        <input type="hidden" id='statusInput' name="statusTodo" value="">
        <button id="statusSubmit" type="submit">Ok</button>
    </form>
    <?php
    if (isset($_POST['deletedTodoId'])) {
        $todoId = $_POST['deletedTodoId'];
        $sqlDelet = "delete from todos where todo_id=$todoId";
        mysqli_query($conn, $sqlDelet);
        header('location: home.php');
    }
    if (isset($_POST['editTodoId'])) {
        $todoId = $_POST['editTodoId'];
        $title = $_POST['newTitle'];
        $sqlDelet = "UPDATE todos SET title = '$title' WHERE todo_id = $todoId;";
        mysqli_query($conn, $sqlDelet);
        header('location: home.php');
    }
    ?>

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

        function changeStatus(id,tatus){

        }

        function hidde(id) {
            document.getElementById(id).style.display = 'none';

        }
    </script>
</body>

</html>