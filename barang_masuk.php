<?php
// START: Proteksi Session
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
// END: Proteksi Session

include 'koneksi.php';

// Ambil data mobil yang sudah ada (untuk opsi tambah stok)
$query_mobil = "SELECT id_mobil, merk, model, stok, harga_beli, harga_jual FROM tabel_mobil ORDER BY merk ASC";
$result_mobil = mysqli_query($koneksi, $query_mobil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Barang Masuk (Pembelian Stok)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Dashboard</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>
    
    <div class="container mt-4">
        <a href="data_mobil.php" class="btn btn-secondary mb-3">< Kembali ke Data Stok</a>
        <h1>Form Barang Masuk (Pembelian Stok)</h1>

        <form action="proses_barang_masuk.php" method="POST" enctype="multipart/form-data">

            <h4 class="mt-4">1. Update Stok Mobil Lama</h4>
            <div class="mb-3">
                <label for="id_mobil" class="form-label">Pilih Mobil Lama:</label>
                <select name="id_mobil" id="id_mobil" class="form-select">
                    <option value="">-- Pilih Mobil Lama (Kosongkan jika Mobil Baru) --</option>
                    <?php 
                    while ($data = mysqli_fetch_assoc($result_mobil)) {
                        echo "<option value='{$data['id_mobil']}'>
                                " . htmlspecialchars($data['merk'] . " " . $data['model']) . " (Stok Saat Ini: {$data['stok']})
                              </option>";
                    }
                    ?>
                </select>
                <div class="form-text">Jika memilih mobil lama, **jangan** isi detail mobil baru di bawah.</div>
            </div>
            
            <hr>
            
            <h4 class="mt-4">2. Tambah Mobil Baru (Hanya isi jika di atas kosong)</h4>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="merk" class="form-label">Merk Mobil:</label>
                    <input type="text" name="merk" id="merk" class="form-control" placeholder="Contoh: Toyota">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="model" class="form-label">Model Mobil:</label>
                    <input type="text" name="model" id="model" class="form-control" placeholder="Contoh: Avanza">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="harga_beli" class="form-label">Harga Beli Satuan (Modal):</label>
                    <input type="text" name="harga_beli" id="harga_beli" class="form-control" placeholder="Contoh: 180000000">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="harga_jual" class="form-label">Harga Jual Satuan:</label>
                     <input type="text" name="harga_jual" id="harga_jual" class="form-control" placeholder="Contoh: 200000000">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Mobil (Opsional):</label>
                <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                <div class="form-text">File gambar harus disimpan di folder **`img_mobil/`**.</div>
            </div>
            
            <hr>