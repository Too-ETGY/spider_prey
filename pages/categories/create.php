<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/header.php');

session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php?page=login");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM game_table ORDER BY id");
$rows = [];

while ($item = mysqli_fetch_assoc($result)) {
    $rows[] = $item;
}
?>
<main class=container>

<select class="form-select" name="id" aria-label="Default select example">
    <option selected>Pilih game untuk dihapus</option>
    <?php
    if (count($rows) > 0) {
        foreach($rows as $row) {
        echo'
        <option value="'.$row["id"].'">'.htmlspecialchars($row["game_name"]).'</option>
        ';
        }
    }else {
        echo "<option disabled>Tidak ada data</option>";
    }
    ?>
</select>

<form action="">
    <input type="text" value="category_name">
</form>

<a href="<?= BASE_URL?>/index.php?page=game" class="btn btn-secondary">kembali</a>

</main>
