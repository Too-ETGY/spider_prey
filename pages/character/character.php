<?php
// echo "characters ". $_GET['id'];
include_once(__DIR__ . '/../../include/config.php');

session_start();
if(!isset($_GET['id'])) {
    header("Location: index.php?page=game");
    exit();
}

$char_id = (int) $_GET['id'];
$char_query = mysqli_query($conn, "SELECT * FROM tier_with_char WHERE char_id = $char_id");
if(mysqli_num_rows($char_query) == 0) {
    header("Location: index.php?page=game");
    exit();
}
$char_row = mysqli_fetch_assoc($char_query);
$game_id = $char_row['game_id'];

$game_query = mysqli_query($conn, "SELECT * FROM game_table WHERE id = $game_id"); 
$game_row = mysqli_fetch_assoc($game_query);
$name = $game_row['game_name'];
$dupes =!empty($game_row['dupes_name']) ? htmlspecialchars($game_row['dupes_name']):null; 
$skill = !empty($game_row['skill_name']) ? htmlspecialchars($game_row['skill_name']):null;
$amplifier = trim($game_row['stat_amplifier']);
$unique_things = $amplifier ? explode(',', $amplifier) : [];

include_once(__DIR__ . '/../../include/navbar_game_read.php');

$char_id = $char_row['char_id'];
$char_name = htmlspecialchars($char_row['char_name']);
$char_icon = $char_row['char_icon'];
$char_base_stat = !empty($char_row['char_base_stat']) ? explode(',',$char_row['char_base_stat']) : [];
$char_base_value = !empty($char_row['char_base_stat']) ? explode(',',$char_row['char_base_stat_value']) : [];
$char_bonus_stat = !empty($char_row['char_bonus_stat']) ? explode(',', $char_row['char_bonus_stat']) : [];
$char_bonus_value = !empty($char_row['char_bonus_stat_value']) ? explode(',', $char_row['char_bonus_stat_value']) : [];
$char_speciality = htmlspecialchars($char_row['char_speciality']);
$tier_name = $char_row['tier_name'] != null ? htmlspecialchars($char_row['tier_name']) : '??';
$color_bg = $char_row['color_bg'] != null ? htmlspecialchars($char_row['color_bg']) : 'black';
?>

<style>
    .item-pills{
        min-width: 15rem;
    }
    @media (max-width: 768px) {
        .item-pills{
            width: 100%;
        }   
    }
</style>

