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

function getLink($gId, $s){
    if($gId == null || $s == null ){
        if($gId != null){
            return "?page=blog&id=$gId";
        }
        if($s != null){
            return "?page=blog&s=$s";
        }
        return "?page=blog";
    }
    return "?page=blog&id=$gId&s=$s";
}

function uploadFile($file, $destinationDir, $prefix = 'file_', $maxSizeMB = 10, $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp']) {
    $errors = [];
    $filename = $file["name"] ?? '';
    $tmpName = $file["tmp_name"] ?? '';
    $fileSize = $file["size"] ?? 0;

    if (empty($filename)) {
        $errors[] = "File is required.";
        return [null, $errors];
    }

    $fileType = mime_content_type($tmpName);
    $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $newfilename = uniqid($prefix, true) . '.' . $fileExt;
    $uploadPath = $destinationDir . $newfilename;

    // Validate type and size
    $validExts = ['png', 'jpg', 'jpeg', 'webp'];
    if (!in_array($fileType, $allowedTypes) || !in_array($fileExt, $validExts)) {
        $errors[] = "Only PNG, JPG, or WEBP images are allowed.";
    }

    if ($fileSize > $maxSizeMB * 1024 * 1024) {
        $errors[] = "File must be under {$maxSizeMB}MB.";
    }

    if (!getimagesize($tmpName)) {
        $errors[] = "Uploaded file is not a valid image.";
    }

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0755, true);
    }

    if (empty($errors)) {
        if (!move_uploaded_file($tmpName, $uploadPath)) {
            $errors[] = "Failed to move uploaded file.";
            return [null, $errors];
        }
        return [$newfilename, []];
    }

    return [null, $errors];
}

function deleteFile($filepath) {
    if (!empty($filepath) && file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}
