<?php
include_once(__DIR__ . '/../../include/config.php');   
include_once(__DIR__ . '/../../include/navbar_home.php');

// === DELETE logic ===
if (isset($_POST['delete'])) {
    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {
        // Get the icon file name first
        $stmt = $conn->prepare("SELECT blog_img FROM blog_table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $iconFile = $row['blog_img'];

            // Delete the game
            $delStmt = $conn->prepare("DELETE FROM blog_table WHERE id = ?");
            $delStmt->bind_param("i", $id);
            $deleteSuccess = $delStmt->execute();

            if ($deleteSuccess) {
                // Delete the icon file from the server if it exists
                $imagePath = __DIR__ . '/../../uploads/blog/' . $iconFile;
                if (!empty($iconFile) && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $_SESSION['flash'] = 'BLog Successfully Deleted!';
            } else {
                $_SESSION['flash'] = 'Error deleting game.';
            }

            $delStmt->close();
        }

        $stmt->close();
        header("Location: index.php?page=blog");
        exit;
    } else {
        $_SESSION['flash'] = 'Invalid game selected.';
        header("Location: index.php?page=blog");
        exit;
    }
}

$game_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$search = isset($_GET['s']) && $_GET['s'] !== '' ? $_GET['s'] : null;

// Count blogs using a prepared statement
$count_stmt = $conn->prepare("SELECT count_blogs(?, ?) AS total");
$count_stmt->bind_param("is", $game_id, $search);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_data = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / 8);
$count_stmt->close();

// Get blog list using a stored procedure
$stmt = $conn->prepare("CALL get_blogs(?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("iis", $game_id, $page, $search);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $result->free();
} else {
    echo "Error: " . $stmt->error;
}

mysqli_next_result($conn);
$stmt->close();
?>
  
<div class="bg-color3 container-fluid px-0 d-flex align-items-center justify-content-center">
        <div class="container my-5 mx-2 p-0 text-center text-white row align-items-start justify-content-between" style="min-height: 50vh;">
            <main class="bg-color1 col-12 col-md-8">
                <div class="row row-cols row-cols-md-1 row-cols-lg-2 align-items-start text-start">  

                <?php
                 if (!empty($_SESSION['flash'])): ?>
                    <script>
                        alert('<?= htmlspecialchars($_SESSION['flash']) ?>');
                    </script>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <?php if (count($rows) > 0) : ?>
                    <?php foreach($rows as $row) : ?>
                        <div class="position-relative px-0">
                            <a class="card col my-4 px-4 bg-transparent text-white text-decoration-none" 
                            href="index.php?page=blog/read&id=<?=$row['id']?>">
                                <img src="<?=BASE_URL ?>/uploads/blog/<?= $row['blog_img']?>" 
                                    class="card-img-top" 
                                    alt="(icon of <?=  htmlspecialchars($row['blog_title'])?>)">
                                <div class="card-body bg-color4 w-100">
                                    <div class="d-flex align-items-center gap-1">
                                        <h5 class="card-title fs-4 font1 mb-0"><?= htmlspecialchars($row['blog_title']) ?></h5>
                                    </div>
                                    <h6 class="card-title fs-6 font1 my-2">
                                        <?=  htmlspecialchars($row['game_name'])?> | 
                                        <?=  date('F j, Y', strtotime($row['blog_date']))?>
                                    </h6>
                                    <p class="card-text lh-1 mb-2">
                                        <?= htmlspecialchars(mb_strimwidth(html_entity_decode(strip_tags($row['blog_desc'])), 0, 180, '...')) ?>
                                    </p>
                                </div>
                            </a>

                            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                            <!-- Delete Button Floating Top Right -->
                            <img src="<?= BASE_URL?>/asset/content/delete.png" 
                                alt="delete.png" 
                                class="position-absolute z-2" 
                                style="top: 0.5rem; right: 0.75rem; max-height: 1.5rem; cursor: pointer;"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal<?=$row['id']?>">

                            <!-- Modal -->
                            <form action="index.php?page=blog" method="POST">
                                <div class="modal fade" id="deleteModal<?=$row['id']?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this article?
                                                <input type="hidden" name="id" value="<?=$row['id']?>">
                                                <p><b><?= htmlspecialchars($row['blog_title']) ?> 
                                                | <?= htmlspecialchars($row['game_name']) ?></b></p>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" name="delete" value="Delete" class="btn btn-danger">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                        <?php endforeach ?>
                <?php else : ?>
                    <div class='my-4 px-4'>No Results Found.</div>
                <?php endif ?>
                </div>

                <div class="row row-cols-2 px-4">
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-start">
                        <?php
                        $base = getLink($game_id, urlencode($search));
                        
                        if ($page > 1): ?>
                            <?php if ($page != 1) :?>
                            <li class="page-item"><a class="page-link" href="<?= $base ?>">First</a></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="<?= $base ?>&p=<?= $page - 1 ?>">Previous</a></li>
                        <?php endif; ?>

                        <?php
                        $displayed = [];

                        // Always display first 3
                        for ($i = 1; $i <= 3 && $i <= $total_pages; $i++) {
                            $displayed[] = $i;
                        }

                        // Always display last 2
                        for ($i = $total_pages - 1; $i <= $total_pages; $i++) {
                            if ($i > 3) $displayed[] = $i;
                        }

                        // Display current page +/- 1 for context
                        for ($i = $page - 1; $i <= $page + 1; $i++) {
                            if ($i > 3 && $i < $total_pages - 1) $displayed[] = $i;
                        }

                        $displayed = array_unique(array_filter($displayed));
                        sort($displayed);

                        $last = 0;
                        foreach ($displayed as $i):
                            if ($last && $i != $last + 1):
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            endif;
                            if ($i == $page):
                                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                            else:
                                echo '<li class="page-item"><a class="page-link" href="' . $base . '&p=' . $i . '">' . $i . '</a></li>';
                            endif;
                            $last = $i;
                        endforeach;
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="<?= $base ?>&p=<?= $page + 1 ?>">Next</a></li>
                            <?php if ($page != $total_pages) :?>
                                <li class="page-item"><a class="page-link" href="<?= $base ?>&p=<?= $total_pages ?>">Last</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    </nav>
                    <?php endif; ?>


                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                    <div class="mb-4 px-0 d-flex justify-content-end">
                        <a href="index.php?page=blog/create" class="btn btn-success">Add Blog</a>
                    </div>
                    <?php endif; ?>  
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