<?php
session_start();
$user_id = $_SESSION['userid'];
if (!isset($_SESSION['userid'])) {
    header('location: index.php');
    exit();
}



include 'db.php';

$error_message = "";

$selectedTab = "all";
if (isset($_GET['tab'])) {
    $requestedTab = $_GET['tab'];
    $allowedTabs = array("all", "active", "completed");
    if (in_array($requestedTab, $allowedTabs, true)) {
        $selectedTab = $requestedTab;
    }
}

$searchText = "";
if (isset($_GET['search'])) {
    $searchText = $_GET['search'];
}

$searchQuerySuffix = "";
if ($searchText != "") {
    $searchQuerySuffix = "&search=" . urlencode($searchText);
}

$allTabLink = "home.php?tab=all" . $searchQuerySuffix;
$activeTabLink = "home.php?tab=active" . $searchQuerySuffix;
$completedTabLink = "home.php?tab=completed" . $searchQuerySuffix;

$allTabClass = $selectedTab == "all" ? "btn selected" : "btn";
$activeTabClass = $selectedTab == "active" ? "btn selected" : "btn";
$completedTabClass = $selectedTab == "completed" ? "btn selected" : "btn";

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
    $dueDate = $_POST['due_date'];
    $priority = $_POST['priority'];

    $validatedDueDate = null;
    if ($dueDate != "") {
        $dueDateObject = DateTime::createFromFormat('Y-m-d', $dueDate);
        if ($dueDateObject === false || $dueDateObject->format('Y-m-d') != $dueDate) {
            $error_message = "Due date is not a valid date";
        } else {
            $validatedDueDate = $dueDate;
        }
    }

    $validatedPriority = "medium";
    $allowedPriorities = array("low", "medium", "high");
    if ($priority != "") {
        if (in_array($priority, $allowedPriorities, true)) {
            $validatedPriority = $priority;
        } else {
            $error_message = "Priority must be low, medium or high";
        }
    }

    if ($error_message == "") {
        $stmt = $conn->prepare("INSERT INTO todos (title, status, due_date, priority, user_id) VALUES (?, 0, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $validatedDueDate, $validatedPriority, $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            header('location: home.php');
            exit();
        }
        $stmt->close();
        $error_message = "Could not add the todo";
    }
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
    $dueDate = $_POST['newDueDate'];
    $priority = $_POST['newPriority'];

    $validatedDueDate = null;
    if ($dueDate != "") {
        $dueDateObject = DateTime::createFromFormat('Y-m-d', $dueDate);
        if ($dueDateObject === false || $dueDateObject->format('Y-m-d') != $dueDate) {
            $error_message = "Due date is not a valid date";
        } else {
            $validatedDueDate = $dueDate;
        }
    }

    $validatedPriority = "medium";
    $allowedPriorities = array("low", "medium", "high");
    if ($priority != "") {
        if (in_array($priority, $allowedPriorities, true)) {
            $validatedPriority = $priority;
        } else {
            $error_message = "Priority must be low, medium or high";
        }
    }

    if ($error_message == "") {
        $stmt = $conn->prepare("UPDATE todos SET title = ?, due_date = ?, priority = ? WHERE todo_id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $title, $validatedDueDate, $validatedPriority, $todoId, $user_id);
        $stmt->execute();
        $stmt->close();
        header('location: home.php');
        exit();
    }
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
                <input type="date" name="due_date" id="due_date">
                <select name="priority" id="priority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
                <input type="submit" name="add" value="Add">
            </form>
            <?php if (!empty($error_message)): ?>
                <p style="color: red; margin-top: 10px;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </nav>
        <section>
            <div class="todo-filters">
                <div class="buttons">
                    <a class="<?php echo $allTabClass; ?>" href="<?php echo $allTabLink; ?>">All</a>
                    <a class="<?php echo $activeTabClass; ?>" href="<?php echo $activeTabLink; ?>">Active</a>
                    <a class="<?php echo $completedTabClass; ?>" href="<?php echo $completedTabLink; ?>">Completed</a>
                </div>
                <form action="home.php" method="GET">
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($selectedTab); ?>">
                    <input type="text" name="search" placeholder="Search by title" value="<?php echo htmlspecialchars($searchText); ?>">
                    <input type="submit" value="Search">
                </form>
            </div>
            <?php
            $stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY todo_id DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $filteredTodos = array();
            while ($row = $result->fetch_assoc()) {
                if ($selectedTab == "active" && $row["status"] != 0) {
                    continue;
                }
                if ($selectedTab == "completed" && $row["status"] != 1) {
                    continue;
                }
                if ($searchText != "" && stripos($row["title"], $searchText) === false) {
                    continue;
                }
                $filteredTodos[] = $row;
            }

            if (count($filteredTodos) == 0) {
                if ($searchText != "") {
                    echo "<p class='empty-state'>No todos match your search.</p>";
                } else {
                    echo "<p class='empty-state'>No todos here yet.</p>";
                }
            } else {
                echo "<div class='container'>";
                foreach ($filteredTodos as $row) {
                    $id = $row["todo_id"];
                    $title = htmlspecialchars($row["title"]);
                    $statusHtml = $row["status"] == 1 ? "checked" : "";
                    $jsTitle = htmlspecialchars($row["title"], ENT_QUOTES, 'UTF-8');

                    $dueDate = $row["due_date"];
                    $jsDueDate = "";
                    $dueDateHtml = "";
                    if ($dueDate != null) {
                        $jsDueDate = htmlspecialchars($dueDate, ENT_QUOTES, 'UTF-8');
                        $dueDateFormatted = date('M j, Y', strtotime($dueDate));
                        $dueDateHtml = "<span class='due-date'>Due: $dueDateFormatted</span>";
                    }

                    $priority = $row["priority"];
                    $priorityLabel = ucfirst($priority);
                    $jsPriority = htmlspecialchars($priority, ENT_QUOTES, 'UTF-8');

                    $isOverdue = false;
                    if ($dueDate != null && $row["status"] == 0) {
                        $isOverdue = $dueDate < date('Y-m-d');
                    }
                    $overdueClass = $isOverdue ? " overdue" : "";

                    echo "
                    <div class='todo$overdueClass'>
                       <input type='checkbox' onchange='changeStatus($id," . $row["status"] . ")' name='status' $statusHtml >
                       <div class='todo-details'>
                           <p>$title</p>
                           <div class='todo-meta'>
                               $dueDateHtml
                               <span class='priority-badge priority-badge-$priority'>$priorityLabel</span>
                           </div>
                       </div>
                       <div class='actions'>
                       <button onclick='edit($id,`$jsTitle`,`$jsDueDate`,`$jsPriority`)'>Edit</button>
                       <button onclick='delet($id)'>Delete</button>
                       </div>
                    </div>
                    ";
                }
                echo "</div>";
            }
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
        <input type="date" name="newDueDate" id="newDueDate" value="">
        <select name="newPriority" id="newPriority">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
        </select>
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

        function edit(id, title, dueDate, priority) {
            document.getElementById('edit-checker').style.display = 'flex';
            document.getElementById('editInput').value = id;
            document.getElementById('newTitle').value = String(title);
            document.getElementById('newDueDate').value = String(dueDate);
            document.getElementById('newPriority').value = String(priority);
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