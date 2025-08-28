<?php
session_start();
if (!isset($_SESSION['logged_in']) or $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <div class="centered-wrapper">
        <div class="form-container" style="width: 60%; min-height: 90%;">
            <h3>To-Do List</h3>
            <?php
            $name = $_SESSION["username"];
            echo "<h2>Hello, $name</h2>";
            ?>

            <button type="button" class="btn btn-primary btn-lg" style="margin-top: 20px;" id="add-btn"
                onclick="showAddTodoDialog()">
                <i class="fa fa-plus"></i> Add New Task
            </button>

            <div>
                <button type="button" class="btn btn-danger"
                    style="opacity: 0.8; position: absolute; bottom: 40px; right: 40px;"
                    onclick="window.location.href='logout.php';">
                    Log out
                </button>
            </div>

            <?php
            include 'database.php';

            $userid = $_SESSION["userid"];

            $sql_cmd = "SELECT * FROM `todolist` WHERE userid = '$userid'";
            $result = mysqli_query($db_connection, $sql_cmd);

            if (isset($_POST['add-todo-btn'])) {
                $task = $_POST['add-todo'];

                if (!empty($task)) {
                    $sql_cmd = "INSERT INTO `todolist`(`userid`, `todo`) VALUES (?, ?)";
                    $stmt = mysqli_prepare($db_connection, $sql_cmd);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'ss', $userid, $task);
                        $result_query = mysqli_stmt_execute($stmt);

                        if (!$result_query) {
                            echo "<div class='alert alert-danger error'>Something Went wrong... Please try again!!</div>";
                        } else {
                            header("Refresh:0");
                        }
                    } else {
                        echo "<div class='alert alert-danger error'>Error preparing SQL statement.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger error'>Task can not be empty.</div>";
                }
            }

            if (isset($_POST['edit-todo-btn'])) {
                $row_id = $_POST['row-id'];
                $task = $_POST['edit-todo'];

                if (!empty($task) && $row_id !== -1) {
                    $sql_cmd = "UPDATE `todolist` SET `todo` = ? WHERE `id` = ?";
                    $stmt = mysqli_prepare($db_connection, $sql_cmd);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'si', $task, $row_id);
                        $result_query = mysqli_stmt_execute($stmt);

                        if (!$result_query) {
                            echo "<div class='alert alert-danger error'>Something Went wrong... Please try again!!</div>";
                        } else {
                            header("Refresh:0");
                        }
                    } else {
                        echo "<div class='alert alert-danger error'>Error preparing SQL statement.</div>";
                    }
                }
            }

            if (isset($_POST['dlt-todo-btn'])) {
                $row_id = $_GET['id'];
                $sql_cmd = "DELETE FROM `todolist` WHERE id = $row_id";
                $result_query = mysqli_query($db_connection, $sql_cmd);

                if (!$result_query) {
                    echo "<div class='alert alert-danger error'>Something Went wrong... Please try again!!</div>";
                } else {
                    header("Refresh:0");
                }
            }
            ?>

            <table class="table border rounded mt-4" style="width: 100%; margin-bottom: 80px;">
                <thead>
                    <tr>
                        <th scope="col" style="width: 10%; vertical-align: middle;">#</th>
                        <th class="text-center" scope="col" style="width: auto; vertical-align: middle;">Tasks</th>
                        <th class="text-center" scope="col" style="width: 16%; vertical-align: middle;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sno = 1;
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <th scope="row" style="vertical-align: middle;">
                            <?php echo $sno++; ?>
                        </th>
                        <td class="text-justify" style="text-align: left; padding: 0 12px; vertical-align: middle;">
                            <?php echo $row['todo']; ?>
                        </td>
                        <td class="text-center" style="vertical-align: middle;">
                            <ul class="list-inline m-0">
                                <li class="list-inline-item">
                                    <button
                                        onclick="showEditTodoDialog(<?php echo $row['id']; ?>, '<?php echo rawurlencode($row['todo']); ?>')"
                                        name="updt-todo-btn" id="updt-todo-btn" class="btn btn-success btn-sm rounded-1"
                                        style="opacity: 0.9;" type="submit" data-toggle="tooltip" data-placement="top"
                                        title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </li>
                                <li class="list-inline-item">
                                    <form action="index.php?id=<?php echo $row['id']; ?>" method="post">
                                        <button name="dlt-todo-btn" id="dlt-todo-btn"
                                            class="btn btn-danger btn-sm rounded-1" style="opacity: 0.9;" type="submit"
                                            data-toggle="tooltip" data-placement="top" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="add-todo-container" class="modal">
        <form class="form modal-content" action="index.php" method="post">
            <div class="form-container" style="backdrop-filter: blur(40px);">
                <div class="input-group mb-3">
                    <input class="shadow-input-wrapped" style="width: 88%;" type="text" id="add-todo" name="add-todo"
                        placeholder="Enter your todo task here..." autocomplete="off">
                    <button class="colored-btn-wrapped" style="width: 12%;" name="add-todo-btn" id="add-todo-btn"
                        type="submit">Add</button>
                </div>
            </div>
        </form>
    </div>
    <div id="edit-todo-container" class="modal">
        <form class="form modal-content" action="index.php" method="post">
            <div class="form-container" style="backdrop-filter: blur(40px);">
                <div class="input-group mb-3">
                    <input type="hidden" name="row-id" id="row-id" value="-1" />
                    <input class="shadow-input-wrapped" style="width: 88%;" type="text" id="edit-todo" name="edit-todo"
                        placeholder="Enter your updated todo task here..." autocomplete="off">
                    <button class="colored-btn-wrapped" style="width: 12%;" name="edit-todo-btn" id="edit-todo-btn"
                        type="submit">Update</button>
                </div>
            </div>
        </form>
    </div>
    <script>
    var modal1 = document.getElementById('add-todo-container');
    var modal2 = document.getElementById('edit-todo-container');

    window.onclick = function(event) {
        if (event.target == modal1) {
            modal1.style.display = "none";
            document.body.style.overflow = 'auto';
        } else if (event.target == modal2) {
            modal2.style.display = "none";
            document.body.style.overflow = 'auto';
        }
    }

    function showAddTodoDialog() {
        modal1.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function showEditTodoDialog(row_id, task) {
        task = decodeURIComponent(task);
        document.getElementById('row-id').value = row_id;
        document.getElementById('edit-todo').value = task;
        modal2.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    </script>
</body>

</html>