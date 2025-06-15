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
        <ul class="navbar-nav me-md-4 me-0 d-none d-xl-flex align-items-center">
            <li class="nav-item mx-4">
                <a class="nav-link p-0 text-white fs-6 fs-special font1" href="index.php?page=game/read&id=<?=$id?>">Character</a>
            </li>
            <li class="nav-item mx-4">
                <a class="nav-link p-0 text-white fs-6 fs-special font1" href="index.php?page=tier&id=<?=$id?>">Tier List</a>
            </li>
            <li class="nav-item mx-4">
                <a class="nav-link p-0 text-white fs-6 fs-special font1" href="index.php?page=blog&id=<?=$id?>">Guide</a>
            </li>
            <li class="nav-item mx-4">
                <?php if($unique_things): ?>
                <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                    <ul class="navbar-nav">
                    <li class="nav-item dropdown fs-6 font1">
                        <button class="btn text-white dropdown-toggle bg-color1" data-bs-toggle="dropdown" aria-expanded="false">
                            Database
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark bg-color1">
                            <?php foreach ($unique_things as $item): ?>
                            <li><a class="dropdown-item" href="index.php?page=unavailable">
                                <?= htmlspecialchars(trim($item)) ?>
                            </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    </ul>
                </div>
                <?php else: ?>
                    <a class="nav-link p-0 text-white fs-6 font1" href="index.php?page=unavailable">Database</a>
                <?php endif?>
            </li>

            <?php 
            if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):
            ?>
                <li class="nav-item mx-4">
                    <a class="nav-link p-0 text-danger fs-6 font1" href="index.php?page=logout">Logout</a>
                </li>
            <?php
            endif; 
            ?>
        </ul>
        </div>
    </div>
</nav>