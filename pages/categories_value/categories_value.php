<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT category_name, game_id FROM category_table WHERE id = $category_id"));
$game = mysqli_fetch_assoc(mysqli_query($conn, "SELECT game_name FROM game_table WHERE id = " . $category['game_id']));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = __DIR__ . "/../../uploads/category/";

    if (isset($_POST['add'])) {
        $name = trim($_POST["value_name"]);
        $errors = [];

        if (empty($name)) {
            $errors[] = "Name is required.";
        }

        [$imagePath, $uploadErrors] = uploadFile($_FILES["icon"], $targetDir, 'category_');
        $errors = array_merge($errors, $uploadErrors ?? []);

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO category_value_table (category_id, catg_value_name, catg_value_icon) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $category_id, $name, $imagePath);
            $stmt->execute();
            $stmt->close();
        }
    } 
    elseif (isset($_POST['edit'])) {
        $id = (int) $_POST['id'];
        $name = trim($_POST["value_name"]);
        $errors = [];

        if (empty($name)) {
            $errors[] = "Name is required.";
        }
        [$imagePath, $uploadErrors] = uploadFile($_FILES["icon"], $targetDir, 'category_');
        $errors = array_merge($errors, $uploadErrors ?? []);

        if (empty($errors)) {
            if ($imagePath) {
                // Delete old image
                $stmt = $conn->prepare("SELECT catg_value_icon FROM category_value_table WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->bind_result($oldIcon);
                $stmt->fetch();
                $stmt->close();

                deleteFile($targetDir.$oldIcon);

                $stmt = $conn->prepare("UPDATE category_value_table SET catg_value_name = ?, catg_value_icon = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $imagePath, $id);
            } else {
                $stmt = $conn->prepare("UPDATE category_value_table SET catg_value_name = ? WHERE id = ?");
                $stmt->bind_param("si", $name, $id);
            }
            $stmt->execute();
            $stmt->close();
        }
    } 
    elseif (isset($_POST['delete'])) {
        $id = (int) $_POST['id'];

        $stmt = $conn->prepare("SELECT catg_value_icon FROM category_value_table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($icon);
        $stmt->fetch();
        $stmt->close();

        deleteFile($targetDir.$icon);

        $stmt = $conn->prepare("DELETE FROM category_value_table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['flash'] = "Category value deleted.";
    }

    header("Location: index.php?page=categories_value&id=$category_id");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM category_value_table WHERE category_id = $category_id");
?>

<main class="container">
    <div class="container-md bg-color2 px-5 my-5 row row-cols-1">
        <h1 class="text-white my-4 font2 display-6 p-0"><?= htmlspecialchars($game['game_name']) ?> 
        -> <?= htmlspecialchars($category['category_name']) ?></h1>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['flash']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" class="text-white font1 p-0">
            <div class="input-group my-3 align-items-start gap-3">
                <input type="file" name="icon" accept="image/*" class="form-control rounded" required>
                <div class="form-floating mb-3">
                    <input type="text" name="value_name" class="form-control rounded" id="floatingInput" placeholder="Enter name" required>
                    <label for="floatingInput">Category name</label>
                </div>
                <button name="add" class="btn btn-success rounded">Submit</button>
            </div>
        </form>

        <table class="table text-white table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Icon & Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td id="column_'.$row['id'].'">
                        <img src="'.BASE_URL.'/uploads/category/' .$row['catg_value_icon'] . '" class="border" style="width:40px;height:40px;object-fit:cover;"><br>
                        ' . htmlspecialchars($row['catg_value_name']) . '
                    </td>';
                echo '<td class="d-flex align-items-center border">
                        <button onclick="edit('.$row['id'].')" class="btn btn-warning text-white me-2">Edit</button>
                        <form method="post">
                            <input type="hidden" name="id" value="'.$row['id'].'">
                            <button class="btn btn-danger" name="delete">Delete</button>
                        </form>
                      </td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>

        <div class="my-4 d-flex justify-content-end gap-3">
            <a href="index.php?page=categories&id=<?= $category['game_id'] ?>" class="btn btn-secondary">Previous</a>
            <a href="index.php?page=game/read&id=<?= $category['game_id'] ?>" class="btn btn-secondary">BACK</a>
        </div>
    </div>
</main>

<script>
let activeEditId = null;
function edit(id) {
    if (activeEditId && activeEditId !== id) {
        const oldCell = document.getElementById("column_" + activeEditId);
        oldCell.innerHTML = oldCell.dataset.original;
    }

    const column = document.getElementById("column_" + id);
    activeEditId = id;
    column.dataset.original = column.innerHTML;
    const brElem = column.querySelector("br");
    let name = "";
    if(brElem && brElem.nextSibling) {
        name = brElem.nextSibling.textContent.trim();
    }

    column.innerHTML = '';
    const form = document.createElement("form");
    form.method = "POST";
    form.enctype = "multipart/form-data";
    form.className = "d-flex align-items-center gap-2 flex-column";

    const input = document.createElement("input");
    input.type = "text";
    input.name = "value_name";
    input.required = true;
    input.value = name;
    input.className = "form-control";

    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.name = "icon";
    fileInput.accept = "image/*";
    fileInput.className = "form-control";

    const idInput = document.createElement("input");
    idInput.type = "hidden";
    idInput.name = "id";
    idInput.value = id;

    const submit = document.createElement("button");
    submit.name = "edit";
    submit.textContent = "Save";
    submit.className = "btn btn-success";

    const cancel = document.createElement("button");
    cancel.type = "button";
    cancel.textContent = "Cancel";
    cancel.className = "btn btn-secondary";
    cancel.onclick = function() {
        column.innerHTML = column.dataset.original;
        activeEditId = null;
    };

    form.appendChild(input);
    form.appendChild(fileInput);
    form.appendChild(idInput);
    form.appendChild(submit);
    form.appendChild(cancel);
    column.appendChild(form);
}
</script>
