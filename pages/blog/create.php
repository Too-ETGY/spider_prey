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
        <h1 class="text-white font2 display-6">Add Blog</h1>

<?php
if (isset($_POST['Add'])) {
    // Sanitize and validate inputs
    $title = trim($_POST["title"]);
    $gId = trim($_POST['game_id']);
    $desc = $_POST['desc'];

    $errors = [];
    // === Text validations ===
    if (empty($title)) {
        $errors[] = "Title is required.";
    }

    $filename = $_FILES["icon"]["name"] ?? '';
    $tmpName = $_FILES["icon"]["tmp_name"] ?? '';
    $fileSize = $_FILES["icon"]["size"] ?? 0;

    $newfilename = '';

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
            $errors[] = "Image must be under 10MB.";
        }

        if (!getimagesize($tmpName)) {
            $errors[] = "Uploaded file is not a valid image.";
        }
    }

    // === If valid, save image and insert ===
    if (empty($errors)) {
        $newfilename = uniqid('blog_', true) . '.' . $fileExt;
        $uploadDir = __DIR__ . '/../../uploads/blog/';
        $uploadPath = $uploadDir . $newfilename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($tmpName, $uploadPath)) {
            // Prepare & bind
            $stmt = mysqli_prepare($conn, "INSERT INTO blog_table (game_id, blog_title, blog_img, blog_desc) 
            VALUES (?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "isss", $gId, $title, $newfilename, $desc);
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    echo "<script>alert('Blog Added!'); window.location.href = 'index.php?page=blog';</script>";
                } else {
                    echo "<p style='color:red;'>Database error: " . mysqli_error($conn) . "</p>";
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "<p style='color:red;'>Statement preparation failed: " . mysqli_error($conn) . "</p>";
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

<form action="" method="post" enctype="multipart/form-data" id="gameForm" class="text-white font1 mt-4">
        
    <div class="row mb-3">
            <label for="title" class="col-sm-2 col-form-label">Title</label>
            <div class="col-sm-10">
                <input type="text" name="title" class="form-control" id="title" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="title" class="col-sm-2 col-form-label">Game</label>
            <div class="col-sm-10">
                <select name="game_id" class="form-select border border-dark rounded-1" aria-label="Default select example" required>
                    <option value="" selected disabled>Select Game</option>
                    <?php
                    $get_game = mysqli_query($conn, "SELECT id, game_name FROM game_table");
                    if (mysqli_num_rows($get_game) > 0) {
                        while($game_data = mysqli_fetch_assoc($get_game)){
                            echo '<option value="'.$game_data["id"].'">'.htmlspecialchars($game_data["game_name"]).'</option>';
                        }
                    } else {
                        echo '<option disabled>No games available</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="icon" class="col-sm-2 col-form-label">Game Icon</label>
            <div class="col-sm-10">
                <input class="form-control border border-dark rounded-1" name="icon" type="file" id="icon" required>
            </div>
        </div>

        <div class="row mb-3 align-items-center">
            <label for="title" class="col-sm-2 col-form-label">Published Date</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" aria-label="Disabled input example" id="title" value="<?= date("Y-m-d");?>"  disabled readonly>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="desc" class="form-label">Description</label>
            <textarea name="desc" class="form-control" id="desc" rows="7" required></textarea>
        </div>
        
        
        <div class="mt-4 d-flex justify-content-end align-items-center gap-2">
            <button type="submit" name="Add" class="btn btn-success h-100">Submit</button>
            <a href="<?= BASE_URL?>/index.php?page=blog" class="btn btn-secondary text-white">Back</a>
        </div>

</form>
</div>
</main>

<?php 
// include_once(__DIR__ . '/../../include/footer.php'); 
mysqli_close($conn);
?>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('desc');
</script>

</body>
</html>