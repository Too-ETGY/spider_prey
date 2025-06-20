<?php    
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

?>
<main class="container">
    <div class="container-md bg-color2 p-4 my-5" style="max-width: 800px;">
        <h1 class="text-white font2 display-6">Add Game</h1>

<?php
if (isset($_POST['submit']) || isset($_POST['submit_redirect'])) {
    $name = trim($_POST["name"]);
    $dupes = trim($_POST['dupes_name'] ?? '');
    $skill = trim($_POST['skill_name'] ?? '');
    $amplifier = trim($_POST['stat_amplifier'] ?? '');
    $errors = [];

    if (empty($name)) {
        $errors[] = "Game name is required.";
    }

    $uploadDir = __DIR__ . '/../../uploads/game/';
    [$newfilename, $uploadErrors] = uploadFile($_FILES["icon"], $uploadDir, 'game_');
    $errors = array_merge($errors, $uploadErrors ?? []);

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO game_table (game_name, game_icon, dupes_name, skill_name, stat_amplifier) 
            VALUES (?, ?, ?, ?, ?)");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $name, $newfilename, $dupes, $skill, $amplifier);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                $last_id = mysqli_insert_id($conn);
                if (isset($_POST['submit_redirect'])) {
                    echo "<script>alert('Game Added!'); window.location.href = 'index.php?page=categories&id={$last_id}';</script>";
                } else {
                    echo "<script>alert('Added Game!'); window.location.href = 'index.php?page=game';</script>";
                }
            } else {
                echo "<p style='color:red;'>Database error: " . mysqli_error($conn) . "</p>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<p style='color:red;'>Statement preparation failed: " . mysqli_error($conn) . "</p>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>

    <form action="" method="post" enctype="multipart/form-data" id="gameForm" class="text-white font1 mt-4">
        <div class="row mb-3">
            <label for="name" class="col-sm-2 col-form-label">Game Name</label>
            <div class="col-sm-10">
                <input type="text" name="name" class="form-control" id="name" required>
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="icon" class="col-sm-2 col-form-label">Game Icon</label>
            <div class="col-sm-10">
                <input class="form-control" name="icon" type="file" id="icon" required>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="dupes_name">What's the dupes system called?</label>
            <input type="text" name="dupes_name" id="dupes_name" class="form-control" placeholder="Leave blank if none">
        </div>
        
        <div class="form-group mb-3">
            <label for="skill_name">What's the skill system called?</label>
            <input type="text" name="skill_name" id="skill_name" class="form-control" placeholder="Leave blank if none">
        </div>
        
        <div class="form-group mb-3">
            <label for="stat_amplifier">What kind of a stat amplifier does it have?</label>
            <input type="text" name="stat_amplifier" id="stat_amplifier" class="form-control" placeholder="Leave blank if none">
        </div>
        
        <div class="mt-4 d-flex justify-content-end align-items-center gap-2">
            <button type="submit" name="submit" class="btn btn-success h-100">Submit</button>
            <a href="<?= BASE_URL?>/index.php?page=game" class="btn btn-secondary text-white">Back</a>
        </div>
        <div class="mt-2 d-flex justify-content-end">
            <button type="submit" name="submit_redirect" class="btn btn-primary text-white">Submit and Add a Category</button>
        </div>
    </form>
</div>
</main>

<?php 
// include_once(__DIR__ . '/../../include/footer.php'); 
mysqli_close($conn);
?>