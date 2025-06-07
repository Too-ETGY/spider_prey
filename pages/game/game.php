<?php
include_once(__DIR__ . '/../../include/config.php');
include_once(__DIR__ . '/../../include/navbar_home.php');
// session_start();

// === DELETE logic ===
if (isset($_POST['delete'])) {
    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {
        $query = "SELECT game_icon FROM game_table WHERE id = $id";
        $query_run = @mysqli_query($conn, $query);

        if ($query_run && mysqli_num_rows($query_run) > 0) {
            $row = mysqli_fetch_assoc($query_run);
            $iconFile = $row['game_icon'];

            $delete = mysqli_query($conn, "DELETE FROM game_table WHERE id = $id");

            if ($delete) {
                $imagePath = __DIR__ . '/../../uploads/' . $iconFile;
                if ($iconFile && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                echo "<script>alert('Game Deleted!');</script>";
            } else {
                echo "<p style='color:red;'>Query error: " . mysqli_error($conn) . "</p>";
            }
        }
    header("Location: index.php?page=game");
    exit;
    }
}

$result = mysqli_query($conn, "SELECT * FROM game_table ORDER BY id");
$rows = [];

while ($item = mysqli_fetch_assoc($result)) {
    $rows[] = $item;
}
?>

<main class="bg-color3 container-fluid px-0 d-flex align-items-center justify-content-center">
    <section class="bg-color2 container my-5 mx-3 text-center text-white d-flex flex-column" style="min-height: 50vh;">
            <h1 class="font2 display-5 mt-5 mx-auto mb-4">Supported Games</h1>

            <?php            
            if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):
            ?>
            <div class="mb-5 d-flex justify-content-end">
                <a href="index.php?page=game/create" class="btn btn-success mx-2">Add Game</a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Delete
                </button>
            </div>

            <!-- Modal -->
            <form action="index.php?page=game" method="POST">
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
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
                            </div>
                            <div  class="modal-footer">
                                <input type="submit" name="delete" value="Delete" class="btn btn-danger">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php 
            endif; 
            ?>            

            <div class="row container-md justify-content-center text-center mb-5 mx-auto">
                <!-- Php -->
                <?php
                if (count($rows) > 0) {
                    foreach($rows as $row) {
                        echo "<div class='col-lg-4 col-sm-6 col-12 mt-0 text-center mb-4'>";
                        echo "<a href='index.php?page=game/read&id=" .$row["id"]. "' class='text-decoration-none text-white justify-content-center mx-auto'>";
                        echo "<img src='".BASE_URL."/uploads/" .$row["game_icon"]. "' alt='(".$row["game_name"]." icon)' class='custom-img-size rounded-2 d-block p-0 mx-auto'>";
                        echo "<p class='fs-5 font1 mb-0'>".$row["game_name"]."</p></a></div>";
                    }
                } else {
                    echo "<div class='bg-red z-3'>";
                    echo "Belum ada game</div>";
                }
                ?>
            </div>
    </section>
</main>

<?php
include_once(__DIR__ . '/../../include/footer.php');

// Tutup koneksi
mysqli_close($conn);
?>