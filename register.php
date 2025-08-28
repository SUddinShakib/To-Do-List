<?php
session_start();
if (isset($_SESSION['logged_in']) and $_SESSION['logged_in'] == true) {
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <div class="centered-wrapper">
        <div class="form-container" id="form">
            <h3>Registration</h3>

            <?php
            if (isset($_POST["register"])) {
                $username = $_POST["username"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $repeat_password = $_POST["repeat_password"];

                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $errors = array();

                if (empty($username) or empty($email) or empty($password) or empty($repeat_password)) {
                    array_push($errors, "All fields are required.");
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errors, "Email is not valid.");
                }

                if (strlen($password) < 4) {
                    array_push($errors, "Password must be atleast 12 characters long.");
                }

                if ($password !== $repeat_password) {
                    array_push($errors, "Password does not match.");
                }

                require_once "database.php";

                $sql_cmd = "SELECT * FROM users WHERE username = '$username'";
                $result = mysqli_query($db_connection, $sql_cmd);
                $rowCount = mysqli_num_rows($result);

                if ($rowCount > 0) {
                    array_push($errors, "Username already exists.");
                }

                $sql_cmd = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($db_connection, $sql_cmd);
                $rowCount = mysqli_num_rows($result);

                if ($rowCount > 0) {
                    array_push($errors, "Email already exists.");
                }

                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger error'>$error</div>";
                    }
                } else {
                    $sql_cmd = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                    $sql_init = mysqli_stmt_init($db_connection);
                    $success = mysqli_stmt_prepare($sql_init, $sql_cmd);

                    if ($success) {
                        mysqli_stmt_bind_param($sql_init, "sss", $username, $email, $password_hash);
                        mysqli_stmt_execute($sql_init);
                        echo "<div class='alert alert-success'>You are registered successfully.</div>";
                    } else {
                        die("Something went wrong...");
                    }
                }
            }
            ?>

            <form class="form" action="register.php" method="post">
                <i class="icon fas fa-user"></i>
                <input class="shadow-input" type="text" name="username" placeholder="Username" autocomplete="off" />
                <br>

                <i class="icon fas fa-at"></i>
                <input class="shadow-input" type="email" name="email" placeholder="Email" autocomplete="off" />
                <br>

                <i class="icon fas fa-lock"></i>
                <input class="shadow-input" type="password" name="password" placeholder="Password" autocomplete="off" />
                <br>

                <i class="icon fas fa-lock"></i>
                <input class="shadow-input" type="password" name="repeat_password" placeholder="Repeat Password"
                    autocomplete="off" />
                <br>

                <button class="colored-btn" type="submit" name="register">Register</button>
            </form>

            <p>Already have an account? <a href="logout.php">Login now</a></p>
        </div>
    </div>

    <script src="./main.js"></script>

</body>

</html>