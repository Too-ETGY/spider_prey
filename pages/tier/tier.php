<?php
// echo "characters ". $_GET['id'];
include_once(__DIR__ . '/../../include/config.php');

session_start();
if(!isset($_GET['id'])) {
    header("Location: index.php?page=game");
    exit();
}

$id = (int) $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM game_table WHERE id = $id");

// Jika data tidak ditemukan, kembali ke halaman utama
if(mysqli_num_rows($result) == 0) {
    header("Location: index.php?page=game");
    exit();
}

// Ambil data untuk ditampilkan
$row = mysqli_fetch_assoc($result);
$name = $row['game_name'];
$dupes = $row['dupes_name'];
$skill = $row['skill_name'];
$amplifier = trim($row['stat_amplifier']);
$unique_things = $amplifier ? explode(',', $amplifier) : [];

include_once(__DIR__ . '/../../include/navbar_game_read.php');
?>

<style>
        body {
      background-color: #3b1144;
      font-family: sans-serif;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    td.tier-label {
      width: 60px;
      text-align: center;
      font-weight: bold;
      font-size: 2rem;
      color: white;
      vertical-align: top;
    }

    tr:nth-child(1) td.tier-label { background-color: purple; }
    tr:nth-child(2) td.tier-label { background-color: crimson; }
    tr:nth-child(3) td.tier-label { background-color: #9ee4ff; }
    tr:nth-child(4) td.tier-label { background-color: #fffab0; }

    td.character-cell {
      padding: 10px;
    }

    .character {
      display: inline-block;
      margin: 6px;
      text-align: center;
      width: 80px;
      color: white;
    }

    .character img {
      width: 80px;
      height: 80px;
      border-radius: 10px;
    }

    .character span {
      display: block;
      font-size: 0.75rem;
      margin-top: 4px;
    }
</style>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=keyboard_double_arrow_left" />

<div class="container-fluid px-0 d-flex align-items-center justify-content-center">
<main class="bg-color1 container my-5 mx-3 text-center text-white d-flex flex-column" style="min-height: 50vh;">
    <section class="d-flex flex-column justify-content-start mt-3 mx-1">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center">
                <h1 class="font2 display-6 my-0"><?= htmlspecialchars($name) ?></h1>
                <?php            
                if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):
                ?>
                    <a href='index.php?page=game/edit&id=<?=$id?>' class='btn btn-warning text-white my-auto ms-2'>Edit</a>
                <?php
                endif;
                ?>
            </div>
            <a href="<?= BASE_URL?>/index.php?page=game" class="btn btn-light bg-color1 text-white mt-0">Back</a>
        </div>
        <h4 class="d-flex justify-content-start font2 fw-normal fs-3 my-3">Tier List</h4>
    </section>

    <!-- <section class="bg-color4 mb-4 rounded-1 align-items-center">
    <?php 
    $catg_run = mysqli_query($conn, "SELECT * FROM category_table WHERE game_id = $id");

    $categories = [];
    while ($catg_row = mysqli_fetch_assoc($catg_run)) {
    $categories[] = $catg_row;
    }

    $total = count($categories);
    $no = 0;

    foreach ($categories as $catg_row) {
    $catg_id = $catg_row['id'];
    echo "<div class='d-inline-block mx-auto'>";

    // Get category values
    $catg_value_run = mysqli_query($conn, "SELECT * FROM category_value_table WHERE category_id = $catg_id");

    while ($value_row = mysqli_fetch_assoc($catg_value_run)) {
            echo '<img 
            src="' . BASE_URL . '/' . $value_row['catg_value_icon'] . '" 
            alt="' . htmlspecialchars($value_row['catg_value_name']) . '" 
            class=""        
            style="width:auto; max-height:2rem; object-fit:cover;">';
    }

    echo "</div>";

    $no++;
    if ($no < $total) {
            // Optional: Add separator between categories
            echo '<span class="mx-1">|</span>';
    }
    }
    ?>
    </section> -->

    <table>
    <tr>
        <td class="tier-label">S</td>
        <td class="character-cell">
        <div class="character-row">
            <div class="character">
            <img src="<?= BASE_URL ?>/asset/content/argenti_icon.png" alt="Jianxin">
            <span>Jianxin<br>(Support, Sub DPS)</span>
            </div>
            <div class="character">
            <img src="resource/jianxin.png" alt="Jianxin">
            <span>Jianxin<br>(Support, Sub DPS)</span>
            </div>
            <div class="character">
            <img src="resource/jianxin.png" alt="Jianxin">
            <span>Jianxin<br>(Support, Sub DPS)</span>
            </div>
            <!-- Add more here -->
        </div>
        </td>
    </tr>
    <tr>
        <td class="tier-label">A</td>
        <td class="character-cell">
        <div class="character-row">
            <!-- Leave empty or add chars -->
        </div>
        </td>
    </tr>
    <tr>
        <td class="tier-label">B</td>
        <td class="character-cell">
        <div class="character-row">
            <div class="character">
            <img src="resource/jianxin.png" alt="Jianxin">
            <span>Jianxin<br>(Support, Sub DPS)</span>
            </div>
        </div>
        </td>
    </tr>
    <tr>
        <td class="tier-label">C</td>
        <td class="character-cell">
        <div class="character-row">
            <div class="character">
            <img src="resource/jianxin.png" alt="Jianxin">
            <span>Jianxin<br>(Support, Sub DPS)</span>
            </div>
            <div class="character">
            <img src="resource/jianxin.png" alt="Jianxin">
            <span>Jianxin<br>(Support, Sub DPS)</span>
            </div>
        </div>
        </td>
    </tr>
    </table>
</main>
</div>

<?php 
include_once(__DIR__ . '/../../include/footer.php');

// Tutup koneksi
mysqli_close($conn);
?>