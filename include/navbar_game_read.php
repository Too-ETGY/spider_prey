<?php
include_once('header.php');
// session_start()
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg position-sticky py-0 top-0 z-3 bg-color1 bg-color1"
style="transition: background-color 0.3s ease">
    <div class="container-fluid px-0 d-flex align-items-center pe-5" style="height: min(10vh, 4rem);">
        <a class="navbar-brand py-0 h-100 d-flex justify-content-center" href="index.php?page=home">
            <img src="<?=BASE_URL ?>/asset/content/Logo.png" alt="" class="h-100 w-auto" id="navbarLogo">
        </a>
        <ul class="navbar-nav me-md-4 me-0 flex-row align-items-center">
            <li class="nav-item d-none d-md-block mx-4">
                <a class="nav-link p-0 text-white fs-6 fs-special font1" href="index.php?page=game/read&id=<?=$id?>">Character</a>
            </li>
            <li class="nav-item d-none d-md-block mx-4">
                <a class="nav-link p-0 text-white fs-6 fs-special font1" href="index.php?page=tier&id=<?=$id?>">Tier List</a>
            </li>
            <li class="nav-item d-none d-md-block mx-4">
                <a class="nav-link p-0 text-white fs-6 fs-special font1" href="index.php?page=blog&id=<?=$id?>">Guide</a>
            </li>
            <li class="nav-item d-none d-md-block mx-4">
                <a class="nav-link p-0 text-white fs-6 fs-special font1" href="index.php?page=unavailable">Database</a>
            </li>

            <?php 
            if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):
            ?>
                <li class="nav-item d-none d-md-block mx-4">
                    <a class="nav-link p-0 text-danger fs-6 font1" href="index.php?page=logout">Logout</a>
                </li>
            <?php
            endif; 
            ?>
        </ul>
        </div>
    </div>
</nav>