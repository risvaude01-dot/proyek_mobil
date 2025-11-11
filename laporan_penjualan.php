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

// >>> TEMPLATE LOGIC UNTUK AMBIL DATA PENJUALAN (GANTI DENGAN LOGIC LO) <<<
/* $sql_penjualan = "SELECT * FROM tabel_penjualan ORDER BY tanggal_jual DESC";
$query_penjualan = mysqli_query($koneksi, $sql_penjualan);
*/
// >>> END: TEMPLATE LOGIC <<<

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan | Showroom</title>
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
        /* Styling untuk angka dan harga */
        .table th, .table td {
            vertical-align: middle;
        }
        .table tbody td:nth-child(5), /* Harga Jual */
        .table tbody td:nth-child(6) { /* Total Bayar */
            text-align: right;
            font-weight: 500;
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
        
        <h1 class="mb-4 text-dark fw-bold">Laporan Penjualan</h1>
        <p class="lead text-secondary mb-4">Daftar lengkap riwayat transaksi penjualan mobil.</p>

        <div class="card mb-4">
            <div class="card-body">
                <form class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="tanggal_mulai" class="form-label small">Dari Tanggal</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai">
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_akhir" class="form-label small">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_merk" class="form-label small">Filter Merk</label>
                        <select class="form-select" id="filter_merk" name="filter_merk">
                            <option value="">Semua Merk</option>
                            </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter Data</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-lg mb-5">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 text-dark fw-semibold">Riwayat Transaksi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">No.</th>
                                <th>Tanggal Jual</th>
                                <th>Merk & Model Mobil</th>
                                <th>Pembeli</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-end">Total Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ganti bagian ini dengan looping data penjualan dari $query_penjualan
                            $no = 1;
                            
                            // Contoh Data Dummy (HAPUS SETELAH LO MASUKKAN LOGIC LO)
                            $dummy_data = [
                                ['2025-10-20', 'Toyota Yaris 2024', 'Budi Santoso', 250000000, 250000000, 'Lunas'],
                                ['2025-10-15', 'Honda HRV 2023', 'Siti Aminah', 320000000, 320000000, 'Lunas'],
                                ['2025-10-01', 'Suzuki Ertiga 2022', 'Rahmat Jaya', 180000000, 150000000, 'DP/Cicilan'],
                            ];

                            foreach ($dummy_data as $data) {
                                $status_badge = ($data[5] == 'Lunas') ? 'bg-success' : 'bg-warning text-dark';
                                echo "<tr>";
                                echo "<td class='text-center'>" . $no++ . "</td>";
                                echo "<td>" . $data[0] . "</td>"; 
                                echo "<td>" . $data[1] . "</td>";
                                echo "<td>" . htmlspecialchars($data[2]) . "</td>";
                                echo "<td>Rp " . number_format($data[3], 0, ',', '.') . "</td>"; 
                                echo "<td>Rp " . number_format($data[4], 0, ',', '.') . "</td>";
                                echo "<td><span class='badge {$status_badge}'>{$data[5]}</span></td>";
                                echo "<td><button class='btn btn-sm btn-info text-white me-1'>Detail</button></td>";
                                echo "</tr>";
                            }
                            
                            if (empty($dummy_data)) { // Ganti dengan mysqli_num_rows($query_penjualan) == 0
                                echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada transaksi penjualan yang tercatat.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
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