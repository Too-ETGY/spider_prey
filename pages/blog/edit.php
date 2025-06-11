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
    header("Location: index.php?page=blog");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM view_blog_with_game_name WHERE id = $id");
if(mysqli_num_rows($result) == 0) {
    header("Location: index.php?page=blog/read");
    exit();
}

$row = mysqli_fetch_assoc($result);
$current_img = $row['blog_img'];
?>
<main class="container">
    <div class="container-md bg-color2 p-4 my-5" style="max-width: 800px;">
        <h1 class="text-white font2 display-6">Edit Blog</h1>

<?php
if (isset($_POST['update'])) {
    // Sanitize and validate inputs
    $title = trim($_POST["title"]);
    $gId = trim($_POST['game_id']);
    $desc = $_POST['desc'];

    $errors = [];
    // === Text validations ===
    if (empty($title)) {
        $errors[] = "Title is required.";
    }

    $newfilename = $current_img;

    // === Handle image upload ===
    if (!empty($_FILES['icon']['name'])) {
        $filename = $_FILES['icon']['name'];
        $tmpName = $_FILES['icon']['tmp_name'];
        $fileSize = $_FILES['icon']['size'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $fileType = mime_content_type($tmpName);
        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
        $allowedExts = ['png', 'jpg', 'jpeg', 'webp'];

        if (!in_array($fileType, $allowedTypes) || !in_array($fileExt, $allowedExts)) {
            $errors[] = "Only PNG, JPG, or WEBP images are allowed.";
        }

        if ($fileSize > 10 * 1024 * 1024) {
            $errors[] = "Image must be under 10MB.";
        }

        if (!getimagesize($tmpName)) {
            $errors[] = "Uploaded file is not a valid image.";
        }

        if (empty($errors)) {
            $uploadDir = __DIR__ . '/../../uploads/blog/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if ($current_img && file_exists($uploadDir . $current_img)) {
                unlink($uploadDir . $current_img);
            }

            $newfilename = uniqid('blog_', true) . "." . $fileExt;
            move_uploaded_file($tmpName, $uploadDir . $newfilename);
        }
    }    

    // === If valid, save image and insert ===
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE blog_table SET 
                blog_title = ?, 
                blog_img = ?, 
                blog_desc = ?, 
                game_id = ? 
                WHERE id = ?");

        $stmt->bind_param("sssii", $title, $newfilename, $desc, $gId, $id);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>alert('Game Updated!'); window.location.href = 'index.php?page=blog/read&id=".$id."';</script>";
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

<form action="" method="post" enctype="multipart/form-data" id="gameForm" class="text-white font1 mt-4">
        
    <div class="row mb-3">
            <label for="title" class="col-sm-2 col-form-label">Title</label>
            <div class="col-sm-10">
                <input type="text" name="title" class="form-control bg-warning-subtle" 
                id="title" value="<?= htmlspecialchars($row['blog_title']) ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="title" class="col-sm-2 col-form-label">Game</label>
            <div class="col-sm-10">
                <select name="game_id" class="form-select border border-dark rounded-1 bg-warning-subtle" required>
                    <?php
                    $get_game = mysqli_query($conn, "SELECT id, game_name FROM game_table");

                    if (mysqli_num_rows($get_game) > 0) {
                        // Correctly reference the game_id from the blog
                        echo '<option value="' . $row['game_id'] . '" selected class="fw-bolder">'
                            . htmlspecialchars($row['game_name']) . '</option>';

                        // Show other games except the one already selected
                        while ($game_data = mysqli_fetch_assoc($get_game)) {
                            if ($game_data['id'] != $row['game_id']) {
                                echo '<option value="' . $game_data['id'] . '">'
                                    . htmlspecialchars($game_data['game_name']) . '</option>';
                            }
                        }
                    } else {
                        echo '<option disabled>No games available</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        

        <?php if (!empty($current_img)) : ?>
            <center>
                <img src="<?= BASE_URL ?>/uploads/blog/<?= $current_img ?>" alt="Current Icon" style="max-width: 300px;" class="h-auto my-2">
            </center>
        <?php endif; ?>
        <div class="row mb-3">
            <label for="icon" class="col-sm-2 col-form-label">Game Icon</label>
            <div class="col-sm-10">
                <input class="form-control border border-dark rounded-1" name="icon" type="file" id="icon">
                <small>Leave if you don't want to change the image</small>
            </div>
        </div>

        <div class="row mb-3 align-items-center">
            <label for="title" class="col-sm-2 col-form-label">Updated Date</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" aria-label="Disabled input example" id="title" value="<?= date("Y-m-d");?>"  disabled readonly>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="desc" class="form-label">Description</label>
            <textarea name="desc" class="form-control" id="desc" rows="7" required>
                <?= $row['blog_desc'] ?>
            </textarea>
        </div>
        
        
        <div class="mt-4 d-flex justify-content-end align-items-center gap-2">
            <button type="submit" name="update" class="btn btn-success h-100">Update</button>
            <a href="<?= BASE_URL?>/index.php?page=blog/read&id=<?= $id ?>" class="btn btn-secondary text-white">Back</a>
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