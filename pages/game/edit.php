<?php    
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php?page=game");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM game_table WHERE id=$id");
if(mysqli_num_rows($result) == 0) {
    header("Location: index.php?page=game/read");
    exit();
}

$row = mysqli_fetch_assoc($result);
$current_img = $row['game_icon'];
?>

<main class="container">
<div class="container-md bg-color2 p-4 my-5" style="max-width: 800px;">
    <h1 class="text-white font2 display-6">Edit Game</h1>
    <a href="index.php?page=categories&id=<?=$id?>" class="btn btn-primary">Add a category</a>

<?php
// if (isset($_POST['update']) || isset($_POST['update_redirect'])) {
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['update']) || isset($_POST['update_redirect']))) {
    $name = trim($_POST["game_name"]);
    $dupes = trim($_POST['dupes_name'] ?? '');
    $skill = trim($_POST['skill_name'] ?? '');
    $amplifier = trim($_POST['stat_amplifier'] ?? '');

    $errors = [];
    if (empty($name)) {
        $errors[] = "Game name is required.";
    }

    $uploadDir = __DIR__ . '/../../uploads/game/';
    $newfilename = $current_img;

    if (!empty($_FILES['icon']['name'])) {
        if (!empty($current_img)) {
            deleteFile($uploadDir . $current_img);
        }
        [$newfilename, $errorMessage] = uploadFile($_FILES['icon'], $uploadDir, "game_");
        $errors = array_merge($errors, $errorMessage ?? []);
    }

    // === Update database ===
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE game_table SET 
            game_name = ?, 
            dupes_name = ?, 
            skill_name = ?, 
            stat_amplifier = ?, 
            game_icon = ? 
            WHERE id = ?");

        $stmt->bind_param("sssssi", $name, $dupes, $skill, $amplifier, $newfilename, $id);
        $result = $stmt->execute();

        if ($result) {
            $redirect = isset($_POST['update_redirect']) 
                ? "index.php?page=categories&id=".$id 
                : "index.php?page=game/read&id=".$id;
            echo "<script>alert('Game Updated!'); window.location.href = '$redirect';</script>";
        } else {
            echo "<p style='color:red;'>Query error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>

<form action="" method="post" enctype="multipart/form-data" class="text-white font1 mt-4">
    <div class="row mb-3">
        <label for="name" class="col-sm-2 col-form-label">Game Name</label>
        <div class="col-sm-10">
            <input type="text" name="game_name" id="game_name" 
            class="form-control <?php echo $row['game_name'] ? 'bg-warning-subtle' : ''; ?>" 
            value="<?php echo htmlspecialchars($row['game_name']); ?>" required>
        </div>
    </div>

    <?php if (!empty($current_img)) : ?>
        <center>
            <img src="<?= BASE_URL ?>/uploads/game/<?= $current_img ?>" alt="Current Icon" style="max-width: 130px;" class="h-auto my-2">
        </center>
    <?php endif; ?>
    <div class="row mb-3">
        <label for="icon" class="col-sm-2 col-form-label">Game Icon</label>
        <div class="col-sm-10">
            <input class="form-control" type="file" name="icon" id="icon">
            <small>Leave if you don't want to change the image</small>
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="dupes_name">What's the dupes system called</label>
        <input type="text" name="dupes_name" id="dupes_name" 
        value="<?php echo htmlspecialchars($row['dupes_name']); ?>"
        class="form-control <?php echo $row['dupes_name'] ? 'bg-warning-subtle' : ''; ?>" 
        placeholder="Leave blank if none">
    </div>

    <div class="form-group mb-3">
        <label for="skill_name">What's the skill system called</label>
        <input type="text" name="skill_name" id="skill_name" 
        value="<?php echo htmlspecialchars($row['skill_name']); ?>"
        class="form-control <?php echo $row['skill_name'] ? 'bg-warning-subtle' : ''; ?>" 
        placeholder="Leave blank if none">
    </div>

    <div class="form-group mb-3">
        <label for="stat_amplifier">What stat amplifier does it have</label>
        <input type="text" name="stat_amplifier" id="stat_amplifier" 
        value="<?php echo htmlspecialchars($row['stat_amplifier']); ?>"
        class="form-control <?php echo $row['stat_amplifier'] ? 'bg-warning-subtle' : ''; ?>" 
        placeholder="Leave blank if none">
    </div>

    <div class="mt-4 d-flex justify-content-end align-items-center gap-2">
        <button type="submit" name="update" class="btn btn-warning h-100">Update</button>
        <a href="<?= BASE_URL ?>/index.php?page=game/read&id=<?= $id ?>" class="btn btn-secondary text-white">Back</a>
    </div>
    <div class="mt-2 d-flex justify-content-end">
        <button type="submit" name="update_redirect" class="btn btn-primary text-white">Update and Add a Category</button>
    </div>
</form>
</div>
</main>

<?php 
mysqli_close($conn); 
?>
