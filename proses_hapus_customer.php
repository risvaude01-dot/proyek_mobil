<?php
// proses_hapus_customer.php - LOGIC MENGHAPUS DATA CUSTOMER

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php");
    exit();
}

include 'koneksi.php';

// 1. Cek ID customer
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:data_customer.php?pesan=id_tidak_valid");
    exit();
}

$id_customer = mysqli_real_escape_string($koneksi, $_GET['id']);

// 2. Query DELETE
$query_delete = "DELETE FROM tabel_customer WHERE id_customer = '$id_customer'";

if (mysqli_query($koneksi, $query_delete)) {
    // Sukses
    header("location:data_customer.php?pesan=hapus_sukses");
    exit();
} else {
    // Gagal (Kemungkinan karena Foreign Key Constraint, customer sudah pernah bertransaksi)
    // Tampilkan pesan yang jelas
    $error_message = "Hapus gagal! Customer ini mungkin sudah tercatat dalam riwayat transaksi (Foreign Key Constraint).";
    header("location:data_customer.php?pesan=hapus_gagal&error=" . urlencode($error_message));
    exit();
}
?>