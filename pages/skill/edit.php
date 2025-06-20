<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$char_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$skill_order = isset($_GET['order']) ? (int)$_GET['order'] : 0;

// Fetch character name
$char_query = $conn->prepare("SELECT char_name FROM character_table WHERE id = ?");
$char_query->bind_param("i", $char_id);
$char_query->execute();
$char_data = $char_query->get_result()->fetch_assoc();
$char_query->close();

// Fetch existing skill data
$stmt = $conn->prepare("SELECT * FROM skill_table WHERE char_id = ? AND skill_order = ?");
$stmt->bind_param("ii", $char_id, $skill_order);
$stmt->execute();
$skill = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$skill) {
    echo "<div class='alert alert-danger'>Skill not found!</div>";
    exit;
}

$current_img=$skill['skill_icon'];
?>

<!-- Flash Message -->
<?php while (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['flash'] ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endwhile; ?>

<main class="container">
    <div class="container-md bg-color2 px-5 my-5 row row-cols-1">
        <h1 class="text-white my-4 font2 display-6 p-0"><?= htmlspecialchars($char_data['char_name']) ?> - Edit Skill</h1>

<?php
if (isset($_POST['submit'])) {
    $name = trim($_POST["name"]);
    $type = trim($_POST["type"]);
    $desc = $_POST["desc"];
    $errors = [];

    if (empty($name)) $errors[] = "Skill name is required.";
    if (empty($type)) $errors[] = "Skill type is required.";
    if (empty($desc)) $errors[] = "Skill description is required.";

    // Handle image upload
    $newfilename = $skill['skill_icon']; // Default to existing
    if (!empty($_FILES['icon']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/skill/';
        [$newfilename, $uploadErrors] = uploadFile($_FILES["icon"], $uploadDir, 'skill_');
        $errors = array_merge($errors, $uploadErrors ?? []);

        // Delete old file if new one is uploaded successfully
        if (empty($uploadErrors) && $newfilename !== $skill['skill_icon']) {
            deleteFile($uploadDir . $skill['skill_icon']);
        }
    }

    if (empty($errors)) {
        $update = $conn->prepare("UPDATE skill_table SET skill_name = ?, skill_type = ?, skill_desc = ?, skill_icon = ? WHERE char_id = ? AND skill_order = ?");
        $update->bind_param("ssssii", $name, $type, $desc, $newfilename, $char_id, $skill_order);
        if ($update->execute()) {
            echo "<script>alert('Skill updated!'); window.location.href = 'index.php?page=character&id=$char_id';</script>";
        } else {
            echo "<p style='color:red;'>Update error: " . $conn->error . "</p>";
        }
        $update->close();
    } else {
        foreach ($errors as $e) {
            echo "<p style='color:red;'>$e</p>";
        }
    }
}
?>

<form action="" method="post" enctype="multipart/form-data" class="text-white font1 mt-4">

    <div class="row mb-3 align-items-center">
        <label for="order" class="col-sm-2 col-form-label">Current Skill Order</label>
        <div class="col-sm-10">
            <input type="text" class="form-control bg-secondary" aria-label="Disabled input example" id="order" value="<?= $skill_order;?>" disabled readonly>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Skill Name</label>
        <div class="col-sm-10">
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($skill['skill_name']) ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Skill Type</label>
        <div class="col-sm-10">
            <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($skill['skill_type']) ?>" required>
        </div>
    </div>

    <?php if (!empty($current_img)) : ?>
        <center>
            <img src="<?= BASE_URL ?>/uploads/skill/<?= $current_img ?>" alt="Current Icon" style="max-width: 130px;" class="h-auto my-2">
        </center>
    <?php endif; ?>
    <div class="row mb-3">
        <label for="icon" class="col-sm-2 col-form-label">Game Icon</label>
        <div class="col-sm-10">
            <input class="form-control" type="file" name="icon" id="icon">
            <small>Leave if you don't want to change the image</small>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Skill Description</label>
        <textarea name="desc" class="form-control" rows="7" required><?= htmlspecialchars($skill['skill_desc']) ?></textarea>
    </div>

    <div class="my-4 d-flex justify-content-end align-items-center gap-2">
        <button type="submit" name="submit" class="btn btn-warning h-100">Update</button>
        <a href="index.php?page=character&id=<?= $char_id ?>" class="btn btn-secondary text-white">Back</a>
    </div>
</form>
</div>
</main>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('desc');</script>
</body></html>