<?php
// data_penjualan.php - MENAMPILKAN SEMUA DATA TRANSAKSI PENJUALAN

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

// Query untuk mengambil data penjualan. MENGGUNAKAN LEFT JOIN untuk menangani ID CUSTOMER = 0.
$query_penjualan = "
    SELECT 
        p.id_penjualan,
        p.tgl_jual, 
        -- Gunakan nama_customer dari tabel c, jika NULL, gunakan nama_pembeli dari tabel p
        COALESCE(c.nama_customer, p.nama_pembeli) AS nama_pembeli_display, 
        CONCAT(m.merk, ' ', m.model, ' (', m.tahun, ')') AS nama_mobil_full, 
        p.harga_jual_per_unit,
        p.jumlah, 
        (p.harga_jual_per_unit * p.jumlah) AS total_penjualan 
    FROM 
        tabel_penjualan p
    LEFT JOIN 
        tabel_customer c ON p.id_customer = c.id_customer
    JOIN 
        tabel_mobil m ON p.id_mobil = m.id_mobil
    ORDER BY 
        p.tgl_jual DESC, p.id_penjualan DESC";

$result_penjualan = mysqli_query($koneksi, $query_penjualan);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan | Showroom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { background-color: #f8f9fa; display: flex; flex-direction: column; min-height: 100vh; }
        .main-content { flex: 1; padding-top: 30px; padding-bottom: 30px; }
        .navbar-custom { background: linear-gradient(90deg, #003366 0%, #004080 100%) !important; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .card { border: none; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); border-radius: 8px; }
        .footer { background-color: #343a40; color: white; padding: 1rem 0; text-align: center; margin-top: auto; }
        /* Style DataTables */
        table.dataTable thead th { background-color: #f1f1f1; border-bottom: 2px solid #dee2e6; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-lg">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-car-front-fill me-2"></i> SHOWROOM CENTRAL</a>
            <div class="d-flex">
                 <span class="navbar-text me-3 text-white small">Halo, **<?php echo htmlspecialchars($username); ?>**</span>
                 <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container main-content">
        <a href="index.php" class="btn btn-outline-secondary mb-4"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
        
        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "hapus_sukses"){
                echo "<div class='alert alert-success text-center'><i class='bi bi-check-circle-fill'></i> Transaksi berhasil dihapus dan **stok mobil sudah dikembalikan**!</div>";
            } else if($_GET['pesan'] == "hapus_gagal"){
                echo "<div class='alert alert-danger text-center'><i class='bi bi-x-octagon-fill'></i> **Penghapusan Gagal**! Silakan cek log error atau hubungi administrator.</div>";
            } else if($_GET['pesan'] == "id_tidak_ditemukan"){
                echo "<div class='alert alert-warning text-center'><i class='bi bi-info-circle-fill'></i> ID Transaksi tidak valid.</div>";
            } else if($_GET['pesan'] == "sukses"){
                 echo "<div class='alert alert-success text-center'><i class='bi bi-check-circle-fill'></i> Transaksi berhasil dicatat dan stok sudah dikurangi.</div>";
            }
        }
        ?>
        <h1 class="mb-4 text-dark fw-bold"><i class="bi bi-receipt-cutoff me-2"></i> Data Transaksi Penjualan</h1>

        <div class="d-flex justify-content-between mb-3">
             <a href="penjualan.php" class="btn btn-danger me-2"><i class="bi bi-plus-circle-fill me-1"></i> Catat Penjualan Baru</a>
             <a href="laporan_penjualan.php" class="btn btn-primary"><i class="bi bi-printer me-1"></i> Cetak Laporan</a>
        </div>


        <div class="card shadow-lg">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 text-primary fw-bold">Riwayat Transaksi Unit Mobil</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelPenjualan" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Unit Mobil</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Total Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if (mysqli_num_rows($result_penjualan) > 0) {
                                while ($data = mysqli_fetch_assoc($result_penjualan)) {
                                    $total_jual = $data['harga_jual_per_unit'] * $data['jumlah'];
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($data['tgl_jual'])); ?></td>
                                        <td><?php echo htmlspecialchars($data['nama_pembeli_display']); ?></td> 
                                        <td><?php echo htmlspecialchars($data['nama_mobil_full']); ?></td> 
                                        <td class="text-end"><?php echo format_rupiah($data['harga_jual_per_unit']); ?></td>
                                        <td class="text-end"><?php echo number_format($data['jumlah']); ?></td>
                                        <td class="text-end fw-bold text-success"><?php echo format_rupiah($total_jual); ?></td>
                                        <td>
                                            <a href="proses_hapus_penjualan.php?id=<?php echo $data['id_penjualan']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('APAKAH ANDA YAKIN INGIN MENGHAPUS TRANSAKSI INI? Stok mobil akan dikembalikan.');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="8" class="text-center">Belum ada data transaksi penjualan.</td></tr>';
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
                &copy; <?php echo date('Y'); ?> Showroom Management System.
            </span>
        </div>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#tabelPenjualan').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json" // Bahasa Indonesia
                
                },
                "order": [[ 0, "desc" ]] // Urutkan berdasarkan ID terbaru
            });
        });
    </script>
</body>
</html>