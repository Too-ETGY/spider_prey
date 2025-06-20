<?php
include_once 'header.php';
// session_start()
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg position-sticky py-0 top-0 z-3 bg-color1 bg-color1"
style="transition: background-color 0.3s ease" id="my-navbar">
    <div class="container-fluid px-0 d-flex align-items-center" style="height: min(10vh, 4rem);">
        <a class="navbar-brand py-0 h-100 d-flex justify-content-center" href="index.php?page=home">
            <img src="<?=BASE_URL ?>/asset/content/Logo.png" alt="" class="h-100 w-auto" id="navbarLogo">
        </a>

        <button class="navbar-toggler bg-color4 text-white me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end bg-color1" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <div class="offcanvas-title" id="offcanvasNavbarLabel" style="height: min(10vh, 4rem);">
                    <img src="<?=BASE_URL ?>/asset/content/Logo.png" alt="" class="h-100 w-auto" id="navbarLogo">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end align-items-lg-center flex-grow-1 pe-0 pe-lg-5">
                    <li class="nav-item mx-4">
                        <a class="nav-link p-0 text-white fs-6 font1" href="index.php?page=game/read&id=<?=$game_id?>">Character</a>
                    </li>
                    <li class="nav-item mx-4">
                        <a class="nav-link p-0 text-white fs-6 font1" href="index.php?page=tier&id=<?=$game_id?>">Tier List</a>
                    </li>
                    <li class="nav-item mx-4">
                        <a class="nav-link p-0 text-white fs-6 font1" href="index.php?page=blog&id=<?=$game_id?>">Guide</a>
                    </li>
                    <?php if($unique_things!=[]): ?>
                    <li class="nav-item dropdown mx-4 text-white border-lights">
                        <button class="nav-link text-white dropdown-toggle fs-6 font1"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Database
                        </button>
                        <ul class="dropdown-menu bg-color1">
                            <?php foreach ($unique_things as $item): ?>
                            <li>
                                <a class="dropdown-item text-white fs-6" href="index.php?page=unavailable">
                                    <?= htmlspecialchars(trim($item)) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item mx-4" >
                        <a class="nav-link p-0 text-white fs-6 font1" href="index.php?page=unavailable">Database</a>
                    </li>
                    <?php endif?>

                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                    <li class="nav-item mx-4 mt-3 mt-md-0">
                        <a class="nav-link p-0 text-danger fs-6 font1" href="<?=BASE_URL?>/admin/logout.php">Logout</a>
                    </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>
</nav>