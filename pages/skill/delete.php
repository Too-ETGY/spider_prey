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

// Fetch skill info
$stmt = $conn->prepare("SELECT skill_icon, skill_name FROM skill_table WHERE char_id = ? AND skill_order = ?");
$stmt->bind_param("ii", $char_id, $skill_order);
$stmt->execute();
$result = $stmt->get_result();
$skill = $result->fetch_assoc();
$stmt->close();

if (!$skill) {
    echo "<div class='alert alert-danger m-5'>Skill not found!</div>";
    exit;
}

// Handle deletion if confirmed
if (isset($_POST['confirm_delete'])) {
    // Delete image
    $imagePath = __DIR__ . '/../../uploads/skill/' . $skill['skill_icon'];
    deleteFile($imagePath);

    // Delete from DB
    $deleteStmt = $conn->prepare("DELETE FROM skill_table WHERE char_id = ? AND skill_order = ?");
    $deleteStmt->bind_param("ii", $char_id, $skill_order);

    if ($deleteStmt->execute()) {
        echo "<script>alert('Skill deleted successfully!'); window.location.href = 'index.php?page=character&id=$char_id';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to delete skill: " . $conn->error . "</div>";
    }
    $deleteStmt->close();
}
?>

<main class="container">
    <div class="container-md bg-color2 px-5 my-5 row row-cols-1">
        <h1 class="text-white my-4 font2 display-6 p-0">Delete Skill</h1>

        <div class="alert alert-warning">
            <p>Are you sure you want to delete the skill:</p>
            <h4><?= htmlspecialchars($skill['skill_name']) ?></h4>
        </div>

        <form method="POST" class="text-white font1">
            <div class="my-4 d-flex justify-content-end align-items-center gap-2">
                <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
                <a href="index.php?page=character&id=<?= $char_id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

</body></html>