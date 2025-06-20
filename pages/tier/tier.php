<?php
// echo "characters ". $_GET['id'];
include_once(__DIR__ . '/../../include/config.php');

session_start();
if(!isset($_GET['id'])) {
    header("Location: index.php?page=game");
    exit();
}

$game_id = (int) $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM game_table WHERE id = $game_id");
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
    .custom-height{
        height:5rem;
    }

    @media max-width:768px {
        .custom-height{
            height:5rem;
        }
    }
</style>

<div class="container-fluid px-0 d-flex align-items-center justify-content-center">
<main class="bg-color1 container my-5 mx-3 text-center text-white d-flex flex-column" style="min-height:50vh;">
    <section class="d-flex flex-column justify-content-start mt-3 mx-1">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center">
                <h1 class="font2 display-6 my-0"><?= htmlspecialchars($name) ?></h1>
                <?php            
                if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):
                ?>
                    <a href='index.php?page=game/edit&id=<?=$game_id?>' class='btn btn-warning text-white my-auto ms-2'>Edit</a>
                <?php
                endif;
                ?>
            </div>
            <a href="<?= BASE_URL?>/index.php?page=game" class="btn btn-light bg-color1 text-white mt-0">Back</a>
        </div>
        <div class="d-flex align-items-center justify-content-between w-100 my-3">
            <h4 class="d-flex justify-content-start font2 fw-normal fs-3 ">Tier List</h4>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):?>
                <div class=" d-flex justify-content-end">
                    <a href="index.php?page=tier/create&id=<?=$game_id?>" class="btn btn-success">Make Tier</a>
                </div>
            <?php endif;?>
        </div>
    </section>

    <section class="mb-4 px-3 d-flex flex-column justify-content-center">
        <?php
        $tierQuery = $conn->prepare("SELECT * FROM tier_table WHERE game_id = ? ORDER BY tier_order ASC");
        $tierQuery->bind_param("i", $game_id);
        $tierQuery->execute();
        $tierResult = $tierQuery->get_result();

        $tiers = [];
        while ($row = mysqli_fetch_assoc($tierResult)) {
                $tiers[] = $row;
        }

        foreach ($tiers as $tier):
        ?>
        <div class="row  mb-3 align-items-stretch" style="min-height:10rem;">
            <div class="col-4 col-sm-3 col-md-2 font3 p-2 display-6 rounded-start d-flex align-items-center justify-content-center"
                style="background-color:<?php echo $tier['color_bg']; ?>;">
                <?= $tier['tier_name'] ?>
            </div>

            <div class="col-8 col-sm-9 col-md-10 bg-color4 rounded-end d-flex flex-wrap justify-content-start align-items-start">
                <?php 
                $tier_id = $tier['id'];
                $charQuery = mysqli_query($conn, "SELECT * FROM tier_with_char WHERE game_id = $game_id AND tier_id = $tier_id");
                while($char_row = mysqli_fetch_assoc($charQuery)):
                    // for($i=0;$i<10;$i++):
                ?>
                <a class="m-3 text-decoration-none text-white" href="?page=character&id=<?=$char_row['char_id']?>">
                    <img class="border border-2 rounded-3 w-auto custom-height"
                        src="<?= BASE_URL ?>/uploads/char/<?=$char_row['char_icon']?>" alt="<?=htmlspecialchars($char_row['char_name'])?>">
                    <p class="fs-6 font1 mb-0 text-center"><?=htmlspecialchars($char_row['char_name'])?></p>
                    <!-- <p class="fs-s mb-0" style="max-width:100%;">(<?=htmlspecialchars($char_row['char_speciality'])?>)</p> -->
                    <p class="mb-0 text-wrap text-break" style="font-size:0.7rem;max-width:100%; white-space:normal;">
                        (<?= preg_replace('/,\s*/', ', <br>', htmlspecialchars_decode(htmlspecialchars($char_row['char_speciality']))) ?>)
                    </p>
                </a>
                <?php endwhile;?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php 
        $charNull = mysqli_query($conn, "SELECT * FROM tier_with_char WHERE game_id = $game_id AND tier_id IS NULL");
        if(mysqli_num_rows($charNull) > 0):
            ?>
            <div class="row  mb-3 align-items-stretch" style="min-height:10rem;">
                <div class="col-4 col-sm-3 col-md-2 font3 p-2 display-6 rounded-start d-flex align-items-center justify-content-center"
                    style="background-color:black;">
                    ??
                </div>
                <div class="col-8 col-sm-9 col-md-10 bg-color4 rounded-end d-flex flex-wrap justify-content-start align-items-start">
                    <?php while($char_row = mysqli_fetch_assoc($charNull)):?>
                    <a class="m-3 text-decoration-none text-white" href="?page=character&id=<?=$char_row['char_id']?>">
                        <img class="border border-2 rounded-3 w-auto custom-height"
                            src="<?= BASE_URL ?>/uploads/char/<?=$char_row['char_icon']?>" alt="<?=htmlspecialchars($char_row['char_name'])?>">
                        <p class="fs-6 font1 mb-0 text-center"><?=htmlspecialchars($char_row['char_name'])?></p>
                        <!-- <p class="fs-s mb-0" style="max-width:100%;">(<?=htmlspecialchars($char_row['char_speciality'])?>)</p> -->
                        <p class="mb-0 text-wrap text-break" style="font-size:0.7rem;max-width:100%; white-space:normal;">
                            (<?= preg_replace('/,\s*/', ', <br>', htmlspecialchars_decode(htmlspecialchars($char_row['char_speciality']))) ?>)
                        </p>
                    </a>
                    <?php endwhile;?>
                </div>
            </div>
        <?php endif;?>
    </section>

</main>
</div>

<?php 
include_once(__DIR__ . '/../../include/footer.php');

// Tutup koneksi
mysqli_close($conn);
?>
</body></html>