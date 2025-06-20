<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$char_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$char_query = $conn->prepare("SELECT char_name FROM character_table WHERE id = ?");
$char_query->bind_param("i", $char_id);
$char_query->execute();
$char_data = $char_query->get_result()->fetch_assoc();
$char_query->close();
?>

<!-- Alert -->
<?php while (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['flash'] ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endwhile; ?>

<main class="container">
    <div class="container-md bg-color2 px-5 my-5 row row-cols-1">
        <h1 class="text-white my-4 font2 display-6 p-0"><?= htmlspecialchars($char_data['char_name']) ?> - Skill</h1>

<?php
if (isset($_POST['submit'])) {
    $name = trim($_POST["name"]);
    $type = trim($_POST["type"]);
    $order = (int) $_POST['order'];
    $desc = $_POST['desc'];

    $errors = [];

    if (empty($name)) {
        $errors[] = "Skill name is required.";
    }
    if(empty($type)){
        $errors[] = "Skill type is required.";
    }
    if(empty($desc)){
        $errors[] = "Skill descrption required.";
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM skill_table WHERE char_id = ? AND skill_order = ?");
    $stmt->bind_param("ii", $char_id, $order);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $errors[] = "A skill with the same order already exists.";
    }

    $uploadDir = __DIR__ . '/../../uploads/skill/';
    [$newfilename, $uploadErrors] = uploadFile($_FILES["icon"], $uploadDir, 'skill_');
    $errors = array_merge($errors, $uploadErrors ?? []);

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, 
        "INSERT INTO skill_table 
        (char_id, skill_name, skill_icon, skill_desc, skill_type, skill_order) 
            VALUES (?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issssi",
            $char_id, $name, $newfilename, $desc, $type, $order);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo "<script>alert('Skill Added!'); window.location.href = 'index.php?page=character&id=".$char_id."';</script>";
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
            <label for="name" class="col-sm-2 col-form-label">Skill Name</label>
            <div class="col-sm-10">
                <input type="text" name="name" class="form-control" id="name" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="type" class="col-sm-2 col-form-label">Skill Type</label>
            <div class="col-sm-10">
                <input type="text" name="type" class="form-control" id="type" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6 mx-0">
                <label for="order" class="col col-form-label">Skill Order <strong>(Can't be edited next time)</strong></label>
                <input type="number" name="order" class="form-control" id="order" required>
            </div>
            <div class="col-6 mx-0">
                <label for="icon" class="col col-form-label">Skill Icon</label>
                <input type="file" name="icon" id="icon" class="col-6 form-control" placeholder="Input a category" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="desc" class="form-label">Skill Description</label>
            <textarea name="desc" class="form-control" id="desc" rows="7" required></textarea>
        </div>
        
        <div class="my-4 d-flex justify-content-end align-items-center gap-2">
            <button type="submit" name="submit" class="btn btn-success h-100">Submit</button>
            <a href="<?= BASE_URL ?>/index.php?page=character&id=<?= $char_id ?>" class="btn btn-secondary text-white">Back</a>
        </div>
    </form>

    </div>
</main>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('desc');
</script>

</body></html>