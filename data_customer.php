<?php
// data_customer.php

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
$username = $_SESSION['username'] ?? 'Admin'; 
include 'koneksi.php';

// Tidak ada fungsi format_rupiah karena ini data customer
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Customer | Showroom Central</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        /* === GAYA KONSISTEN DENGAN DASHBOARD === */
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
                    <a href="tambah_customer.php" class="btn btn-sm btn-success me-3"><i class="bi bi-plus-circle-fill me-1"></i> Tambah Customer Baru</a>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door-fill me-1"></i> Dashboard</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="dataMasterDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-database-fill me-1"></i> Data Master
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dataMasterDropdown">
                            <li><a class="dropdown-item" href="data_mobil.php">Data Mobil</a></li>
                            <li><a class="dropdown-item active" href="data_customer.php">Data Customer</a></li>
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
        <h2 class="fw-bolder text-dark mb-4"><i class="bi bi-person-badge-fill me-2 text-primary"></i> Data Customer</h2>
        
        <?php 
        // Logika untuk menampilkan pesan dari proses hapus/tambah/edit
        if (isset($_GET['pesan'])) {
            $pesan = $_GET['pesan'];
            $alert_class = '';
            $message = '';

            if ($pesan == "hapus_berhasil") {
                $alert_class = 'alert-success';
                $message = '✅ Data customer berhasil dihapus!';
            } else if ($pesan == "hapus_gagal") {
                $alert_class = 'alert-danger';
                $message = '❌ Data customer gagal dihapus.';
                // Pesan khusus jika gagal karena Foreign Key
                if (isset($_GET['error']) && $_GET['error'] == 'fk') {
                    $message = '⚠️ Data customer ini tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan. Hapus riwayat transaksi terkait terlebih dahulu.';
                }
            } else if ($pesan == "input_berhasil") {
                $alert_class = 'alert-success';
                $message = '✅ Data customer berhasil ditambahkan!';
            } else if ($pesan == "update_berhasil") {
                $alert_class = 'alert-info';
                $message = '✅ Data customer berhasil diperbarui!';
            }
            
            if (!empty($message)) {
        ?>
            <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show shadow-sm" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            }
        }
        ?>

        <div class="card shadow border-0">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fw-bold">Daftar Customer Showroom</h5>
            </div>
            <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="dataTableCustomer">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Customer</th>
                                <th>Alamat</th>
                                <th>Nomor Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Query untuk mengambil data customer
                            // ASUMSI: Lo punya kolom 'nama_customer', 'alamat', dan 'no_telp'
                            $data = mysqli_query($koneksi, "SELECT * FROM tabel_customer ORDER BY id_customer DESC");
                            
                            if (mysqli_num_rows($data) > 0) {
                                while ($d = mysqli_fetch_array($data)) {
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <a href="edit_customer.php?id=<?php echo $d['id_customer']; ?>" class="fw-bold text-primary text-decoration-none">
                                                <?php echo htmlspecialchars($d['nama_customer']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($d['alamat']); ?></td>
                                        <td><?php echo htmlspecialchars($d['no_telp'] ?? $d['no_telepon'] ?? 'N/A'); ?></td>
                                        <td>
                                            <a href="edit_customer.php?id=<?php echo $d['id_customer']; ?>" class="btn btn-sm btn-info text-white me-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                            <a href="hapus_customer.php?id=<?php echo $d['id_customer']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data customer ini?')"><i class="bi bi-trash-fill"></i> Hapus</a>
                                        </td>
                                    </tr>
                                <?php 
                                }
                            } else {
                                // Tampilkan pesan jika tidak ada data
                                echo '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data customer yang terdaftar.</td></tr>';
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
            // Inisialisasi DataTables
            $('#dataTableCustomer').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
                }
            });
        });
    </script>
</body>
</html>