<?php
// Tambahkan ini di baris paling atas untuk Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta'); 

$host = "localhost";
$user = "root";
$pass = "";
$db   = "share-doc"; 

$conn = mysqli_connect($host, $user, $pass, $db);

// Sinkronkan juga zona waktu koneksi MySQL dengan PHP
mysqli_query($conn, "SET time_zone = '+07:00'");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>