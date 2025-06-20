<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$char_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$char_query = $conn->prepare("SELECT * FROM tier_with_char WHERE char_id = ?");
$char_query->bind_param("i", $char_id);
$char_query->execute();
$char_data = $char_query->get_result()->fetch_assoc();
$char_query->close();

$current_img = $char_data['char_icon'];
$game_id = $char_data['game_id'];

$ctg_query = $conn->prepare("SELECT * FROM character_with_categories WHERE char_id = ?");
$ctg_query->bind_param("i", $char_id);
$ctg_query->execute();
$result = $ctg_query->get_result();
$curr_ctg = [];
while ($ctg = $result->fetch_assoc()) {
    $curr_ctg[] = $ctg;
}
$ctg_query->close();


?>

<!-- Alert -->
<?php while (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['flash'] ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endwhile; ?>

<main class="container">
    <div class="container-md bg-color2 px-5 my-5 row row-cols-1">
        <h1 class="text-white my-4 font2 display-6 p-0"><?= htmlspecialchars($char_data['char_name']) ?> - Edit Character</h1>

<?php
if (isset($_POST['edit'])) {
    $name = trim($_POST["name"]);
    $tier_id = isset($_POST["tier_id"]) && trim($_POST["tier_id"]) !== '' ? (int) $_POST["tier_id"] : null;
    $speciality = trim($_POST['speciality'] ?? '');

    $base_stat = $_POST['base_stat_name'];
    $base_value = $_POST['base_stat_value'];

    $bonus_stat = isset($_POST['bonus_stat_name']) && is_array($_POST['bonus_stat_name']) ? $_POST['bonus_stat_name'] : [];
    $bonus_value = isset($_POST['bonus_stat_value']) && is_array($_POST['bonus_stat_value']) ? $_POST['bonus_stat_value'] : [];

    $bonus_stat_result = implode(', ', array_map('trim', $bonus_stat));
    $bonus_value_result = implode(', ', array_map('trim', $bonus_value));

    $c_catg = $_POST['c_category'];

    $errors = [];

    if (empty($name)) {
        $errors[] = "Character name is required.";
    }

    if(empty($base_stat) || empty($base_value)){
        $errors[] = "Must include atleast one stat at max level";
    } else{
        $base_stat_result = implode(', ', array_map('trim', $base_stat));
        $base_value_result = implode(', ', array_map('trim', $base_value));
    }

    if(empty($c_catg)){
        $errors[] = "Must belong to atleast one category";
    } else{
        $char_category_result = implode(',', array_map('trim', $c_catg));

    }

    $uploadDir = __DIR__ . '/../../uploads/char/';
    $newfilename = $current_img;

    if (!empty($_FILES['icon']['name'])) {
        if (!empty($current_img)) {
            deleteFile($uploadDir . $current_img);
        }
        [$newfilename, $errorMessage] = uploadFile($_FILES['icon'], $uploadDir, "game_");
        $errors = array_merge($errors, $errorMessage ?? []);
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, 
        "UPDATE character_table SET  
            tier_id = ?, 
            char_name = ?, 
            char_icon = ?, 
            char_base_stat = ?, 
            char_base_stat_value = ?, 
            char_bonus_stat = ?, 
            char_bonus_stat_value = ?, 
            char_speciality = ? 
            WHERE id = ?");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isssssssi",
            $tier_id, $name, $newfilename, 
            $base_stat_result, $base_value_result, $bonus_stat_result, $bonus_value_result, $speciality, $char_id);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                // $last_id = mysqli_insert_id($conn);

                $stmt2 = mysqli_prepare($conn, "CALL update_character_with_categories(?, ?);");
                mysqli_stmt_bind_param($stmt2, "is", $char_id, $char_category_result);

                if(mysqli_stmt_execute($stmt2)){
                    echo "<script>alert('Character Updated!'); window.location.href = 'index.php?page=character&id=".$char_id."';</script>";
                } else{
                    echo "<script>alert('Something wrong when updating category'); 
                    window.location.href = 'index.php?page=character&id=".$char_id."';</script>";
                }
                mysqli_stmt_close($stmt2);

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
            <label for="name" class="col-sm-2 col-form-label">Character Name</label>
            <div class="col-sm-10">
                <input type="text" name="name" class="form-control bg-warning-subtle" id="name" value="<?= htmlspecialchars($char_data['char_name']) ?>" required>
            </div>
        </div>

        <?php if (!empty($current_img)) : ?>
        <center>
            <img src="<?= BASE_URL ?>/uploads/char/<?= $current_img ?>" alt="Current Icon" style="max-width: 130px;" class="h-auto my-2">
        </center>
        <?php endif; ?>
        <div class="row mb-3">
            <label for="icon" class="col-sm-2 col-form-label">Character Icon</label>
            <div class="col-sm-10">
                <input class="form-control" name="icon" type="file" id="icon">
                <small>Leave if you don't want to change the image</small>
            </div>
        </div>

        <div class="row mb-3">
            <label for="title" class="col-sm-2 col-form-label">Tier <small>(May be left)</small></label>
            <div class="col-sm-10">
                <?php
                $selectedTier = $char_data['tier_id'] ?? '';
                $selectedTierName = $char_data['tier_name'] != null ? htmlspecialchars($char_data['tier_name']) :'(No Rank)';?>
                <select name="tier_id" class="form-select border border-dark rounded-1 
                    <?=$selectedTier!=''?'bg-warning-subtle':''?>">
                    <?php
                    $get_tier = mysqli_query($conn, "SELECT id, tier_name FROM tier_table WHERE game_id = $game_id ORDER BY tier_order");
                    if (mysqli_num_rows($get_tier) > 0) {

                        echo '<option value="' . $selectedTier . '" selected class="fw-bolder">' . $selectedTierName . '</option>';

                        while ($tier_data = mysqli_fetch_assoc($get_tier)) {
                            if ($tier_data['id'] != $char_data['tier_id']) {
                                echo '<option value="' . $tier_data['id'] . '">'
                                    . htmlspecialchars($tier_data['tier_name']) . '</option>';
                            }
                        }
                    } else {
                        echo '<option disabled>No tiers available</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="speciality" class="col-sm-2 col-form-label">Specialty <small>(May be left)</small></label>
            <div class="col-sm-10">
                <input type="text" name="speciality" class="form-control <?php echo $char_data['char_speciality'] ? 'bg-warning-subtle' : '' ?>" 
                id="speciality" value="<?=$char_data['char_speciality']?>">
            </div>
        </div>

        <div>
        <?php
        $get_category = mysqli_query($conn, "SELECT id, category_name FROM category_table WHERE game_id = $game_id");

        if (mysqli_num_rows($get_category) > 0) :
            while($category_row = mysqli_fetch_assoc($get_category)):
                $category_id = $category_row['id'];
                $category_name = $category_row['category_name'];

                $selected_value_id = '';
                foreach ($curr_ctg as $ctg) {
                    if ($ctg['category_id'] == $category_id) {
                        $selected_value_id = $ctg['catg_value_id'];
                        break;
                    }
                }

                $is_selected = $selected_value_id !== '';
                ?>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><?= htmlspecialchars($category_name) ?></label>
                    <div class="col-sm-10">
                        <select name="c_category[]" 
                                class="form-select border border-dark rounded-1 bg-warning-subtle" 
                                aria-label="Default select example">

                            <!-- <option value="" di>Select <?= htmlspecialchars($category_name) ?></option> -->

                            <?php
                            $get_c_value = mysqli_query($conn, "SELECT id, catg_value_name FROM category_value_table WHERE category_id = $category_id");
                            if (mysqli_num_rows($get_c_value) > 0) :
                                while($value_c_row = mysqli_fetch_assoc($get_c_value)): 
                                    $value_id = $value_c_row['id'];
                                    $value_name = $value_c_row['catg_value_name'];
                                    ?>
                                    <option value="<?= $value_id ?>" <?= $value_id == $selected_value_id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($value_name) ?>
                                    </option>
                                <?php endwhile;
                            else: ?>
                                <option disabled>No values available</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            <?php endwhile;
        else: ?>
            <p class="text-muted">No categories available yet.</p>
        <?php endif; ?>
        </div>

        <?php
        $base_stat_names = explode(',', $char_data['char_base_stat'] ?? '');
        $base_stat_values = explode(',', $char_data['char_base_stat_value'] ?? '');
        $bonus_stat_names = explode(',', $char_data['char_bonus_stat'] ?? '');
        $bonus_stat_values = explode(',', $char_data['char_bonus_stat_value'] ?? '');
        ?>

        <div class="row mb-3">
            <!-- BASE STATS -->
            <div class="col-md-6">
                <label class="form-label">BASE STAT (Max Level)</label>
                <div id="baseStatContainer" class="border rounded mb-2 p-3 w-100">
                    <?php
                    $base_count = max(count($base_stat_names), 1);
                    for ($i = 0; $i < $base_count; $i++):
                        $name = trim($base_stat_names[$i] ?? '');
                        $value = trim($base_stat_values[$i] ?? '');
                        $hasData = $name !== '' || $value !== '';
                    ?>
                    <div class="row base-stat-entry <?= $hasData ? 'bg-warning-subtle' : '' ?> rounded p-2 mb-2">
                        <div class="col-5 mb-1">
                            <input type="text" placeholder="Name" name="base_stat_name[]" class="form-control" value="<?= htmlspecialchars($name) ?>">
                        </div>
                        <div class="col-5 mb-1">
                            <input type="text" placeholder="Value" name="base_stat_value[]" class="form-control" value="<?= htmlspecialchars($value) ?>">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-outline-danger text-white remove-stat">✖</button>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                <button type="button" id="addStatBtn1" class="btn btn-warning mt-2">+ Add Base Stat</button>
            </div>

            <!-- BONUS STATS -->
            <div class="col-md-6">
                <label class="form-label">BONUS STAT (Max Level)</label>
                <div id="bonusStatContainer" class="border rounded mb-2 p-3 w-100">
                    <?php
                    $bonus_count = max(count($bonus_stat_names), 1);
                    for ($i = 0; $i < $bonus_count; $i++):
                        $name = trim($bonus_stat_names[$i] ?? '');
                        $value = trim($bonus_stat_values[$i] ?? '');
                        $hasData = $name !== '' || $value !== '';
                    ?>
                    <div class="row bonus-stat-entry <?= $hasData ? 'bg-warning-subtle' : '' ?> rounded p-2 mb-2">
                        <div class="col-5 mb-1">
                            <input type="text" placeholder="Name" name="bonus_stat_name[]" class="form-control" value="<?= htmlspecialchars($name) ?>">
                        </div>
                        <div class="col-5 mb-1">
                            <input type="text" placeholder="Value" name="bonus_stat_value[]" class="form-control" value="<?= htmlspecialchars($value) ?>">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-outline-danger text-white remove-stat">✖</button>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                <button type="button" id="addStatBtn2" class="btn btn-warning mt-2">+ Add Bonus Stat</button>
            </div>
        </div>

        
        <div class="my-4 d-flex justify-content-end align-items-center gap-2">
            <button type="submit" name="edit" class="btn btn-success h-100">Submit</button>
            <a href="<?= BASE_URL ?>/index.php?page=character&id=<?= $char_id ?>" class="btn btn-secondary text-white">Back</a>
        </div>
    </form>

    </div>
</main>

<script>
// Base Stat
document.getElementById('addStatBtn1').addEventListener('click', function () {
    const container = document.getElementById('baseStatContainer');
    const entry = document.querySelector('.base-stat-entry').cloneNode(true);
    entry.querySelectorAll('input').forEach(input => input.value = '');
    entry.classList.remove('bg-warning-subtle');
    container.appendChild(entry);
});
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-stat')) {
        const container = document.getElementById('baseStatContainer');
        if (container.querySelectorAll('.base-stat-entry').length > 1) {
            e.target.closest('.base-stat-entry').remove();
        }
    }
});

// Bonus Stat
document.getElementById('addStatBtn2').addEventListener('click', function () {
    const container = document.getElementById('bonusStatContainer');
    const entry = document.querySelector('.bonus-stat-entry').cloneNode(true);
    entry.querySelectorAll('input').forEach(input => input.value = '');
    entry.classList.remove('bg-warning-subtle');
    container.appendChild(entry);
});
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-stat')) {
        const container = document.getElementById('bonusStatContainer');
        if (container.querySelectorAll('.bonus-stat-entry').length > 1) {
            e.target.closest('.bonus-stat-entry').remove();
        }
    }
});
</script>

</body></html>