<div class="container-fluid px-0 d-flex align-items-center justify-content-center">
<main class="bg-color1 container my-5 mx-3 text-center text-white d-flex flex-column">
    <section class="d-flex flex-column justify-content-center mt-3 mx-1">
        <div class="d-flex justify-content-end mb-2">
            <a href="<?= BASE_URL?>/index.php?page=game/read&id=<?=$game_id?>" class="btn btn-light bg-color1 text-white mt-0">Back</a>
        </div>
        <div class="d-flex flex-column flex-md-row align-items-md-start align-items-center justify-content-md-start justify-content-centers">
            <div class="mx-auto mx-md-0">
                <img src="<?=BASE_URL?>/uploads/char/<?=$char_icon?>" alt="<?= htmlspecialchars($char_name)?> Icon" 
                class="w-auto border rounded mx-4 mb-4" style="height: 15rem;">
            </div>
            <div class="mx-md-4 mb-4 d-flex flex-column justify-content-center justify-content-md-start">
                <div class="mx-md-0 mx-auto d-flex align-items-center justify-content-start w-100 mb-4">
                    <h1 class="font2 display-6 my-0"><?= htmlspecialchars($char_name)?></h1>
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):?>
                        <a href='index.php?page=character/edit&id=<?=$char_id?>' class='btn btn-warning text-white my-auto ms-2'>Edit</a>
                    <?php endif;?>
                </div>

                <?php
                $categoryQuery = mysqli_query($conn, "SELECT * FROM character_with_categories WHERE char_id = $char_id"); 
                if(mysqli_num_rows($categoryQuery)>0):?>
                <div class="d-flex flex-wrap align-items-center mx-md-0 mx-auto gap-4 w-100 mb-4">
                    <?php while($categories_result = mysqli_fetch_assoc($categoryQuery)):?>
                        <div class="bg-color4 d-flex align-items-center justify-content-center font1 fs-6 rounded px-1">
                            <img src="<?=BASE_URL?>/uploads/category/<?=$categories_result['catg_value_icon']?>" style="heigh:24px;width:24px;" alt="">
                            <?=$categories_result['catg_value_name']?>
                        </div>
                    <?php endwhile; ?>
                </div> 
                <?php endif;?>

                <div class="w-100 d-flex flex-row flex-wrap align-items-start gap-4">
                    <div class="align-items-start justify-content-start">
                        <p class="font1 fs-l mb-0 text-start">Max Level Stats</p>
                        <div class="d-flex flex-column align-items-stretch">
                            <?php for ($i = 0; $i < count($char_base_stat); $i++): 
                                $base_stat = $char_base_stat[$i];
                                $base_value = $char_base_value[$i];?>
                                <div class="bg-color2 mb-1 d-flex align-items-center justify-content-between font1 fs-6 rounded item-pills px-1">
                                    <span><?=$base_stat?></span> <span><?=$base_value?></span>
                                </div>
                            <?php endfor;?>
                        </div>
                    </div>
                    
                    <?php if($char_bonus_stat != []):?>
                    <div class="align-items-start justify-content-start">
                        <p class="font1 fs-l mb-0 text-start">Bonus Stats</p>
                        <div class="d-flex flex-column align-items-stretch">
                            <?php for ($i = 0; $i < count($char_bonus_stat); $i++): 
                                $bonus_stat = $char_bonus_stat[$i];
                                $bonus_value = $char_bonus_value[$i];?>
                                <div class="bg-color2 mb-1 d-flex align-items-center justify-content-between font1 fs-6 rounded item-pills px-1">
                                    <span><?=$bonus_stat?></span> <span><?=$bonus_value?></span>
                                </div>
                            <?php endfor;?>
                        </div>
                    </div>
                    <?php endif;?>
                    
                    <div class="align-items-start justify-content-start">
                        <p class="font1 fs-l mb-0 text-start">Upgrade Material</p>
                        <div class="d-flex flex-column align-items-stretch">
                            <div class="bg-color2 mb-1 d-flex align-items-center justify-content-between font1 fs-6 rounded item-pills px-1">
                                (TBA)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center me-md-4 mb-4">
                <div class="rounded d-flex flex-column justify-content-center align-items-center" style="background-color:<?=$color_bg?>;height:8.5rem;width:8rem;">
                    <p class="font3 p-2 display-5 mb-0"><?=$tier_name?></p>
                    <span class="font1 fs-6 text-center mx-3"><?=$char_speciality?></span>
                </div>
            </div>
        </div>

        <?php
        $getRelated = mysqli_query($conn, "CALL get_blogs($game_id, null, '$char_name')");
        if(mysqli_num_rows($getRelated)>0):?>
            <div class="d-flex flex-column align-items-start justify-content-start mb-3 mx-4">
                <h1 class="font2 display-6 my-0 text-starts">Related Post</h1>
                <div class="d-flex flex-wrap justify-content-center justify-content-md-start">
                <?php
                while($related = mysqli_fetch_assoc($getRelated)):?>
                    <a class="card bg-transparent text-white text-decoration-none me-3 mb-3" 
                    href="index.php?page=blog/read&id=<?=$related['id']?>" style="max-width:15rem;">
                        <img src="<?=BASE_URL ?>/uploads/blog/<?= $related['blog_img']?>" 
                        class="card-img-top w-auto" alt="..." style="height:10rem;">
                        <div class="card-body bg-color4 text-start w-100">
                            <h5 class="card-title"><?= htmlspecialchars($related['blog_title']) ?></h5>
                            <h6 class="card-title"><?= date('j-m-Y', strtotime($related['blog_date']))?></h6>
                        </div>
                    </a>
                <?php endwhile; ?>
                </div>
            </div>
        <?php endif;
        while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
            $extraResult = mysqli_use_result($conn);
            if ($extraResult instanceof mysqli_result) {
                $extraResult->free();
            }
        }?>

        <?php if($skill!=null):?>
            <div class="d-flex flex-column align-items-start justify-content-start mb-4 mx-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h1 class="font2 display-6 my-0 text-starts"><?=$skill?></h1>
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):?>
                        <div class=" d-flex justify-content-end">
                            <a href="index.php?page=skill/create&id=<?=$char_id?>" class="btn btn-success">Add Skill</a>
                        </div>
                    <?php endif;?>
                </div>
                <?php 
                $getSkill = mysqli_query($conn, "SELECT * FROM skill_table WHERE char_id = $char_id ORDER BY skill_order");
                if(mysqli_num_rows($getSkill)>0):?>
                <div class="d-flex dlex-column">
                    <div class="row">
                        <div class="col-2">p</div>
                        <div class="col-10">p</div>
                    </div>
                </div>
                <?php else:?>
                <div class="d-flex dlex-column">
                    <p>No Data Provided</p>
                </div>
                <?php endif;?>
            </div>
        <?php endif;?>

        <?php if($dupes!=null):?>
            <div class="d-flex flex-column align-items-start justify-content-start mb-4 mx-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h1 class="font2 display-6 my-0 text-starts"><?=$dupes?></h1>
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):?>
                        <div class=" d-flex justify-content-end">
                            <a href="index.php?page=dupes/create&id=<?=$char_id?>" class="btn btn-success">Add Supes</a>
                        </div>
                    <?php endif;?>
                </div>
                <?php 
                $getDupes = mysqli_query($conn, "SELECT * FROM dupes_table WHERE char_id = $char_id ORDER BY dupes_order");
                if(mysqli_num_rows($getDupes)>0):?>
                <div class="d-flex dlex-column">
                    <div class="row">
                        <div class="col-2">p</div>
                        <div class="col-10">p</div>
                    </div>
                </div>
                <?php else:?>
                <div class="d-flex dlex-column">
                    <p>No Data Provided</p>
                </div>
                <?php endif;?>
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