<?php
// Konfigurasi database
$host = "localhost";
$username = "root";
$password = ""; // Sesuaikan dengan password MySQL Anda, biasanya kosong
$database = "final_project_ppw1";

// Base URL (adjust if using a different server or subfolder)
define('BASE_URL', 'http://localhost/final_project');

// Default timezone (can be adjusted)
date_default_timezone_set('Asia/Jakarta');

// Start session (only if you’ll be using login/session features)
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// function isLoggedIn() {
//     session_start();
//     return isset($_SESSION['user_id']);
// }

// Fungsi untuk redirect jika belum login
// if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
//     header("Location: index.php?page=login");
//     exit;
// }

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);
// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d > 0) return $diff->d . " day" . ($diff->d > 1 ? "s" : "") . " ago";
    if ($diff->h > 0) return $diff->h . " hour" . ($diff->h > 1 ? "s" : "") . " ago";
    if ($diff->i > 0) return $diff->i . " minute" . ($diff->i > 1 ? "s" : "") . " ago";

    return "just now";
}
