<?php
// edit_customer.php - Form Edit Customer

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
$username = $_SESSION['username'] ?? 'Admin'; 
include 'koneksi.php';

// Ambil ID yang akan di edit
$id_customer = $_GET['id'];
$data_customer = mysqli_query($koneksi, "SELECT * FROM tabel_customer WHERE id_customer='$id_customer'");
$d = mysqli_fetch_assoc($data_customer);

if (!$d) {
    echo "<script>alert('Data customer tidak ditemukan!'); window.location='data_customer.php';</script>";
    exit();
}

// Cek apakah ada pesan dari proses sebelumnya
$pesan_error = '';
if (isset($_GET['pesan']) && $_GET['pesan'] == 'update_gagal') {
    $pesan_error = '❌ Gagal memperbarui data customer! Pastikan semua kolom terisi dengan benar.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer | Showroom Central</title>
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
    
    <?php include 'navbar_partial.php'; ?>
    
    <div class="container py-4"> 
        <h2 class="fw-bolder text-dark mb-4"><i class="bi bi-person-check-fill me-2 text-info"></i> Edit Data Customer</h2>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $pesan_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Form Edit Customer: <?php echo htmlspecialchars($d['nama_customer']); ?></h5>
            </div>
            <div class="card-body">
                
                <form method="post" action="proses_edit_customer.php">
                    <input type="hidden" name="id_customer" value="<?php echo $d['id_customer']; ?>">
                    
                    <div class="mb-3">
                        <label for="nama_customer" class="form-label">Nama Customer</label>
                        <input type="text" class="form-control" id="nama_customer" name="nama_customer" required value="<?php echo htmlspecialchars($d['nama_customer']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo htmlspecialchars($d['alamat']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="no_telp" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="no_telp" name="no_telp" required value="<?php echo htmlspecialchars($d['no_telp'] ?? ''); ?>" placeholder="Contoh: 081234567890">
                    </div>
                    
                    <hr>
                    <button type="submit" class="btn btn-info text-white me-2"><i class="bi bi-arrow-repeat me-1"></i> Update Data</button>
                    <a href="data_customer.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-1"></i> Batal</a>
                </form>
                
            </div>
        </div>
    </div> <?php include 'footer_partial.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>