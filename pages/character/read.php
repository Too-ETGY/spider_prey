<?php
include_once(__DIR__ . '/../../include/config.php');

$game_id = (int) ($_GET['game_id'] ?? 0);
$filter = $_GET['filter'] ?? '';

$query = $filter 
    ? "CALL get_character_with_categories($game_id, '$filter')"
    : "SELECT DISTINCT char_id, char_name, char_icon, game_id FROM character_with_categories WHERE game_id = $game_id";

$result = mysqli_query($conn, $query);

$characters = [];
while ($row = mysqli_fetch_assoc($result)) {
    $characters[] = $row;
}

header('Content-Type: application/json');
echo json_encode($characters);
?>