<?php
session_start();

include ("./includes/_dbconnect.php");

$user_exists = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verifying the password (plain text comparison)
        if ($password === $row["password"]) {
            $_SESSION["username"] = $row["user_name"];
            header("location: ./index.php");
            exit;
        } else {
            $user_exists = false;
        }
    } else {
        $user_exists = false;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./css/login.css">
    <title>Log In</title>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-4 login-part p-5">
                <form action="./login.php" method="post">
                    <h1 class="login-heading">Login</h1>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            aria-describedby="emailHelp" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !$user_exists): ?>
                    <br>
                    <div class='alert alert-danger' role='alert'>
                        Wrong credentials!
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-8 hero-image-part">
                <img src="./assets/login_bg.png" alt="hero-image">
            </div>
        </div>
    </div>
</body>

</html>