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
include_once(__DIR__ . '/../../include/navbar_game_read.php');

// Ambil data untuk ditampilkan
$row = mysqli_fetch_assoc($result);
$name = $row['game_name'];
$dupes = $row['dupes_name'];
$skill = $row['skill_name'];
$amplifier = trim($row['stat_amplifier']);
$unique_things = $amplifier ? explode(',', $amplifier) : [];
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=keyboard_double_arrow_left" />

<main class="bg-color3 container-fluid px-0 d-flex align-items-center justify-content-center">
        <section class="bg-color1 container my-5 mx-3 text-center text-white d-flex flex-column" style="min-height: 50vh;">
                <div class="d-flex align-items-center justify-content-between mt-3 mx-1">
                        <div class="d-flex align-items-center">
                                <h1 class="font2 display-7 my-0"><?= htmlspecialchars($name) ?></h1>
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
                <div class="font1 text-start mt-4">
                        <?php if ($dupes): ?>
                            <p><?= htmlspecialchars($dupes) ?></p>
                        <?php endif; ?>

                        <?php if ($skill): ?>
                                <p><?= htmlspecialchars($skill) ?></p>
                        <?php endif; ?>


                        <?php if (!empty($unique_things)): ?>
                                <p class="fs-6">Special mechanic:</p>
                                <ul class="fs-6" style="list-style-type: disc;">
                                        <?php foreach ($unique_things as $item): ?>
                                        <li><?= htmlspecialchars(trim($item)) ?></li>
                                        <?php endforeach; ?>
                                </ul>
                        <?php endif; ?>
                        </ul>
                </div>
        </section>
</main>

<?php 
include_once(__DIR__ . '/../../include/footer.php');

// Tutup koneksi
mysqli_close($conn);
?>