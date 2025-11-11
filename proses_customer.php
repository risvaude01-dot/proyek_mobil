<?php
// proses_customer.php
// Fungsinya: Menerima data dari form, lalu menyimpannya ke tabel_customer.

session_start();
// Cek jika belum login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php");
    exit();
}

include 'koneksi.php'; // Pastikan lo punya file koneksi.php

// 1. Ambil data dari form yang dikirim (menggunakan POST)
// Kita gunakan mysqli_real_escape_string untuk keamanan data
$nama_customer = mysqli_real_escape_string($koneksi, $_POST['nama_customer']);
$telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

// 2. Buat Query INSERT
$sql_insert = "INSERT INTO tabel_customer (nama_customer, telepon, alamat)
               VALUES ('$nama_customer', '$telepon', '$alamat')";

// 3. Jalankan Query
$insert_success = mysqli_query($koneksi, $sql_insert);

if ($insert_success) {
    // Jika berhasil, redirect kembali ke halaman data_customer dengan pesan sukses
    header("location:data_customer.php?pesan=sukses_tambah");
    exit();
} else {
    // Jika gagal
    header("location:data_customer.php?pesan=gagal");
    exit();
}
?>