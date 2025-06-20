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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = trim($_POST['name']);
        $order = (int) $_POST['order'];
        $color = $_POST['color'];
        $errors = [];

        // Validate duplicate order
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tier_table WHERE game_id = ? AND tier_order = ?");
        $stmt->bind_param("ii", $game_id, $order);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $errors[] = "A tier with the same order already exists.";
        }

        if (!empty($name) && empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO tier_table (game_id, tier_name, tier_order, color_bg) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $game_id, $name, $order, $color);
            $stmt->execute();
            $stmt->close();
        } else {
            foreach ($errors as $e) {
                echo "<div class='alert alert-danger'>$e</div>";
            }
        }

    } elseif (isset($_POST['edit'])) {
        $id = (int) $_POST['id'];
        $name = trim($_POST['name']);
        $order = (int) $_POST['order'];
        $color = $_POST['color'];
        $errors = [];

        // Validate duplicate order (excluding self)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tier_table WHERE game_id = ? AND tier_order = ? AND id != ?");
        $stmt->bind_param("iii", $game_id, $order, $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $errors[] = "Another tier already uses this order.";
        }

        if (!empty($name) && empty($errors)) {
            $stmt = $conn->prepare("UPDATE tier_table SET tier_name = ?, tier_order = ?, color_bg = ? WHERE id = ?");
            $stmt->bind_param("sisi", $name, $order, $color, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            foreach ($errors as $e) {
                echo "<div class='alert alert-danger'>$e</div>";
            }
        }

    } elseif (isset($_POST['delete'])) {
        $id = (int) $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM tier_table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: index.php?page=tier/create&id=$game_id");
    exit;
}
?>

<!-- Alert -->
<?php while (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['flash'] ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endwhile; ?>


<main class="container">
    <div class="container-md bg-color2 px-5 my-5 row row-cols-1">
        <h1 class="text-white my-4 font2 display-6 p-0"><?= htmlspecialchars($game['game_name']) ?> - Manage Tier</h1>

        <form action="" method="post" class="text-white font1 p-0">
            <div class="input-group my-3 align-items-end justify-content-center">
            <div class="border p-3">
                <div class="form-floating mb-3">
                    <input type="text" name="name" class="form-control rounded" id="floatingInput" placeholder="Input a category" required>
                    <label for="floatingInput">What Tier?</label>
                </div>
                <div class="row">
                    <div class="col-6 mx-0">
                        <label for="order" class="col col-form-label">Order:</label>
                        <input type="number" name="order" class="form-control" id="order" required>
                    </div>
                    <div class="col-6 mx-0">
                        <label for="color" class="col col-form-label">Pick Color:</label>
                        <input type="color" name="color" id="color" class="col-6 form-control form-control-color" placeholder="Input a category" required>
                    </div>
                </div>
            </div>
            <button name="add" class="btn btn-success ms-3 rounded">Submit</button> 
            </div>
        </form>

<?php
$tierQuery = $conn->prepare("SELECT * FROM tier_table WHERE game_id = ? ORDER BY tier_order ASC");
$tierQuery->bind_param("i", $game_id);
$tierQuery->execute();
$tierResult = $tierQuery->get_result();
?>

        <table class="table table-light table-bordered">
            <thead>
                <tr>
                    <th scope="col">Order</th>
                    <th scope="col">Name</th>
                    <th scope="col">Color</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($tier = $tierResult->fetch_assoc()): ?>
                    <tr>
                        <form action="" method="post" id="form-display">
                            <td>
                                <input type="number" name="order" class="form-control" value="<?= $tier['tier_order'] ?>">
                            </td>
                            <td>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tier['tier_name']) ?>">
                            </td>
                            <td>
                                <input type="color" name="color" class="form-control form-control-color" value="<?= htmlspecialchars($tier['color_bg']) ?>">
                            </td>
                            <td>
                                <input type="hidden" name="id" value="<?= $tier['id'] ?>">
                                <button type="submit" name="edit" class="btn btn-primary btn-sm">Save</button>
                                <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete this tier?')">Delete</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="my-4 d-flex justify-content-end gap-3">
            <a href="<?= BASE_URL ?>/index.php?page=tier&id=<?= $game_id ?>" class="btn btn-secondary text-white mt-3">See Tier</a>
        </div>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const inputs = form.querySelectorAll("input");
    let isDirty = false;

    inputs.forEach(input => {
        input.addEventListener("change", () => {
            if (!isDirty) {
                isDirty = true;
                const warning = document.createElement("div");
                warning.className = "alert alert-warning mt-3";
                warning.textContent = "⚠️ You have unsaved changes!";
                form.parentElement.insertBefore(warning, form.nextSibling);
            }
        });
    });
});
</script>

</body></html>