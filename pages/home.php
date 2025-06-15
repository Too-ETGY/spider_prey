<?php
include_once(__DIR__ . '/../include/config.php');

include_once(__DIR__ . '/../include/navbar_home.php');
?>

<!-- Transparent Navbar -->
<script>
    const navbar = document.querySelector('.navbar');
    const logo = document.getElementById('navbarLogo');

    // Function to update navbar background based on scroll position
    function updateNavbarTransparency() {
        if (window.scrollY <= 20) {
            navbar.classList.add('navbar-transparent');
            logo.src = 'asset/content/Logo_no-bg.png';
        } else {
            navbar.classList.remove('navbar-transparent');
            logo.src = 'asset/content/Logo.png';
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        navbar.classList.remove('position-sticky');
        navbar.classList.add('position-fixed', 'min-vw-100');
        updateNavbarTransparency(); // Apply background based on initial scroll position
    });

    window.addEventListener('scroll', updateNavbarTransparency);
</script>

<!-- Main -->
<main class="container-fluid px-0 bg-color2">

    <!-- Main -->
    <section class="bg-image container-fluid p-0 d-flex flex-column justify-content-center">
        <div class="container-md text-center text-white z-2">
            <h1 class="font2 display-5 mb-3">Master Your Gacha Journey with Expert Guides & Tips</h1>
            <p class="font1 fs-5 px-4 px-md-4">
                Get started with beginner-friendly guides, character builds, tier lists, and advanced strategies for games like Honkai: Star Rail, Genshin Impact, and more.
            </p>
        </div>
    </section>

    <!-- Games -->  
    <section class="container-fluid pb-4 text-center text-white d-flex flex-column align-items-center justify-content-center">
        <div class="container-md"> 
            <h1 class="font2 display-5 mb-4">Popular Games</h1>
            <div class="row justify-content-center text-center">
                <?php
                // Query untuk mengambil data mahasiswa
                $result = mysqli_query($conn, "SELECT * FROM game_table ORDER BY id LIMIT 3");
                // Cek apakah ada data
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    // Looping untuk menampilkan data
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='col-lg-3 col-sm-6 col-12 mt-0 text-center'>";
                        echo "<a href='index.php?page=character/character&id=" .$row["id"]. "' class='text-decoration-none text-white justify-content-center mx-auto'>";
                        echo "<img src='".BASE_URL."/uploads/game/" .$row["game_icon"]. "' alt='' class='custom-img-size rounded-2 d-block p-0 mx-auto'>";
                        echo "<p class='fs-5 font1 mb-0'>".$row["game_name"]."</p></a></div>";
                    }
                    echo '</div> <a href="index.php?page=game" class="btn text-white mt-3" style="min-width: 15%; background-color: rgba(61,54,92, 0.75);">More</a>';
                } else {
                    echo "Tidak ada data </div>";
                }
                // Tutup koneksi
                mysqli_close($conn);         
                ?>
        </div>
    </section>

    <!-- Features -->
    <section class="container-fluid py-4 text-center d-flex flex-column align-items-center">
        <h1 class="font2 display-5 mb-3 text-white">We help you by providing</h1>   
        <div class="row container-md mt-1 justify-content-center">
            <article class="bg-color2 text-white col-10 col-sm-5 col-lg-3 mx-0 my-2 m-sm-2 m-lg-4 p-4 card rounded-4 custom-card-shadow">
                <div class="card-body d-flex flex-column justify-content-center align-items-center font1">
                    <svg class="svg-inline--fa fa-book-open card-title fs-6 " 
                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="book-open" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="36">
                        <path fill="currentColor" d="M144.3 32.04C106.9 31.29 63.7 41.44 18.6 61.29c-11.42 5.026-18.6 
                        16.67-18.6 29.15l0 357.6c0 11.55 11.99 19.55 22.45 14.65c126.3-59.14 219.8 11 223.8 14.01C249.1 478.9 
                        252.5 480 256 480c12.4 0 16-11.38 16-15.98V80.04c0-5.203-2.531-10.08-6.781-13.08C263.3 65.58 216.7 33.35 144.3 32.04zM557.4 61.29c-45.11-19.79-88.48-29.61-125.7-29.26c-72.44 1.312-118.1 33.55-120.9 
                        34.92C306.5 69.96 304 74.83 304 80.04v383.1C304 468.4 307.5 480 320 480c3.484 0 6.938-1.125 9.781-3.328c3.925-3.018 97.44-73.16 223.8-14c10.46 4.896 22.45-3.105 22.45-14.65l.0001-357.6C575.1 77.97 
                        568.8 66.31 557.4 61.29z">
                        </path>
                    </svg>
                    <h4 class="card-title fs-5 lh-1">Beginner and Advanced Guides</h4>
                    <p class="card-text fs-6 lh-1">
                        Knowledge is power and Spider's Prey teaches you what to do 
                        (or what not to do) in order to gain advantage over other players.
                    </p>
                </div>
            </article>
            <article class="bg-color2 text-white col-10 col-sm-5 col-lg-3 mx-0 my-2 m-sm-2 m-lg-4 p-4 card rounded-4 custom-card-shadow"">
                <div class="card-body d-flex flex-column justify-content-center align-items-center font1">
                    <svg class="svg-inline--fa fa-user-group card-title fs-6" 
                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user-group" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="36">
                        <path fill="currentColor" d="M224 256c70.7 0 128-57.31 128-128S294.7 0 224 0C153.3 0 96 57.31 96 128S153.3 256 224 256zM274.7 304H173.3c-95.73 0-173.3 77.6-173.3 173.3C0 496.5 15.52 512 34.66 512H413.3C432.5 
                        512 448 496.5 448 477.3C448 381.6 370.4 304 274.7 304zM479.1 320h-73.85C451.2 357.7 480 414.1 480 477.3C480 490.1 476.2 501.9 470 512h138C625.7 512 640 497.6 640 479.1C640 391.6 568.4 320 479.1 320zM432 256C493.9 
                        256 544 205.9 544 144S493.9 32 432 32c-25.11 0-48.04 8.555-66.72 22.51C376.8 76.63 384 101.4 384 128c0 35.52-11.93 68.14-31.59 94.71C372.7 243.2 400.8 256 432 256z">
                        </path>
                    </svg>
                    <h4 class="card-title fs-5 lh-1" style="font-size: 1.5rem;">Character Reviews</h4>
                    <p class="card-text fs-6 lh-1">
                        Gacha games revolve around collecting characters and Spider's 
                        Prey helps you use them in an optimal view.
                    </p>
                
                </div>
            </article>
            <article class="bg-color2 text-white col-10 col-sm-5 col-lg-3 mx-0 my-2 m-sm-2 m-lg-4 p-4 card rounded-4 custom-card-shadow">
                <div class="card-body d-flex flex-column justify-content-center align-items-center font1">
                    <svg class="svg-inline--fa fa-ranking-star card-title fs-6"
                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="ranking-star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="36">
                        <path fill="currentColor" d="M406.1 61.65C415.4 63.09 419.4 74.59 412.6 81.41L374.6 118.1L383.6 170.1C384.1 179.5 375.3 186.7 366.7 182.4L320.2 157.9L273.3 182.7C264.7 187 255 179.8 256.4 170.5L265.4 
                        118.4L227.4 81.41C220.6 74.59 224.6 63.09 233.9 61.65L286.2 54.11L309.8 6.332C314.1-2.289 326.3-1.93 330.2 6.332L353.8 54.11L406.1 61.65zM384 256C401.7 256 416 270.3 416 288V480C416 497.7 401.7 512 384 
                        512H256C238.3 512 224 497.7 224 480V288C224 270.3 238.3 256 256 256H384zM160 320C177.7 320 192 334.3 192 352V480C192 497.7 177.7 512 160 512H32C14.33 512 0 497.7 0 480V352C0 334.3 14.33 320 32 320H160zM448 
                        416C448 398.3 462.3 384 480 384H608C625.7 384 640 398.3 640 416V480C640 497.7 625.7 512 608 512H480C462.3 512 448 497.7 448 480V416z">
                        </path>
                    </svg>
                    </svg>
                    <h4 class="card-title fs-5 lh-1" style="font-size: 1.5rem;">Tier Lists and Meta Analysis</h4>
                    <p class="card-text fs-6 lh-1">Whether you are a meta slave or collector, 
                        playing any gacha game without a good tier list makes everything harder.
                    </p>
                </div>
            </article>
            <article class="bg-color2 text-white col-10 col-sm-5 col-lg-3 mx-0 my-2 m-sm-2 m-lg-4 p-4 card rounded-4 custom-card-shadow"">
                <div class="card-body d-flex flex-column justify-content-center align-items-center font1">
                    <svg class="svg-inline--fa fa-newspaper card-title fs-6" 
                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="newspaper" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="36">
                        <path fill="currentColor" d="M480 32H128C110.3 32 96 46.33 96 64v336C96 408.8 88.84 416 80 416S64 408.8 64 400V96H32C14.33 96 0 110.3 0 128v288c0 35.35 28.65 64 64 64h384c35.35 0 64-28.65 64-64V64C512 
                        46.33 497.7 32 480 32zM272 416h-96C167.2 416 160 408.8 160 400C160 391.2 167.2 384 176 384h96c8.836 0 16 7.162 16 16C288 408.8 280.8 416 272 416zM272 320h-96C167.2 320 160 312.8 160 304C160 295.2 167.2 288 
                        176 288h96C280.8 288 288 295.2 288 304C288 312.8 280.8 320 272 320zM432 416h-96c-8.836 0-16-7.164-16-16c0-8.838 7.164-16 16-16h96c8.836 0 16 7.162 16 16C448 408.8 440.8 416 432 416zM432 320h-96C327.2 320 320 
                        312.8 320 304C320 295.2 327.2 288 336 288h96C440.8 288 448 295.2 448 304C448 312.8 440.8 320 432 320zM448 208C448 216.8 440.8 224 432 224h-256C167.2 224 160 216.8 160 208v-96C160 103.2 167.2 96 176 96h256C440.8 
                        96 448 103.2 448 112V208z">
                        </path>
                    </svg>

                    <h4 class="card-title fs-5 lh-1" style="font-size: 1.5rem;">News and updates</h4>
                    <p class="card-text fs-6 lh-1">Stay up to date with any patch notes, 
                        new content or other important updates.
                    </p>
                </div>
            </article>
            <article class="bg-color2 text-white col-10 col-sm-5 col-lg-3 mx-0 my-2 m-sm-2 m-lg-4 p-4 card rounded-4 custom-card-shadow"">
                <div class="card-body d-flex flex-column justify-content-center align-items-center font1">
                    <svg class="svg-inline--fa fa-list-ol card-title fs-6" 
                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="list-ol" 
                        role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="36">
                        <path fill="currentColor" d="M55.1 56.04C55.1 42.78 66.74 32.04 79.1 32.04H111.1C125.3 32.04 135.1 42.78 135.1 56.04V176H151.1C165.3 176 175.1 186.8 175.1 200C175.1 213.3 165.3 224 151.1 224H71.1C58.74 
                        224 47.1 213.3 47.1 200C47.1 186.8 58.74 176 71.1 176H87.1V80.04H79.1C66.74 80.04 55.1 69.29 55.1 56.04V56.04zM118.7 341.2C112.1 333.8 100.4 334.3 94.65 342.4L83.53 357.9C75.83 368.7 60.84 371.2 50.05 
                        363.5C39.26 355.8 36.77 340.8 44.47 330.1L55.59 314.5C79.33 281.2 127.9 278.8 154.8 309.6C176.1 333.1 175.6 370.5 153.7 394.3L118.8 432H152C165.3 432 176 442.7 176 456C176 469.3 165.3 480 152 480H64C54.47 
                        480 45.84 474.4 42.02 465.6C38.19 456.9 39.9 446.7 46.36 439.7L118.4 361.7C123.7 355.9 123.8 347.1 118.7 341.2L118.7 341.2zM512 64C529.7 64 544 78.33 544 96C544 113.7 529.7 128 512 128H256C238.3 128 224 113.7 
                        224 96C224 78.33 238.3 64 256 64H512zM512 224C529.7 224 544 238.3 544 256C544 273.7 529.7 288 512 288H256C238.3 288 224 273.7 224 256C224 238.3 238.3 224 256 224H512zM512 384C529.7 384 544 398.3 544 416C544 
                        433.7 529.7 448 512 448H256C238.3 448 224 433.7 224 416C224 398.3 238.3 384 256 384H512z">
                        </path>
                    </svg>
                    <h4 class="card-title fs-5 lh-1" style="font-size: 1.5rem;">Stats and data</h4>
                    <p class="card-text fs-6 lh-1">We gathers a lot of stats and data about 
                        characters and uses them to optimize your gameplay.
                    </p>    
                </div>
            </article>
            <article class="bg-color2 text-white col-10 col-sm-5 col-lg-3 mx-0 my-2 m-sm-2 m-lg-4 p-4 card rounded-4 custom-card-shadow"">
                <div class="card-body d-flex flex-column justify-content-center align-items-center font1">
                    <svg class="svg-inline--fa fa-calculator card-title fs-6" 
                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calculator" 
                    role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="36">
                        <path fill="currentColor" d="M336 0h-288C22.38 0 0 22.38 0 48v416C0 489.6 22.38 512 48 512h288c25.62 0 48-22.38 48-48v-416C384 22.38 361.6 0 336 0zM64 208C64 199.2 71.2 192 80 192h32C120.8 192 128 
                        199.2 128 208v32C128 248.8 120.8 256 112 256h-32C71.2 256 64 248.8 64 240V208zM64 304C64 295.2 71.2 288 80 288h32C120.8 288 128 295.2 128 304v32C128 344.8 120.8 352 112 352h-32C71.2 352 64 344.8 64 
                        336V304zM224 432c0 8.801-7.199 16-16 16h-128C71.2 448 64 440.8 64 432v-32C64 391.2 71.2 384 80 384h128c8.801 0 16 7.199 16 16V432zM224 336c0 8.801-7.199 16-16 16h-32C167.2 352 160 344.8 160 336v-32C160 
                        295.2 167.2 288 176 288h32C216.8 288 224 295.2 224 304V336zM224 240C224 248.8 216.8 256 208 256h-32C167.2 256 160 248.8 160 240v-32C160 199.2 167.2 192 176 192h32C216.8 192 224 199.2 224 208V240zM320 
                        432c0 8.801-7.199 16-16 16h-32c-8.799 0-16-7.199-16-16v-32c0-8.801 7.201-16 16-16h32c8.801 0 16 7.199 16 16V432zM320 336c0 8.801-7.199 16-16 16h-32c-8.799 0-16-7.199-16-16v-32C256 295.2 263.2 288 272 
                        288h32C312.8 288 320 295.2 320 304V336zM320 240C320 248.8 312.8 256 304 256h-32C263.2 256 256 248.8 256 240v-32C256 199.2 263.2 192 272 192h32C312.8 192 320 199.2 320 208V240zM320 144C320 152.8 312.8 
                        160 304 160h-224C71.2 160 64 152.8 64 144v-64C64 71.2 71.2 64 80 64h224C312.8 64 320 71.2 320 80V144z">
                        </path>
                    </svg>
                    <h4 class="card-title fs-5 lh-1" style="font-size: 1.5rem;">Tools and calculation</h4>
                    <p class="card-text fs-6 lh-1">Spider's Prey develops tools that allow you test and 
                        simulate various things without wasting resources in game.
                    </p>
                </div>
            </article>
        </div>
    </section>

    <!-- Blog -->
    <section class="container-fluid py-4 text-center d-flex flex-column align-items-center">
        <h1 class="font2 display-5 mb-4 text-white">Check the recent news in <a href="index.php?page=blog" style="color: var(--color3);">here</a></h1>
        <div class="container-md mt-2 justify-content-center bg-color3 text-white p-0 d-flex align-items-stretch border border-black border-2 custom-iframe-size">
            <!-- <iframe class="h-auto w-100" src="<?=BASE_URL?>/pages/blog/blog.php" title="embed blog page"></iframe> -->
            <iframe class="h-auto w-100" src="https://blog.prydwen.gg/" title="embed blog page"></iframe>
        </div>
    </section>

    <!-- Contact -->
    <section class="container-fluid py-4 text-center d-flex flex-column align-items-center">
        <div class="row container-md justify-content-between m-0 p-0 overflow-hidden" style="max-width: 58rem;">
            <div class="card col-6 bg-color3 text-white rounded-3 justify-content-center p-5 gap-5">
                <div class="d-flex justify-content-center">
                    <h2 class="font2 display-7 text-start">Meet the team on discord</h2>
                    <a href="#" class="h-100 w-auto"><img src="asset/content/discord.png" alt="" style="max-height: 5rem;"></a>
                </div>
                <p class="font1 fs-5 text-start">Join the ever-growing community on our Discord (over 53000 members) and meet the team!</p>
            </div>
            <div class="card col-5 bg-color3 text-white rounded-3 justify-content-center p-5 gap-5">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="font2 display-7 text-start">Contact Us</h2>
                    <a href="#" class="h-100 w-auto "><img src="asset/content/email.png" alt="" style="max-height: 5rem;"></a>
                </div>
                <p class="font1 fs-5 text-start">Have feedback, issues, or want to join our out team?
                    <br>Reach out to us at spiders.prey@gmail.com — we're happy to hear from you!
                </p>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="container-fluid py-4 d-flex flex-column align-items-center">
    <div class="container-md text-start" style="max-width: 80rem;">
        <h1 class="text-white font2 display-7">FAQ</h1>
        <div class="row g-4">
            <div class="accordion" id="accordionExample1">
                <div class="accordion-item show">
                    <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button fs-5 btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Is Spider's Prey free to use?
                    </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample1">
                    <div class="accordion-body">
                        Yes! All of our guides, tier lists, and character breakdowns are completely free to access.
                    </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionExample2">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button fs-5 btn-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            How often do you update the character tier lists?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample2">
                        <div class="accordion-body">
                            We update our tier lists and recommendations whenever new characters or balance patches are released. We also provide you guys with the up-to-date news about gacha gaming.
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionExample3">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button fs-5 btn-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Which gacha games do you cover?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample3">
                        <div class="accordion-body">
                            We currently cover Honkai: Star Rail, Genshin Impact, and Wuthering Waves, with plans to expand into other popular gacha games in the future.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
</main>

<?php
include_once(__DIR__ . '/../include/footer.php');
?>