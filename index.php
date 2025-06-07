<?php
// Get page parameter (?page=game/create OR ?page=login)
$page = $_GET['page'] ?? 'home';

// Break into parts
$parts = explode('/', $page);

// If just one part, e.g. "login"
if (count($parts) === 1) {
    $main = $parts[0];

    // If it's a flat page (like login.php or policy.php)
    $flatPage = "pages/$main.php";
    $nestedPage = "pages/$main/$main.php";

    if (file_exists($flatPage)) {
        $file = $flatPage;
    } elseif (file_exists($nestedPage)) {
        $file = $nestedPage;
    } else {
        $file = "pages/404.php";
    }

} else {
    // If two parts like "game/create"
    $main = $parts[0];
    $sub = $parts[1];
    $file = "pages/$main/$sub.php";

    if (!file_exists($file)) {
        $file = "pages/404.php";
    }
}

include_once $file;
