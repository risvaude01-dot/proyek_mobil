<?php
// edit_mobil.php - Form Edit Mobil

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
$username = $_SESSION['username'] ?? 'Admin'; 
include 'koneksi.php';

// Ambil ID yang akan di edit
$id_mobil = $_GET['id'];
$data_mobil = mysqli_query($koneksi, "SELECT * FROM tabel_mobil WHERE id_mobil='$id_mobil'");
$d = mysqli_fetch_assoc($data_mobil);

if (!$d) {
    echo "<script>alert('Data mobil tidak ditemukan!'); window.location='data_mobil.php';</script>";
    exit();
}

$pesan_error = '';
if (isset($_GET['pesan']) && $_GET['pesan'] == 'update_gagal') {
    $pesan_error = '❌ Gagal memperbarui data mobil! Pastikan semua kolom terisi dan format gambar/harga benar.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mobil | Showroom Central</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* CSS KONSISTEN */
        body { background-color: #f4f6f9; display: flex; flex-direction: column; min-height: 100vh; padding-top: 70px; }
        .footer-custom { background-color: #2c3e50; color: #ecf0f1; padding: 40px 0; margin-top: auto; }
        .footer-copy { background-color: #21303e; padding: 10px 0; color: #95a5a6; }
        .current-img { max-width: 150px; height: auto; border-radius: 8px; margin-top: 10px;}
    </style>
</head>
<body>
    
    <?php include 'navbar_partial.php'; ?>
    
    <div class="container py-4"> 
        <h2 class="fw-bolder text-dark mb-4"><i class="bi bi-gear-fill me-2 text-info"></i> Edit Data Unit Mobil</h2>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $pesan_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Form Edit Mobil: <?php echo htmlspecialchars($d['merk'] . ' ' . $d['model']); ?></h5>
            </div>
            <div class="card-body">
                
                <form method="post" action="proses_edit_mobil.php" enctype="multipart/form-data">
                    <input type="hidden" name="id_mobil" value="<?php echo $d['id_mobil']; ?>">
                    <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($d['gambar']); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="merk" class="form-label">Merk Mobil</label>
                            <input type="text" class="form-control" id="merk" name="merk" required value="<?php echo htmlspecialchars($d['merk']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="model" class="form-label">Model Mobil</label>
                            <input type="text" class="form-control" id="model" name="model" required value="<?php echo htmlspecialchars($d['model']); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tahun" class="form-label">Tahun Produksi</label>
                            <input type="number" class="form-control" id="tahun" name="tahun" required min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo htmlspecialchars($d['tahun']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="warna" class="form-label">Warna</label>
                            <input type="text" class="form-control" id="warna" name="warna" required value="<?php echo htmlspecialchars($d['warna']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="stok" class="form-label">Stok Unit</label>
                            <input type="number" class="form-control" id="stok" name="stok" required min="0" value="<?php echo htmlspecialchars($d['stok']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="harga_beli" class="form-label">Harga Beli (Modal)</label>
                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" required min="0" value="<?php echo htmlspecialchars($d['harga_beli']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual (Retail)</label>
                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="0" value="<?php echo htmlspecialchars($d['harga_jual']); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Ganti Gambar Mobil</label>
                        <?php if ($d['gambar']): ?>
                            <p class="mb-1 small">Gambar saat ini:</p>
                            <img src="gambar/<?php echo htmlspecialchars($d['gambar']); ?>" alt="Gambar Mobil Saat Ini" class="current-img mb-2">
                        <?php endif; ?>
                        
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
                    </div>
                    
                    <hr>
                    <button type="submit" class="btn btn-info text-white me-2"><i class="bi bi-arrow-repeat me-1"></i> Update Data</button>
                    <a href="data_mobil.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-1"></i> Batal</a>
                </form>
                
            </div>
        </div>
    </div> <?php include 'footer_partial.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>