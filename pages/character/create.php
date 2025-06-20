<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$game_query = $conn->prepare("SELECT game_name FROM game_table WHERE id = ?");
$game_query->bind_param("i", $game_id);
$game_query->execute();
$game_data = $game_query->get_result()->fetch_assoc();
$game_query->close();
?>

<!-- Alert -->
<?php while (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['flash'] ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endwhile; ?>

<main class="container">
    <div class="container-md bg-color2 px-5 my-5 row row-cols-1">
        <h1 class="text-white my-4 font2 display-6 p-0"><?= htmlspecialchars($game_data['game_name']) ?> - Add Character</h1>

<?php
if (isset($_POST['submit'])) {
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
    [$newfilename, $uploadErrors] = uploadFile($_FILES["icon"], $uploadDir, 'char_');
    $errors = array_merge($errors, $uploadErrors ?? []);

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, 
        "INSERT INTO character_table 
        (game_id, tier_id, char_name, char_icon, char_base_stat, char_base_stat_value, char_bonus_stat, char_bonus_stat_value, char_speciality) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iisssssss",
            $game_id, $tier_id, 
            $name, $newfilename, $base_stat_result, $base_value_result, $bonus_stat_result, $bonus_value_result, $speciality);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                $last_id = mysqli_insert_id($conn);

                $stmt2 = mysqli_prepare($conn, "CALL add_character_with_categories(?, ?);");
                mysqli_stmt_bind_param($stmt2, "is", $last_id, $char_category_result);

                if(mysqli_stmt_execute($stmt2)){
                    echo "<script>alert('Character Added!'); window.location.href = 'index.php?page=game/read&id=".$game_id."';</script>";
                } else{
                    echo "<script>alert('Something wrong when inserting category'); 
                    window.location.href = 'index.php?page=game/read&id=".$game_id."';</script>";
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
                <input type="text" name="name" class="form-control" id="name" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="icon" class="col-sm-2 col-form-label">Character Icon</label>
            <div class="col-sm-10">
                <input class="form-control" name="icon" type="file" id="icon" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="title" class="col-sm-2 col-form-label">Tier <small>(May be left)</small></label>
            <div class="col-sm-10">
                <select name="tier_id" class="form-select border border-dark rounded-1" aria-label="Default select example">
                    <option value="" selected>Select Tier</option>
                    <?php
                    $get_tier = mysqli_query($conn, "SELECT id, tier_name FROM tier_table WHERE game_id = $game_id ORDER BY tier_order");
                    if (mysqli_num_rows($get_tier) > 0) {
                        while($tier_row = mysqli_fetch_assoc($get_tier)){
                            echo '<option value="'.$tier_row["id"].'">'.htmlspecialchars($tier_row["tier_name"]).'</option>';
                        }
                    } else {
                        echo '<option disabled>No Tier yet</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="speciality" class="col-sm-2 col-form-label">Specialty <small>(May be left)</small></label>
            <div class="col-sm-10">
                <input type="text" name="speciality" class="form-control" id="speciality">
            </div>
        </div>

        <div>
        <?php
        $get_category = mysqli_query($conn, "SELECT id, category_name FROM category_table WHERE game_id = $game_id");
        if (mysqli_num_rows($get_category) > 0) :
            while($category_row = mysqli_fetch_assoc($get_category)):
                $category_id = $category_row['id'];
                $category_name = $category_row['category_name']
                ?>
                <div class="row mb-3">
                    <label for="title" class="col-sm-2 col-form-label"><?=htmlspecialchars($category_name)?></label>
                    <div class="col-sm-10">
                        <select name="c_category[]" 
                        class="form-select border border-dark rounded-1" aria-label="Default select example">

                            <option value="" selected disabled>Select <?=htmlspecialchars($category_name)?></option>
                            <?php
                            $get_c_value = mysqli_query($conn, "SELECT id, catg_value_name 
                            FROM category_value_table WHERE category_id = $category_id");

                            if (mysqli_num_rows($get_c_value) > 0) :
                                while($value_c_row = mysqli_fetch_assoc($get_c_value)):?>
                                    <option value="<?=$value_c_row['id']?>">
                                        <?=htmlspecialchars($value_c_row['catg_value_name'])?>
                                    </option>
                            <?php endwhile; endif;?>
                        </select>
                    </div>
                </div>
        <?php endwhile;
        else:
            echo '<option disabled>No Tier yet</option>';
        endif;
        ?>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">BASE STAT (Max Level)</label>
                <div id="baseStatContainer" class="col-md-6 border rounded mb-2 p-3 w-100">
                    <div class="row base-stat-entry">
                        <div class="col-5 mb-1">
                            <input type="text" placeholder="Name" name="base_stat_name[]" class="form-control" id="base_name" required>
                        </div>
                        <div class="col-5 mb-1">
                            <input type="text" placeholder="Value" name="base_stat_value[]" class="form-control" id="base_value" required>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-outline-danger text-white remove-stat" style="">✖</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="addStatBtn1" class="btn btn-warning mt-2">+ Add Base Stat</button>
            </div>

            <div class="col-md-6">
                <label class="form-label">BONUS STAT (Max Level)</label>
                <div id="bonusStatContainer" class="col-md-6 border rounded mb-2 p-3 w-100">
                    <div class="row bonus-stat-entry">
                        <div class="col-5 mb-1">
                            <input type="text" placeholder="Name" name="bonus_stat_name[]" class="form-control" id="bonus_name">
                        </div>
                        <div class="col-5 mb-1">
                            <input type="text" placeholder="Value" name="bonus_stat_value[]" class="form-control" id="bonus_value">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-outline-danger text-white remove-stat" style="">✖</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="addStatBtn2" class="btn btn-warning mt-2">+ Add Bonus Stat</button>
            </div>
        </div>
        
        <div class="my-4 d-flex justify-content-end align-items-center gap-2">
            <button type="submit" name="submit" class="btn btn-success h-100">Submit</button>
            <a href="<?= BASE_URL ?>/index.php?page=game/read&id=<?= $game_id ?>" class="btn btn-secondary text-white">Back</a>
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