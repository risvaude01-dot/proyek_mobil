<?php
// index.php - DASHBOARD UTAMA (REVISI FINAL)

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

// 1. Total Jenis Mobil
$query_jenis = mysqli_query($koneksi, "SELECT COUNT(id_mobil) AS total FROM tabel_mobil"); 
$data_jenis = mysqli_fetch_assoc($query_jenis);
$total_jenis_mobil = $data_jenis['total'] ?? 0;

// 2. Total Unit Tersedia (SUM kolom stok)
$query_stok = mysqli_query($koneksi, "SELECT SUM(stok) AS total FROM tabel_mobil");
$data_stok = mysqli_fetch_assoc($query_stok);
$total_unit_tersedia = $data_stok['total'] ?? 0;

// Tentukan bulan dan tahun saat ini
$bulan_ini = date('Y-m');

// 3. Total Unit Terjual Bulan Ini
$query_terjual = mysqli_query($koneksi, "
    SELECT SUM(jumlah) AS total 
    FROM tabel_penjualan 
    WHERE DATE_FORMAT(tgl_jual, '%Y-%m') = '$bulan_ini'
");
$data_terjual = mysqli_fetch_assoc($query_terjual);
$mobil_terjual_bulan_ini = $data_terjual['total'] ?? 0;

// 4. Total Nilai Jual Bulan Ini
$query_nilai_jual = mysqli_query($koneksi, "
    SELECT SUM(total_harga) AS total 
    FROM tabel_penjualan 
    WHERE DATE_FORMAT(tgl_jual, '%Y-%m') = '$bulan_ini'
");
$data_nilai_jual = mysqli_fetch_assoc($query_nilai_jual);
$total_nilai_jual_bulan_ini = $data_nilai_jual['total'] ?? 0;


// --- 5. DATA GRAFIK PENJUALAN BULANAN (LAST 12 MONTHS) ---
$data_penjualan_bulanan = [];
$labels_bulan = [];
for ($i = 11; $i >= 0; $i--) {
    $bulan_lalu = date('Y-m', strtotime("-$i month"));
    $nama_bulan = date('M Y', strtotime("-$i month")); 
    $query_graph = mysqli_query($koneksi, "
        SELECT SUM(jumlah) AS total_unit 
        FROM tabel_penjualan 
        WHERE DATE_FORMAT(tgl_jual, '%Y-%m') = '$bulan_lalu'
    ");
    $data_graph = mysqli_fetch_assoc($query_graph);
    $total_unit_terjual = $data_graph['total_unit'] ?? 0;
    $data_penjualan_bulanan[] = (int)$total_unit_terjual; 
    $labels_bulan[] = $nama_bulan;
}
$json_labels = json_encode($labels_bulan);
$json_data = json_encode($data_penjualan_bulanan);

// --- 6. DATA WIDGET: STOK RENDAH ---
// Batas stok rendah <= 3 unit
$query_stok_rendah = mysqli_query($koneksi, "
    SELECT merk, model, stok 
    FROM tabel_mobil 
    WHERE stok <= 3 AND stok > 0 
    ORDER BY stok ASC 
    LIMIT 5
");

// --- 7. DATA WIDGET: TRANSAKSI TERBARU ---
$query_transaksi_terbaru = mysqli_query($koneksi, "
    SELECT 
        p.tgl_jual, 
        COALESCE(c.nama_customer, p.nama_pembeli) AS nama_pembeli_display, 
        m.model,
        p.jumlah
    FROM tabel_penjualan p
    LEFT JOIN tabel_customer c ON p.id_customer = c.id_customer
    JOIN tabel_mobil m ON p.id_mobil = m.id_mobil
    ORDER BY p.tgl_jual DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Showroom Central</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* === GAYA BARU (NAVBAR ATAS & MODERN) === */
        body { 
            background-color: #f4f6f9; /* Background lebih cerah */
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
            padding-top: 70px; /* Jarak dari fixed navbar */
        }
        .navbar-brand, .nav-link { font-weight: 500; }
        
        /* Card Statistik Lebih Sleek */
        .card-stat { 
            border: none;
            border-radius: 12px; 
            transition: transform 0.3s, box-shadow 0.4s;
            overflow: hidden;
            position: relative;
        }
        .card-stat::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-bottom-left-radius: 100%;
        }
        .card-stat:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; 
        }
        .card-stat .icon-box { 
            font-size: 3rem; 
            opacity: 0.6;
            margin-left: 10px;
        }
        .card-stat h2 {
            font-size: 2.2rem;
            margin-bottom: 0;
        }
        
        /* Dropdown muncul saat hover (hanya di desktop) */
        @media (min-width: 992px) {
            .navbar-expand-lg .navbar-nav .dropdown:hover .dropdown-menu {
                display: block;
                animation: fadeIn 0.3s ease-in-out;
            }
        }
        
        /* Footer Styling */
        .footer-custom {
            background-color: #2c3e50; /* Warna biru gelap */
            color: #ecf0f1;
            padding: 40px 0;
            margin-top: auto;
        }
        .footer-custom a {
            color: #bdc3c7;
            text-decoration: none;
        }
        .footer-custom a:hover {
            color: #ffffff;
            text-decoration: underline;
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
                <i class="bi bi-car-front-fill me-2"></i> SHOWROOM CENTRAL KARAWANG
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php"><i class="bi bi-house-door-fill me-1"></i> Menu</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dataMasterDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-database-fill me-1"></i> Data Master
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dataMasterDropdown">
                            <li><a class="dropdown-item" href="data_mobil.php">Data Mobil</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bolder text-dark"><i class="bi bi-speedometer2 me-2 text-primary"></i> Ringkasan Operasional</h1>
            <small class="text-muted">Akses Terakhir: <?php echo date('d M Y, H:i'); ?></small>
        </div>

        <div class="row g-4">
            
            <div class="col-lg-3 col-md-6">
                <div class="card card-stat text-white bg-primary shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-uppercase small">Total Jenis Mobil</p>
                            <h2 class="fw-bold"><?php echo number_format($total_jenis_mobil); ?></h2>
                        </div>
                        <div class="icon-box"><i class="bi bi-gear-fill"></i></div>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-end p-2"><a href="data_mobil.php" class="text-white small">Lihat Detail <i class="bi bi-arrow-right-circle"></i></a></div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card card-stat text-white bg-info shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-uppercase small">Unit Tersedia (Stok)</p>
                            <h2 class="fw-bold"><?php echo number_format($total_unit_tersedia); ?></h2>
                        </div>
                        <div class="icon-box"><i class="bi bi-car-front-fill"></i></div>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-end p-2"><a href="data_mobil.php" class="text-white small">Lihat Detail <i class="bi bi-arrow-right-circle"></i></a></div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card card-stat text-white bg-success shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-uppercase small">Terjual Bulan Ini</p>
                            <h2 class="fw-bold"><?php echo number_format($mobil_terjual_bulan_ini); ?> Unit</h2>
                        </div>
                        <div class="icon-box"><i class="bi bi-truck"></i></div>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-end p-2"><a href="data_penjualan.php" class="text-white small">Lihat Transaksi <i class="bi bi-arrow-right-circle"></i></a></div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card card-stat text-white bg-danger shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-uppercase small">Total Nilai Jual (Bulan Ini)</p>
                            <h4 class="fw-bold"><?php echo format_rupiah($total_nilai_jual_bulan_ini); ?></h4>
                        </div>
                        <div class="icon-box"><i class="bi bi-wallet2"></i></div>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-end p-2"><a href="data_penjualan.php" class="text-white small">Lihat Transaksi <i class="bi bi-arrow-right-circle"></i></a></div>
                </div>
            </div>
            
        </div>
        
        <div class="row g-4 mt-4">
            
            <div class="col-lg-6">
                <div class="card shadow border-0 h-100">
                    <div class="card-header bg-warning text-dark fw-bold border-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Peringatan Stok Rendah (< 4 Unit)
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php if (mysqli_num_rows($query_stok_rendah) > 0): ?>
                                <?php while ($data_stok = mysqli_fetch_assoc($query_stok_rendah)): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><?php echo htmlspecialchars($data_stok['merk'] . ' ' . $data_stok['model']); ?></strong>
                                        <span class="badge bg-danger rounded-pill"><?php echo $data_stok['stok']; ?> Unit</span>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="list-group-item text-success text-center">
                                    <i class="bi bi-check-circle-fill me-1"></i> Semua stok aman!
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer text-end small bg-white border-0">
                        <a href="data_mobil.php" class="text-primary fw-bold">Kelola Stok <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card shadow border-0 h-100">
                    <div class="card-header bg-dark text-white fw-bold border-0">
                        <i class="bi bi-clock-history me-2"></i> 5 Transaksi Penjualan Terbaru
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php if (mysqli_num_rows($query_transaksi_terbaru) > 0): ?>
                                <?php while ($data_transaksi = mysqli_fetch_assoc($query_transaksi_terbaru)): ?>
                                    <li class="list-group-item">
                                        <span class="badge bg-primary me-2"><?php echo date('d/m', strtotime($data_transaksi['tgl_jual'])); ?></span>
                                        **<?php echo htmlspecialchars($data_transaksi['nama_pembeli_display']); ?>** membeli 
                                        <span class="text-info fw-bold"><?php echo $data_transaksi['jumlah']; ?> Unit <?php echo htmlspecialchars($data_transaksi['model']); ?></span>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                 <li class="list-group-item text-muted text-center">
                                     Belum ada transaksi penjualan yang tercatat.
                                 </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer text-end small bg-white border-0">
                        <a href="data_penjualan.php" class="text-info fw-bold">Lihat Semua Riwayat <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>


        <hr class="my-5">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-secondary text-white border-0">
                        <h5 class="card-title mb-0"><i class="bi bi-graph-up me-2"></i> Grafik Penjualan Bulanan (Total Unit)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="grafikPenjualan" height="100"></canvas> 
                    </div>
                </div>
            </div>
        </div>
    </div> <footer class="footer-custom">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-bold"><i class="bi bi-car-front-fill me-2"></i> Showroom Central Karawang</h5>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script> 
    
    <script>
        // Ambil data dari PHP yang sudah di-encode ke JSON
        const labels = <?php echo $json_labels; ?>;
        const dataPenjualan = <?php echo $json_data; ?>;

        const ctx = document.getElementById('grafikPenjualan').getContext('2d');

        new Chart(ctx, {
            type: 'bar', // Jenis grafik: Bar (batang)
            data: {
                labels: labels, // Label Bulan dari PHP
                datasets: [{
                    label: 'Unit Terjual',
                    data: dataPenjualan, // Data Unit Terjual dari PHP
                    backgroundColor: 'rgba(0, 123, 255, 0.8)', // Biru
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Unit'
                        },
                        ticks: {
                            callback: function(value, index, ticks) {
                                if (Math.floor(value) === value) {
                                    return value;
                                }
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>