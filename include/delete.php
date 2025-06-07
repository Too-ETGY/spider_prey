<!-- <?php    
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

?>
<main class="containe-fluid d-flex justify-content-center align-items-center">
    <div class="container bg-secondary my-5 p-4 mx-3">
        <h1>Add Game</h1>
        
    <?php
    if (isset($_POST['submit'])) {
        // Sanitize and validate inputs
        $name = trim($_POST["name"]);
        $dupes = trim($_POST['dupes_name'] ?? '');
        $skill = trim($_POST['skill_name'] ?? '');
        $amplifier = trim($_POST['stat_amplifier'] ?? '');

        $filename = $_FILES["icon"]["name"] ?? '';
        $tmpName = $_FILES["icon"]["tmp_name"] ?? '';
        $fileSize = $_FILES["icon"]["size"] ?? 0;
        
        $errors = [];
        $newfilename = '';

        // === Text validations ===
        if (empty($name)) {
            $errors[] = "Game name is required.";
        }

        // === Image validations ===
        if (empty($filename)) {
            $errors[] = "Game icon is required.";
        } else {
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
            $allowedExts = ['png', 'jpg', 'jpeg', 'webp'];
            $fileType = mime_content_type($tmpName);
            $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($fileType, $allowedTypes) || !in_array($fileExt, $allowedExts)) {
                $errors[] = "Only PNG, JPG, or WEBP images are allowed.";
            }

            if ($fileSize > 10 * 1024 * 1024) {
                $errors[] = "Image must be under 2MB.";
            }

            if (!getimagesize($tmpName)) {
                $errors[] = "Uploaded file is not a valid image.";
            }
        }

        // === If valid, save image and insert ===
        if (empty($errors)) {
            $newfilename = uniqid('game_', true) . '.' . $fileExt;
            $uploadDir = __DIR__ . '/../../uploads/';
            $uploadPath = $uploadDir . $newfilename;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($tmpName, $uploadPath)) {
                $name = mysqli_real_escape_string($conn, htmlspecialchars($name));
                $dupes = mysqli_real_escape_string($conn, htmlspecialchars($dupes));
                $skill = mysqli_real_escape_string($conn, htmlspecialchars($skill));
                $amplifier = mysqli_real_escape_string($conn, htmlspecialchars($amplifier));

                $query = "INSERT INTO game_table (game_name, game_icon, dupes_name, skill_name, stat_amplifier)
                        VALUES ('$name', '$newfilename', '$dupes', '$skill', '$amplifier')";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    echo "<script>alert('Game Added!'); document.location.href = 'index.php?page=game';</script>";
                } else {
                    echo "<p style='color:red;'>Database error: " . mysqli_error($conn) . "</p>";
                }
            } else {
                echo "<p style='color:red;'>Failed to upload image file.</p>";
            }
        } else {
            foreach ($errors as $error) {
                echo "<p style='color:red;'>$error</p>";
            }
        }
    }
    ?>

        
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Game Name</label>
            <input type="text" name="name" id="name" required>
        </div>
        
        <div class="form-group">
            <label for="icon">Game Icon</label>
            <input type="file" name="icon" id="icon" required>
        </div>
        
        <div class="form-group">
            <label for="dupes_name">What's the dupes system called</label>
            <input type="text" name="dupes_name" id="dupes_name">
        </div>
        
        <div class="form-group">
            <label for="skill_name">What's the skill system called</label>
            <input type="text" name="skill_name" id="skill_name">
        </div>
        
        <div class="form-group">
            <label for="stat_amplifier">What stat amplifier does it have</label>
            <input type="text" name="stat_amplifier" id="stat_amplifier"></input>
        </div>
        
        <div style="margin-top: 20px;">
            <input type="submit" name="submit" value="submit" class="btn btn-success">
            <a href="<?= BASE_URL?>/index.php?page=game" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
</main>


<?php 
// include_once(__DIR__ . '/../../include/footer.php'); 
?> -->