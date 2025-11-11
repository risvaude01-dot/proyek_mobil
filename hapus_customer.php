<?php
// hapus_customer.php

session_start();
// Pastikan user sudah login sebelum melakukan aksi
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}

include 'koneksi.php';

// 1. Ambil ID Customer dari URL (GET Request)
$id_customer = $_GET['id'];

// 2. Query DELETE
// Hati-hati: Pastikan nama kolom ID benar (id_customer)
$query = "DELETE FROM tabel_customer WHERE id_customer='$id_customer'";

if (mysqli_query($koneksi, $query)) {
    // 3. Jika berhasil, kembali ke halaman data_customer.php
    header("location:data_customer.php?pesan=hapus_berhasil");
} else {
    // Jika gagal, tampilkan pesan error atau kembali dengan pesan gagal
    // Seringkali gagal jika ID Customer ini masih dipakai di tabel_penjualan (Foreign Key Constraint)
    // Tampilkan pesan yang lebih informatif jika ada error Foreign Key
    
    $error_msg = mysqli_error($koneksi);
    
    // Cek jika errornya karena Foreign Key Constraint
    if (strpos($error_msg, 'foreign key constraint') !== false) {
        header("location:data_customer.php?pesan=hapus_gagal&error=fk");
    } else {
        header("location:data_customer.php?pesan=hapus_gagal");
    }
}

exit();
?>