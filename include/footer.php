<!-- Footer -->

<?php 
// try {
//     $show2 = $show;
// } catch (Exception $e) {
//     $show2 = true;
// }
// if($show): ?>
<footer class="container-fluid bg-color1 px-0 d-flex justify-content-center align-items-center">
    <div class="container-md p-5 pb-3 row g-0 g-md-4 text-center text-md-start">
        <section class="col-12 col-md-6">
            <a class="p-0 m-0 d-flex justify-content-center justify-content-md-start" href="#">
                <img src="<?=BASE_URL ?>/asset/content/Logo.png" alt="" class="w-auto" id="navbarLogo" style="height: min(10vh, 4rem);">
            </a>
            <p class="text-white font1 mt-2">Spider's Prey is a dedicated guide hub for gacha gamers, 
                providing tier lists, builds, and in-depth strategies to help you dominate your favorite games.
            </p>
        </section>
        <section class="col-12 col-md-3">
            <p class="fs-l text-white font1 ">Pages</p>
            <ul class="list-unstyled">
                <li><a href="#" class="text-white text-decoration-none font1">Home</a></li>
                <li><a href="#" class="text-white text-decoration-none font1">Game</a></li>
                <li><a href="#" class="text-white text-decoration-none font1">Blog</a></li>
            </ul>
        </section>
        <section class="col-12 col-md-3">
            <p class="fs-l text-white font1 ">Contact</p>
            <ul class="list-unstyled">
                <li><a href="#" class="text-white text-decoration-none font1">Discord</a></li>
                <li><a href="#" class="text-white text-decoration-none font1">Email</a></li>
            </ul>
        </section>
        <section class="col-12 mt-3 mt-md-4 d-flex flex-column flex-md-row align-items-center justify-content-between">
            <p class="">
                <a href="index.php?page=policy" class="font1 fs-6 text-decoration-none text-white">Privacy Policy</a>
            </p>
            <p class="font1 fs-6 text-white">Copyright &copy; 2025 SpiderPrey.gg</p>
        </section>
    </div>
</footer>
<?php 
// endif
?>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>