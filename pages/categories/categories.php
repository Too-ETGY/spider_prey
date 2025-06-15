<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT game_name FROM game_table WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>

<?php
// Handle insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['add'])){
        $catg_name = trim($_POST["catg_name"]);
        $errors = [];

        if (empty($catg_name)) {
            $errors[] = "Category name is required.";
        }

        // Check for duplicate category name for the same game
        $check = mysqli_prepare($conn, "SELECT COUNT(*) FROM category_table WHERE game_id = ? AND category_name = ?");
        $check->bind_param("is", $game_id, $catg_name);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            $errors[] = "Category already exists.";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO category_table (game_id, category_name) VALUES (?, ?)");
            $stmt->bind_param("is", $game_id, $catg_name);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=categories&id=" . $game_id);
            exit;

        } else {
            foreach ($errors as $error) {
                $_SESSION['flash'] = $error;
            }
        }
    }

    if (isset($_POST['edit'])) {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $conn->prepare("SELECT id FROM category_table WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $catg_name = trim($_POST["catg_name"]);
                $errors = [];

                if (empty($catg_name)) {
                    $errors[] = "Category name is required.";
                }

                // Check for duplicate category name for the same game, excluding the current category
                $check = $conn->prepare("SELECT COUNT(*) FROM category_table WHERE game_id = ? AND category_name = ? AND id != ?");
                $check->bind_param("isi", $game_id, $catg_name, $id);
                $check->execute();
                $check->bind_result($count);
                $check->fetch();
                $check->close();

                if ($count > 0) {
                    $errors[] = "Category already exists.";
                }

                if (empty($errors)) {
                    // Perform UPDATE, not INSERT
                    $updateStmt = $conn->prepare("UPDATE category_table SET category_name = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $catg_name, $id);
                    $updateStmt->execute();
                    $updateStmt->close();

                    // $_SESSION['flash'] = 'Category updated successfully.';
                } else {
                    foreach ($errors as $error) {
                        $_SESSION['flash'] = $error;
                    }
                }
            }
            $stmt->close();
            header("Location: index.php?page=categories&id=" . $game_id);
            exit;
        } else {
            $_SESSION['flash'] = 'Invalid category selected.';
            header("Location: index.php?page=categories&id=" . $game_id);
            exit;
        }
    }

    if(isset($_POST['delete'])){
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $conn->prepare("SELECT id FROM category_table WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $delStmt = $conn->prepare("DELETE FROM category_table WHERE id = ?");
                $delStmt->bind_param("i", $id);
                $deleteSuccess = $delStmt->execute();

                // $_SESSION['flash'] = $deleteSuccess ? 'Category successfully deleted!' : 'Error deleting category.';
                $delStmt->close();
            } else{
                $_SESSION['flash'] = 'Error deleting category.';
            }

            $stmt->close();
            header("Location: index.php?page=categories&id=" . $game_id);
            exit;
        } else {
            $_SESSION['flash'] = 'Invalid category selected.';
            header("Location: index.php?page=categories&id=" . $game_id);
            exit;
        }
    }
}

// Get all categories for the current game
$categories_result = mysqli_query($conn, "SELECT * FROM category_table WHERE game_id = $game_id");
?>

<!-- Alert -->
<?php while (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['flash'] ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endwhile; ?>


<main class="container">
    <div class="container-md bg-color2 px-5 my-5 row row-cols-1">
        <h1 class="text-white my-4 font2 display-6 p-0"><?= htmlspecialchars($game['game_name']) ?> - Character Type</h1>

        <form action="" method="post" class="text-white font1 p-0">
            <div class="input-group my-3 align-items-center">
                <input type="text" name="catg_name" class="form-control rounded" placeholder="Input a category" required>
                <button name="add" class="btn btn-success ms-3 rounded">Submit</button>
            </div>
        </form>

        <table class="table text-white">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($categories_result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($categories_result)) {
                        echo '<tr>';
                        echo '<th scope="row">' . $no++ . '</th>';
                        echo '<td id="column_'.$row['id'].'">' . htmlspecialchars($row['category_name']) . '</td>';
                        echo 
                        '
                        <td class="d-flex align-items-center">
                            <a href="index.php?page=categories_value&id='.$row['id'].'" class="btn btn-info text-white">Go</a>
                            <button onclick="edit('.$row['id'].')" class="btn btn-warning text-white ms-3">Edit</button>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="'.$row['id'].'">
                                <input type="submit" value="Delete" name="delete" class="btn btn-danger ms-3 rounded">
                            </form>
                        </td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3"><div class="alert alert-warning">No categories available yet.</div></td></tr>';
                }
                ?>
            </tbody>
        </table>

        <div class="my-4 d-flex justify-content-end gap-3">
            <a href="<?= BASE_URL ?>/index.php?page=game/edit&id=<?= $game_id ?>" class="btn btn-secondary text-white mt-3">Back</a>
            <a href="<?= BASE_URL ?>/index.php?page=game/read&id=<?= $game_id ?>" class="btn btn-secondary text-white mt-3">BACKBACK</a>
        </div>
    </div>
</main>

<form action="" method="post" style="display: none;" id="put_form">
    <input type="hidden" name="id" value="">
    <input type="hidden" name="catg_name" value="">
    <input type="hidden" name="edit" value="edit">
</form>

<script>
    let currentEditing = null;
    let originalContent = "";

    function edit(id) {
        // Cancel previous edit if active
        if (currentEditing !== null) {
            cancelEdit(currentEditing, originalContent);
        }

        const column = document.getElementById("column_" + id);
        originalContent = column.innerHTML;
        currentEditing = id;

        const currentValue = column.textContent.trim();

        // Create form container
        const form = document.createElement("div");
        form.className = "d-flex align-items-center gap-2";

        // Input field
        const input = document.createElement("input");
        input.type = "text";
        input.name = "catg_name";
        input.value = currentValue;
        input.className = "form-control";
        input.required = true;

        // Hidden fields
        const hiddenId = document.createElement("input");
        hiddenId.type = "hidden";
        hiddenId.name = "id";
        hiddenId.value = id;

        const hiddenEdit = document.createElement("input");
        hiddenEdit.type = "hidden";
        hiddenEdit.name = "edit";
        hiddenEdit.value = "edit";

        // Save button
        const saveBtn = document.createElement("button");
        saveBtn.type = "submit";
        saveBtn.className = "btn btn-success btn-sm";
        saveBtn.textContent = "Save";

        // Cancel button
        const cancelBtn = document.createElement("button");
        cancelBtn.type = "button";
        cancelBtn.className = "btn btn-secondary btn-sm";
        cancelBtn.textContent = "Cancel";
        cancelBtn.onclick = () => cancelEdit(id, originalContent);

        // Form element
        const realForm = document.createElement("form");
        realForm.method = "POST";
        realForm.className = "d-flex align-items-center gap-2";

        realForm.appendChild(input);
        realForm.appendChild(hiddenId);
        realForm.appendChild(hiddenEdit);
        realForm.appendChild(saveBtn);
        realForm.appendChild(cancelBtn);

        column.innerHTML = "";
        column.appendChild(realForm);
    }

    function cancelEdit(id, content) {
        const col = document.getElementById("column_" + id);
        col.innerHTML = content;
        currentEditing = null;
        originalContent = "";
    }
</script>

</body></main>
