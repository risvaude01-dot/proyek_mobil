<?php
// START: Proteksi Session
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
// END: Proteksi Session

include 'koneksi.php';

// Ambil data mobil yang stoknya > 0 untuk dropdown
$query_mobil = "SELECT id_mobil, merk, model, harga_jual, stok FROM tabel_mobil WHERE stok > 0 ORDER BY merk ASC";
$result_mobil = mysqli_query($koneksi, $query_mobil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Barang Keluar (Penjualan)</title>
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
        <a href="index.php" class="btn btn-secondary mb-3">< Kembali ke Dashboard</a>
        <h1>Form Barang Keluar (Penjualan)</h1>
        
        <form action="proses_barang_keluar.php" method="POST">
            <div class="mb-3">
                <label for="pilih_mobil" class="form-label">Pilih Mobil:</label>
                <select name="pilih_mobil" id="pilih_mobil" class="form-select" required>
                    <option value="">-- Pilih Mobil --</option>
                    <?php 
                    while ($data = mysqli_fetch_assoc($result_mobil)) {
                        // Value-nya digabungkan: ID_MOBIL|HARGA_JUAL (ini penting untuk proses hitung di PHP)
                        echo "<option value='{$data['id_mobil']}|{$data['harga_jual']}'>
                                " . htmlspecialchars($data['merk'] . " " . $data['model']) . " (Stok: {$data['stok']}, Harga: Rp " . number_format($data['harga_jual'], 0, ',', '.') . ")
                              </option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="jumlah_dijual" class="form-label">Jumlah Dijual (Unit):</label>
                <input type="number" name="jumlah_dijual" id="jumlah_dijual" min="1" value="1" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="nama_pembeli" class="form-label">Nama Pembeli:</label>
                <input type="text" name="nama_pembeli" id="nama_pembeli" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="kontak_pembeli" class="form-label">Kontak Pembeli (HP/Email):</label>
                <input type="text" name="kontak_pembeli" id="kontak_pembeli" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Catat Penjualan</button>
        </form>
    </div>
</body>
</html>
<?php
mysqli_close($koneksi);
?>