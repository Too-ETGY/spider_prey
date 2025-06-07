<?php
include_once(__DIR__ . '/../../include/config.php');

include_once(__DIR__ . '/../../include/navbar_home.php');
?>

<main class="bg-color3 container-fluid px-0 d-flex align-items-center justify-content-center">
        <div class="bg-color4 container my-5 mx-3 px-0 text-center text-white d-flex align-items-start justify-content-between row">
            <section class="bg-color2 col-8 p-3 m-0 g-3 row justify-content-around align-items-start">
                <a class="card col-5 px-0 text-start text-white text-decoration-none" href="#">
                    <img src="<?=BASE_URL ?>/asset/content/bg-image.jpg" class="card-img-top" alt="...">
                    <div class="card-body bg-color4">
                        <h5 class="card-title">title</h5>
                        <h6 class="card-title">Game_name | release_date</h6>
                        <p class="card-text lh-1 mb-3 cutoff-text">Some quick example text to build on the card title and make up the bulk of the card’s content. Some quick example text to build on the card title and make up the bulk of the card’s content. Some quick example text to build on the card title and make up the bulk of the card’s content. Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                    </div>
                </a>
                <a class="card col-5 px-0 text-start text-white text-decoration-none" href="#">
                    <img src="<?=BASE_URL ?>/asset/content/bg-image.jpg" class="card-img-top" alt="...">
                    <div class="card-body bg-color4">
                        <h5 class="card-title">title</h5>
                        <h6 class="card-title">Game_name | release_date</h6>
                        <p class="card-text lh-1 mb-3 cutoff-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        <!-- <a href="#" class="btn btn-primary">Go somewhere</a> -->
                    </div>
                </a>
                <a class="card col-5 px-0 text-start text-white text-decoration-none" href="#">
                    <img src="<?=BASE_URL ?>/asset/content/bg-image.jpg" class="card-img-top" alt="...">
                    <div class="card-body bg-color4">
                        <h5 class="card-title">title</h5>
                        <h6 class="card-title">Game_name | release_date</h6>
                        <p class="card-text lh-1 mb-3 cutoff-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        <!-- <a href="#" class="btn btn-primary">Go somewhere</a> -->
                    </div>
                </a>
            </section>

            <aside class="bg-color2 col-3">
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </aside>
        </div>
    </main>


<?php
include_once(__DIR__ . '/../../include/footer.php');

// Tutup koneksi
mysqli_close($conn);
?>