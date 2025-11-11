<?php
// koneksi.php

$host = "localhost";
$user = "root";
$pass = ""; // Ganti sesuai password MySQL lo
$db = "db_showroom"; // Pastikan nama database lo ini

// Koneksi ke MySQL
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (mysqli_connect_errno()){
    echo "Koneksi database gagal : " . mysqli_connect_error();
    die();
}
?>