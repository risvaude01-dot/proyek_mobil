<?php
// PASTIKAN TIDAK ADA SPASI, ENTER, ATAU KARAKTER LAIN SEBELUM <?php
// START: Proteksi Session
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}

$username = $_SESSION['username'] ?? 'Admin'; 

include 'koneksi.php';

// >>> TEMPLATE LOGIC UNTUK AMBIL DATA KEUANGAN (GANTI DENGAN LOGIC LO) <<<
// Lo bisa ambil data total dari transaksi, HPP (Harga Pokok Penjualan), dll.
$total_modal = 500000000;
$total_penjualan_bruto = 750000000;
$laba_kotor = $total_penjualan_bruto - $total_modal;
// >>> END: TEMPLATE LOGIC <<<

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan | Showroom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS Konsisten dengan Dashboard */
        body {
            background-color: #e9ecef; 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1; 
            padding-top: 30px;
            padding-bottom: 30px;
        }
        .navbar-custom {
            background: linear-gradient(90deg, #003366 0%, #004080 100%) !important; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }
        /* Styling untuk Summary Cards */
        .summary-card-value {
            font-size: 2rem;
            font-weight: bold;
            text-align: right;
        }
        .footer {
            background-color: #343a40; 
            color: white;
            padding: 1rem 0;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-lg">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-car-front-fill me-2"></i> SHOWROOM CENTRAL</a>
            <div class="d-flex">
                 <span class="navbar-text me-3 text-white small">
                    Halo, **<?php echo htmlspecialchars($username); ?>**
                 </span>
                 <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>
    <div class="container main-content">
        <a href="index.php" class="btn btn-outline-secondary mb-4"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
        
        <h1 class="mb-4 text-dark fw-bold">Ringkasan Keuangan</h1>
        <p class="lead text-secondary mb-5">Analisis cepat laba/rugi dan total aset.</p>

        <div class="row g-4 mb-5">
            
            <div class="col-lg-4 col-md-6">
                <div class="card bg-info text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-white-75 small">TOTAL HARGA POKOK (MODAL)</h5>
                        <div class="summary-card-value">
                            Rp <?php echo number_format($total_modal, 0, ',', '.'); ?>
                        </div>
                        <i class="bi bi-gear-fill position-absolute end-0 bottom-0 me-3 mb-2" style="opacity: 0.3; font-size: 3rem;"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-white-75 small">TOTAL NILAI JUAL (BRUTO)</h5>
                        <div class="summary-card-value">
                            Rp <?php echo number_format($total_penjualan_bruto, 0, ',', '.'); ?>
                        </div>
                        <i class="bi bi-cash-stack position-absolute end-0 bottom-0 me-3 mb-2" style="opacity: 0.3; font-size: 3rem;"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-white-75 small">ESTIMASI LABA KOTOR</h5>
                        <div class="summary-card-value">
                            Rp <?php echo number_format($laba_kotor, 0, ',', '.'); ?>
                        </div>
                        <i class="bi bi-graph-up-arrow position-absolute end-0 bottom-0 me-3 mb-2" style="opacity: 0.3; font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-lg">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 text-dark fw-semibold">Laporan Rugi/Laba Detail</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Di sini lo bisa tambahkan tabel atau grafik yang menampilkan detail pemasukan dan pengeluaran operasional (biaya gaji, listrik, dll.) untuk mendapatkan laba bersih.</p>
                <button class="btn btn-outline-primary"><i class="bi bi-file-earmark-spreadsheet"></i> Export Laporan (XLSX)</button>
            </div>
        </div>
        
    </div>
    <footer class="footer bg-dark">
        <div class="container">
            <span class="text-white-50 small">
                &copy; <?php echo date('Y'); ?> Showroom Management System. Crafted for Admin.
            </span>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>