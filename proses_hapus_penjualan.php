<?php
// proses_hapus_penjualan.php - LOGIC MENGHAPUS TRANSAKSI DAN MENGEMBALIKAN STOK

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php");
    exit();
}

include 'koneksi.php';

// 1. Cek apakah ID transaksi diterima dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:data_penjualan.php?pesan=id_tidak_ditemukan");
    exit();
}

$id_penjualan = mysqli_real_escape_string($koneksi, $_GET['id']);

// 2. Ambil data transaksi yang akan dihapus (PENTING untuk stok!)
$query_data_hapus = mysqli_query($koneksi, "
    SELECT id_mobil, jumlah 
    FROM tabel_penjualan 
    WHERE id_penjualan = '$id_penjualan'
");

if (mysqli_num_rows($query_data_hapus) == 0) {
    header("location:data_penjualan.php?pesan=data_tidak_ditemukan");
    exit();
}

$data_hapus = mysqli_fetch_assoc($query_data_hapus);
$id_mobil = $data_hapus['id_mobil'];
$jumlah_kembali = $data_hapus['jumlah']; // Jumlah unit yang akan dikembalikan ke stok

// Mulai Transaksi Database (untuk memastikan dua query berjalan sukses)
mysqli_begin_transaction($koneksi);
$success = true;

// 3. HAPUS Transaksi dari tabel_penjualan
$query_hapus = "DELETE FROM tabel_penjualan WHERE id_penjualan = '$id_penjualan'";
if (!mysqli_query($koneksi, $query_hapus)) {
    $success = false;
}

// 4. KEMBALIKAN STOK di tabel_mobil
if ($success) {
    $query_update_stok = "
        UPDATE tabel_mobil 
        SET stok = stok + $jumlah_kembali 
        WHERE id_mobil = '$id_mobil'
    ";
    if (!mysqli_query($koneksi, $query_update_stok)) {
        $success = false;
    }
}

// 5. Commit atau Rollback Transaksi
if ($success) {
    mysqli_commit($koneksi);
    header("location:data_penjualan.php?pesan=hapus_sukses");
    exit();
} else {
    mysqli_rollback($koneksi);
    header("location:data_penjualan.php?pesan=hapus_gagal");
    exit();
}
?>