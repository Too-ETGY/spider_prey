<?php
include_once(__DIR__ . '/../../include/config.php');    
include_once(__DIR__ . '/../../include/navbar_home.php');

if(!isset($_GET['id'])) {
        header("Location: index.php?page=game");
        exit();
}

$id = (int) $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM blog_with_game WHERE id = $id");

if(mysqli_num_rows($result) == 0) {
        header("Location: index.php?page=game");
        exit();
}

$row = mysqli_fetch_assoc($result);
$title = $row['blog_title'];
$game_name = $row['game_name'];
$game_id = $row['game_id'];
$date = $row['blog_date'];
$update = $row['updated_at'];
$imgPath = $row['blog_img'];
$blog_desc = $row['blog_desc'];

?>
  
<div class="bg-color3 container-fluid px-0 d-flex align-items-center justify-content-center">
        <div class="container my-5 mx-2 p-0 text-center text-white row align-items-start justify-content-between" style="min-height: 50vh;">
            <main class="bg-color4 p-0 col-12 col-md-8">
                <img src="<?=BASE_URL ?>/uploads/blog/<?= $imgPath?>" alt="" class="w-100 h-auto" style="max-width=100%;">
                <div class="align-items-start text-start p-4">
                <div class="d-flex align-items-center justify-content-between mt-3 mx-1">
                        <div class="d-flex align-items-center">
                                        <h1 class="display-5 font2"><?= $title?></h1>

                                        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                                        <a href='index.php?page=blog/edit&id=<?=$id?>' class='btn btn-warning text-white my-auto ms-2'>Edit</a>
                                        <?php endif; ?>
                                </div>
                                <a href="<?= BASE_URL?>/index.php?page=blog" class="btn btn-light bg-color4 text-white mt-0">Back</a>
                        </div>
                        <p class="fs-6 font1"><a class="text-decoration-none text-white fw-bold" 
                                href="index.php?page=game/read&id=<?= $game_id ?>"><?= $game_name ?></a> | 
                        <?php 
                        if (!empty($update)) {
                                echo "Posted on " . date('F j, Y', strtotime($date)) .
                                        " (updated " . time_elapsed_string($update) . ")";
                                } else {
                                echo "Posted on " . date('F j, Y', strtotime($date));
                                }
                        ?>
                        </p>
                        <p class="border"><?= $blog_desc ?></p>
                </div>
            </main>

            <?php
            include_once(__DIR__ . '/../../include/sidebar.php');
            ?>
        </div>
</div>


<?php
include_once(__DIR__ . '/../../include/footer.php');

// Tutup koneksi
mysqli_close($conn);
?>