<?php
include_once(__DIR__ . '/../include/config.php');
session_start();

echo"<script>alert('logout')</script>";

// // Hapus semua session
session_unset();
session_destroy();

// // Redirect ke halaman login
header("Location: ".BASE_URL."/index.php?page=home");
exit();