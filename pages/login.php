<?php
include_once(__DIR__ . '/../include/config.php');

session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists
    $query = mysqli_query($conn, "SELECT * FROM admin_table WHERE email='$email' LIMIT 1");
    $admin = mysqli_fetch_assoc($query);

    if ($admin && $password == $admin['password']) {
        $_SESSION['admin'] = true;
        echo '<script>alert("logged in")</script>';
        // $_SESSION['admin_email'] = $admin['email'];
        header("Location: index.php?page=home");
        exit;
    } else {
        echo "<script>alert('Login failed. Please check your email or password.');</script>";
    }
}
include_once(__DIR__ . '/../include/header.php');
?>

<main class="container">
    <div class="container-md bg-color2 p-4" style="max-width: 800px; margin-top: 25vh;">
        <h1 class="text-white font2 display-6 ">Login</h1>

        <form action="" method="post" class="text-white font1 mt-3">
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="text" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <button class="btn btn-light bg-color2 mt-3 text-white" type="submit" name="login">Login >></button>
            </div>
        </form>
    </div>
</main>
