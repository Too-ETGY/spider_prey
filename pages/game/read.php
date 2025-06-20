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

// Jika data tidak ditemukan, kembali ke halaman utama
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

if (isset($_POST['delete'])) {
    $del_id = (int) ($_POST['id'] ?? 0);
    if ($del_id > 0) {
        // Get the icon file name
        $stmt = $conn->prepare("SELECT char_icon FROM character_table WHERE id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $iconFile = $row['char_icon'];

            // Delete the database record
            $delStmt = $conn->prepare("DELETE FROM character_table WHERE id = ?");
            $delStmt->bind_param("i", $del_id);
            $deleteSuccess = $delStmt->execute();
            $delStmt->close();

            if ($deleteSuccess) {
                // Use the delete function
                $imagePath = __DIR__ . '/../../uploads/char/' . $iconFile;
                deleteFile($imagePath);

                $_SESSION['flash'] = 'Character Deleted!';
            } else {
                $_SESSION['flash'] = 'Error deleting.';
            }
        }
        $stmt->close();
        
        header("Location: index.php?page=game/read&id=$game_id");
        exit;
    } else {
        $_SESSION['flash'] = 'Invalid char selected.';
        header("Location: index.php?page=game/read&id=$game_id");
        exit;
    }
}

include_once(__DIR__ . '/../../include/navbar_game_read.php');
?>

<style>
        .filter-icon {
                max-height: 1.75rem;
                cursor: pointer;
                margin: 5px;
                border-radius: 5px;
                transition: 0.2s;
        }

        .filter-icon.active {
                border: 2px solid white;
        }

        .custom-height{
                height: 9.5rem;
        }
</style>

<div class="container-fluid px-0 d-flex align-items-center justify-content-center">
        <main class="bg-color1 container my-5 mx-3 text-center text-white d-flex flex-column" style="min-height: 50vh;">
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
                                <h4 class="d-flex justify-content-start font2 fw-normal fs-3 ">Charater List</h4>
                                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):?>
                                        <div class=" d-flex justify-content-end">
                                                <a href="index.php?page=character/create&id=<?=$game_id?>" class="btn btn-success">Add Char</a>
                                        </div>
                                <?php endif;?>
                        </div>
                </section>

                <section class="bg-color4 mb-4 rounded-1 align-items-center">
                        <?php 
                        $catg_run = mysqli_query($conn, "SELECT * FROM category_table WHERE game_id = $game_id");

                        $categories = [];
                        while ($catg_row = mysqli_fetch_assoc($catg_run)) {
                                $categories[] = $catg_row;
                        }

                        $total = count($categories);
                        $no = 0;

                        foreach ($categories as $catg_row) {
                                $catg_id = $catg_row['id'];
                                echo "<div class='d-inline-block mx-auto'>";

                                // Get category values
                                $catg_value_run = mysqli_query($conn, 
                                        "SELECT * FROM category_value_table WHERE category_id = $catg_id ORDER BY catg_value_name ASC");

                                while ($value_row = mysqli_fetch_assoc($catg_value_run)) {
                                        echo '
                                        <img 
                                        src="' . BASE_URL . '/uploads/category/' . $value_row['catg_value_icon'] . '" 
                                        alt="' . htmlspecialchars($value_row['catg_value_name']) . '" 
                                        data-category-id="'.$catg_id.'"
                                        data-category-name="'.$catg_row['category_name'].'" 
                                        data-value-name="'.$value_row['catg_value_name'].'" 
                                        data-value-id="'.$value_row['id'].'"
                                        class="filter-icon">';
                                }

                                echo "</div>";

                                $no++;
                                if ($no < $total) {
                                        echo '<span class="mx-1">|</span>';
                                }
                        }
                        ?>
                </section>

                <section class="bg-color4 mb-4 rounded-1">
                        <div id="selected-output" class="row justify-content-start"></div>
                </section>
        </main>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
const selectedFilters = {};
// const gameId = <?= $game_id ?>;

function fetchCharacters(filterString = "") {
        const gameId = <?= $game_id ?>;

        $.ajax({
                url: "index.php?page=character/read",
                method: "GET",
                data: { game_id: gameId, filter: filterString },
                dataType: "json",
                success: function (data) {
                        let html = "";
                        if (data.length === 0) {
                                html = "<p class='text-white'>No characters matched.</p>";
                        } else {data.forEach(char => {
                                // for(i=0; i<15; i++){
                                html += 
                                `<div class="col-6 col-sm-4 col-md-3 col-lg-2 p-2">
                                <div class="position-relative text-white rounded-2 p-2 h-100">
                                        <a class="text-decoration-none text-white d-block text-center" href="?page=character&id=${char.char_id}">
                                        <img class="border rounded-3 object-fit-contain w-auto custom-height""
                                                src="<?= BASE_URL ?>/uploads/char/${char.char_icon}" 
                                                alt="${char.char_name}">
                                        <p class="fs-6 font1 mt-2 mb-0">
                                                ${char.char_name} <br>
                                                <span class="fs-s">${char.char_speciality ?? ''}</span>
                                        </p>
                                        </a>

                                        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                                        <img src="<?= BASE_URL?>/asset/content/delete.png" 
                                        alt="delete.png" 
                                        class="position-absolute z-2" 
                                        style="top: -0.75rem; right: 1.25rem; max-height: 1.5rem; cursor: pointer;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal${char.char_id}">

                                        <form action="index.php?page=game/read&id=<?=$game_id?>" method="POST">
                                        <div class="modal fade" id="deleteModal${char.char_id}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                        <div class="modal-header">
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                        Are you sure you want to delete this character?
                                                        <input type="hidden" name="id" value="${char.char_id}">
                                                        <p><b>${char.char_name}</b></p>
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
                                </div>`;

                                // }
                                });
                        }
                        $('#selected-output').html(html);
                },
                error: function () {
                        $('#selected-output').html("<p class='text-danger'>Failed to load characters.</p>");
                }
        });
}

$(document).on('click', '.filter-icon', function () {
    const categoryId = $(this).data('category-id');
    const valueId = $(this).data('value-id');

    // Deselect others in the same category
    $(`.filter-icon[data-category-id=${categoryId}]`).removeClass('active');

    // Toggle logic
    if (selectedFilters[categoryId] === valueId) {
        delete selectedFilters[categoryId];
    } else {
        $(this).addClass('active');
        selectedFilters[categoryId] = valueId;
    }

    const filterString = Object.values(selectedFilters).join(',');
    fetchCharacters(filterString);
});

$(document).ready(function () {
    fetchCharacters(); // load all characters initially
});
</script>

</body>
</main>

<?php 
include_once(__DIR__ . '/../../include/footer.php');

// Tutup koneksi
mysqli_close($conn);
?>