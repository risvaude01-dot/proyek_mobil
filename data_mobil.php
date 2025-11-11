<?php
// data_mobil.php

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
$username = $_SESSION['username'] ?? 'Admin'; 
include 'koneksi.php';

// Fungsi untuk format Rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// ... (semua PHP dan Query lama lo di sini, dari baris 1 sampai sebelum DOCTYPE) ...

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mobil | Showroom Central</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        /* === GAYA KONSISTEN DENGAN INDEX.PHP === */
        body { 
            background-color: #f4f6f9; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
            padding-top: 70px; /* Jarak dari fixed navbar */
        }
        .navbar-brand, .nav-link { font-weight: 500; }
        
        /* Dropdown muncul saat hover (hanya di desktop) */
        @media (min-width: 992px) {
            .navbar-expand-lg .navbar-nav .dropdown:hover .dropdown-menu {
                display: block;
                animation: fadeIn 0.3s ease-in-out;
            }
        }
        
        /* Footer Styling */
        .footer-custom {
            background-color: #2c3e50; 
            color: #ecf0f1;
            padding: 40px 0;
            margin-top: auto;
        }
        .footer-custom a {
            color: #bdc3c7;
            text-decoration: none;
        }
        .footer-copy {
            background-color: #21303e;
            padding: 10px 0;
            color: #95a5a6;
        }

        /* Styling tambahan untuk gambar */
        .img-thumb {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #003366;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-car-front-fill me-2"></i> SHOWROOM CENTRAL
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <a href="tambah_mobil.php" class="btn btn-sm btn-success me-3"><i class="bi bi-plus-circle-fill me-1"></i> Tambah Data Mobil</a>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door-fill me-1"></i> Dashboard</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="dataMasterDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-database-fill me-1"></i> Data Master
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dataMasterDropdown">
                            <li><a class="dropdown-item active" href="data_mobil.php">Data Mobil</a></li>
                            <li><a class="dropdown-item" href="data_customer.php">Data Customer</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="transaksiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-cash-stack me-1"></i> Transaksi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="transaksiDropdown">
                            <li><a class="dropdown-item" href="data_penjualan.php">Riwayat Penjualan</a></li>
                            <li><a class="dropdown-item" href="penjualan.php">Catat Penjualan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan_penjualan.php"><i class="bi bi-printer-fill me-1"></i> Laporan</a>
                    </li>
                </ul>
                
                <div class="navbar-nav ms-auto">
                    <span class="navbar-text me-3 text-white small">Halo, **<?php echo htmlspecialchars($username); ?>**</span>
                    <a href="logout.php" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>

            </div>
        </div>
    </nav>
    
    <div class="container-fluid py-4"> 
        <h2 class="fw-bolder text-dark mb-4"><i class="bi bi-car-front-fill me-2 text-primary"></i> Data Unit Mobil</h2>
        
        <div class="card shadow border-0">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fw-bold">Daftar Stok Unit yang Tersedia</h5>
            </div>
            <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="dataTableMobil">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Gambar</th>
                                <th>Merk/Model</th>
                                <th>Tahun/Warna</th>
                                <th>Hrg. Beli</th>
                                <th>Hrg. Jual</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Asumsi query lo sudah benar untuk mengambil data
                            $data = mysqli_query($koneksi, "SELECT * FROM tabel_mobil ORDER BY id_mobil DESC");
                            while ($d = mysqli_fetch_array($data)) {
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <?php if (!empty($d['gambar'])): ?>
                                            <img src="gambar/<?php echo htmlspecialchars($d['gambar']); ?>" alt="Gambar Mobil" class="img-thumb">
                                        <?php else: ?>
                                            <span class="text-muted small">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_mobil.php?id=<?php echo $d['id_mobil']; ?>" class="fw-bold text-primary text-decoration-none">
                                            <?php echo htmlspecialchars($d['merk']); ?>
                                        </a>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($d['model']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($d['tahun']); ?> / <?php echo htmlspecialchars($d['warna']); ?></td>
                                    <td><?php echo format_rupiah($d['harga_beli']); ?></td>
                                    <td><?php echo format_rupiah($d['harga_jual']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php 
                                                if($d['stok'] <= 3 && $d['stok'] > 0) echo 'bg-warning text-dark';
                                                else if ($d['stok'] == 0) echo 'bg-danger';
                                                else echo 'bg-success';
                                            ?>">
                                            <?php echo htmlspecialchars($d['stok']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit_mobil.php?id=<?php echo $d['id_mobil']; ?>" class="btn btn-sm btn-info text-white me-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                        <a href="hapus_mobil.php?id=<?php echo $d['id_mobil']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="bi bi-trash-fill"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php 
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <footer class="footer-custom">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-bold"><i class="bi bi-car-front-fill me-2"></i> Showroom Central</h5>
                    <p class="small">Sistem manajemen yang efisien untuk operasional showroom. Fokus pada akurasi data dan kecepatan transaksi.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-bold">Informasi & Akses Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="small">Dashboard</a></li>
                        <li><a href="data_mobil.php" class="small">Inventaris Mobil</a></li>
                        <li><a href="data_customer.php" class="small">Daftar Customer</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-bold">Kontak Kami</h5>
                    <ul class="list-unstyled small">
                        <li><i class="bi bi-geo-alt-fill me-2"></i> Karawang, Jawa Barat (Kantor Pusat)</li>
                        <li><i class="bi bi-envelope-fill me-2"></i> support@showroomcentral.com</li>
                        <li><i class="bi bi-phone-fill me-2"></i> (0267) 1234567</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <div class="footer-copy text-center">
        <p class="mb-0 small">&copy; <?php echo date('Y'); ?> Showroom Management System. Dikelola oleh Tim IT Showroom Central.</p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <script src="https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#dataTableMobil').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/2.0.8/i18n/id.json" // Tambahkan bahasa Indonesia untuk DataTable
                }
            });
        });
    </script>
</body>
</html>