<?php
include_once ("config.php");

$game_data = mysqli_query($conn, "SELECT id, game_name FROM game_table");
$items = [];

while ($item = mysqli_fetch_assoc($game_data)) {
    $items[] = $item;
}
mysqli_next_result($conn);
?>

<aside class="col-11 col-sm-4 col-md-3 p-0 mx-auto">

    <div class="bg-color1 p-3 mb-3 mx-0 d-flex align-items-center justify-content-center">
        <div class="dropdown">
            <button class="btn text-white dropdown-toggle rounded-0 border-end bg-color4" 
            type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
            <ul class="dropdown-menu bg-color4">
                <?php 
                foreach ($items as $item) :
                    $g_Id= $item['id'];
                    $blog_game_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT count_blogs( $g_Id, NULL) AS total;"));
                ?>
                    <li style="">
                        <a class="dropdown-item text-white w-100" href="index.php?page=blog&id=<?=$g_Id ?>">
                        <?= htmlspecialchars($item['game_name'])?> (<?= $blog_game_count['total']?>)</a>
                    </li>
                <?php endforeach?>
            </ul>
        </div>

        <form action="" role="search" method="get">
            <input type="hidden" name="page" value="blog">
            <?php if($game_id!=null):?>
            <input type="hidden" name="id" value="<?=$game_id?>">
            <?php endif?>
            <div class="d-flex align-items-center bg-color4">
                <input class="form-control border-0 bg-color4" 
                    placeholder="Search..." 
                    type="search" 
                    name="s" />

                <button class="btn border-start rounded-0" type="submit">
                    <svg class="text-white"
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="bg-color1 mx-0 mb-3 text-start row row-cols-1">
        <h3 class="mt-2 mb-4 font2">Recent POST</h3>
        <?php 
        $recent_news = mysqli_query($conn, "SELECT * FROM blog_with_game ORDER BY blog_date DESC LIMIT 5");
        while($item = mysqli_fetch_assoc($recent_news)): ?>
            <a class="card px-4 bg-transparent text-white text-decoration-none mb-3" 
            href="index.php?page=blog/read&id=<?=$item['id']?>">
                <img src="<?=BASE_URL ?>/uploads/blog/<?= $item['blog_img']?>" 
                class="card-img-top" alt="...">
                <div class="card-body bg-color4">
                    <h5 class="card-title"><?= htmlspecialchars($item['blog_title']) ?></h5>
                    <h6 class="card-title"><?= date('j-m-Y', strtotime($item['blog_date']))?></h6>
                </div>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="bg-color1 mx-0 text-start row row-cols-1">
        <h3 class="mt-2 mb-4 font2">Games</h3>
        <ul>
            <?php 
            foreach ($items as $item) : ?>
                <li>
                    <a href="index.php?page=game/read&id=<?= $item['id'] ?>" class="text-white">
                        <?= htmlspecialchars($item['game_name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li><a href="index.php?page=game" class="text-white">More</a></li>                    
        </ul>
    </div>
</aside>


<script>
    // if (window.self !== window.top) {
    //     window.top.location = window.self.location.href;
    // };
</script>