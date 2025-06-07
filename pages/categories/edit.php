<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    mysqli_query($conn, "DELETE FROM category_table WHERE id = $delete_id");
    header("Location: index.php?page=categories/edit&id=$game_id");
    exit;
}

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update existing
    if (isset($_POST['category_name'])) {
        foreach ($_POST['category_name'] as $id => $name) {
            $name = trim($name);
            if ($name !== '') {
                $safe_name = mysqli_real_escape_string($conn, $name);
                mysqli_query($conn, "UPDATE category_table SET category_name = '$safe_name' WHERE id = $id");
            }
        }
    }

    // Add new
    if (isset($_POST['new_category_name'])) {
        foreach ($_POST['new_category_name'] as $name) {
            $name = trim($name);
            if ($name !== '') {
                $safe_name = mysqli_real_escape_string($conn, $name);
                mysqli_query($conn, "INSERT INTO category_table (category_name, game_id) VALUES ('$safe_name', $game_id)");
            }
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM category_table WHERE game_id = $game_id");
$game = mysqli_fetch_assoc(mysqli_query($conn, "SELECT game_name FROM game_table WHERE id = $game_id"));
?>

<main class="container">
    <div class="container-md bg-color2 p-4 my-5">
        <h1 class="text-white font2 display-6"><?= htmlspecialchars($game['game_name']) ?> - Manage Categories</h1>

        <form method="POST" id="categoryForm">
            <input type="hidden" name="game_id" value="<?= $game_id ?>">

            <table class="table table-primary mt-4 bg-info" id="categoryTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories && mysqli_num_rows($categories) > 0):
                        $i = 1;
                        while ($row = mysqli_fetch_assoc($categories)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <input type="text" name="category_name[<?= $row['id'] ?>]" 
                                       value="<?= htmlspecialchars($row['category_name']) ?>" 
                                       class="form-control editable" oninput="checkNewInputs()">
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this category?')">Delete</button>
                                </form>
                                <a href="index.php?page=category_value/read&id=<?= $row['id'] ?>" 
                                   class="btn btn-info btn-sm">Read</a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>

            <button type="button" class="btn btn-success" onclick="addRow()">Add Category</button>
            <button type="submit" class="btn btn-primary" id="saveBtn" disabled>Save Changes</button>
        </form>

        <a href="<?= BASE_URL ?>/index.php?page=game/read&id=<?= $game_id ?>" class="btn btn-secondary text-white mt-3">Back</a>
    </div>
</main>

<script>
function addRow() {
    const table = document.getElementById('categoryTable').getElementsByTagName('tbody')[0];
    const newRow = document.createElement('tr');
    const index = table.rows.length + 1;

    newRow.innerHTML = `
        <td>${index}</td>
        <td><input type="text" name="new_category_name[]" class="form-control new-input" oninput="checkNewInputs()"></td>
        <td><span class="text-muted">Will be added</span></td>
    `;
    table.appendChild(newRow);
    checkNewInputs();
}

function checkNewInputs() {
    const inputs = document.querySelectorAll('.new-input, .editable');
    let shouldEnable = false;
    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            shouldEnable = true;
        }
    });
    document.getElementById('saveBtn').disabled = !shouldEnable;
}
</script>
