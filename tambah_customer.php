<?php
// tambah_customer.php - Form Input Customer Baru

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
$username = $_SESSION['username'] ?? 'Admin'; 
include 'koneksi.php';

// Cek apakah ada pesan dari proses sebelumnya
$pesan_error = '';
if (isset($_GET['pesan']) && $_GET['pesan'] == 'input_gagal') {
    $pesan_error = '❌ Gagal menambahkan data customer! Pastikan semua kolom terisi dengan benar.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Customer | Showroom Central</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* CSS KONSISTEN */
        body { background-color: #f4f6f9; display: flex; flex-direction: column; min-height: 100vh; padding-top: 70px; }
        .footer-custom { background-color: #2c3e50; color: #ecf0f1; padding: 40px 0; margin-top: auto; }
        .footer-copy { background-color: #21303e; padding: 10px 0; color: #95a5a6; }
    </style>
</head>
<body>
    
    <?php include 'navbar_partial.php'; ?> <div class="container py-4"> 
        <h2 class="fw-bolder text-dark mb-4"><i class="bi bi-person-plus-fill me-2 text-primary"></i> Tambah Customer Baru</h2>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $pesan_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Form Data Customer</h5>
            </div>
            <div class="card-body">
                
                <form method="post" action="proses_tambah_customer.php">
                    
                    <div class="mb-3">
                        <label for="nama_customer" class="form-label">Nama Customer</label>
                        <input type="text" class="form-control" id="nama_customer" name="nama_customer" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="no_telp" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="no_telp" name="no_telp" required placeholder="Contoh: 081234567890">
                    </div>
                    
                    <hr>
                    <button type="submit" class="btn btn-success me-2"><i class="bi bi-save-fill me-1"></i> Simpan Data</button>
                    <a href="data_customer.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-1"></i> Batal</a>
                </form>
                
            </div>
        </div>
    </div> <?php include 'footer_partial.php'; ?> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>