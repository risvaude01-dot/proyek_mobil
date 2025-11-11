<?php
// penjualan.php - FORM PENCATATAN TRANSAKSI PENJUALAN

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php");
    exit();
}
$username = $_SESSION['username'] ?? 'Admin'; 
include 'koneksi.php';

// Fungsi untuk format Rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// 1. Query untuk mengambil data mobil yang stoknya > 0
$query_mobil = mysqli_query($koneksi, "
    SELECT 
        id_mobil, 
        CONCAT(merk, ' ', model, ' (', tahun, ')') AS nama_mobil_full, 
        harga_jual,
        stok
    FROM 
        tabel_mobil 
    WHERE 
        stok > 0 
    ORDER BY merk, model
");

// 2. Query untuk mengambil data customer
$query_customer = mysqli_query($koneksi, "SELECT id_customer, nama_customer FROM tabel_customer ORDER BY nama_customer ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Penjualan | Showroom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar-custom { background: linear-gradient(90deg, #003366 0%, #004080 100%) !important; }
        .card { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
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
    
    <div class="container mt-5">
        <a href="data_penjualan.php" class="btn btn-outline-secondary mb-4"><i class="bi bi-arrow-left"></i> Kembali ke Riwayat Transaksi</a>
        
        <h1 class="mb-4 text-dark fw-bold"><i class="bi bi-tags-fill me-2"></i> Catat Transaksi Penjualan</h1>

        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "stok_kurang"){
                echo "<div class='alert alert-danger text-center'><i class='bi bi-x-octagon-fill'></i> **Gagal!** Jumlah unit melebihi stok yang tersedia.</div>";
            } else if($_GET['pesan'] == "gagal_validasi"){
                 echo "<div class='alert alert-warning text-center'><i class='bi bi-exclamation-triangle-fill'></i> **Perhatian!** Data Unit atau Customer tidak boleh kosong.</div>";
            } else if($_GET['pesan'] == "sukses"){
                 echo "<div class='alert alert-success text-center'><i class='bi bi-check-circle-fill'></i> Transaksi berhasil dicatat dan stok sudah dikurangi.</div>";
            }
        }
        ?>
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Form Penjualan Unit Mobil</h5>
            </div>
            <div class="card-body">
                <form action="proses_penjualan.php" method="POST">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tgl_jual" class="form-label">Tanggal Jual</label>
                            <input type="date" class="form-control" id="tgl_jual" name="tgl_jual" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_mobil" class="form-label">Unit Mobil (Stok: <span id="stok-info" class="fw-bold text-primary">--</span>)</label>
                            <select class="form-select" id="id_mobil" name="id_mobil" required>
                                <option value="">Pilih Unit Mobil</option>
                                <?php 
                                mysqli_data_seek($query_mobil, 0); // Reset pointer
                                while($data_mobil = mysqli_fetch_assoc($query_mobil)): 
                                ?>
                                    <option value="<?php echo $data_mobil['id_mobil']; ?>" 
                                            data-harga="<?php echo $data_mobil['harga_jual']; ?>"
                                            data-stok="<?php echo $data_mobil['stok']; ?>">
                                        <?php echo htmlspecialchars($data_mobil['nama_mobil_full']); ?> 
                                        (<?php echo format_rupiah($data_mobil['harga_jual']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_customer" class="form-label">Pilih Customer</label>
                            <select class="form-select" id="id_customer" name="id_customer" required>
                                <option value="">Pilih Customer Terdaftar</option>
                                <option value="0" class="fw-bold text-danger">-- CUSTOMER BARU (WALK-IN) --</option>
                                <?php while($data_customer = mysqli_fetch_assoc($query_customer)): ?>
                                    <option value="<?php echo $data_customer['id_customer']; ?>">
                                        <?php echo htmlspecialchars($data_customer['nama_customer']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text">Pilih 'CUSTOMER BARU' jika pembeli belum terdaftar.</div>
                        </div>
                        
                        <div class="col-md-6" id="input-pembeli-manual">
                            <div class="mb-3">
                                <label for="nama_pembeli" class="form-label">Nama Pembeli Manual</label>
                                <input type="text" class="form-control" id="nama_pembeli" name="nama_pembeli" placeholder="Isi jika customer baru/walk-in">
                            </div>
                            <div class="mb-3">
                                <label for="kontak_pembeli" class="form-label">Kontak Pembeli</label>
                                <input type="text" class="form-control" id="kontak_pembeli" name="kontak_pembeli" placeholder="Nomor telepon/kontak">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="harga_jual_per_unit" class="form-label">Harga Jual per Unit</label>
                            <input type="number" class="form-control text-end" id="harga_jual_per_unit" name="harga_jual_per_unit" readonly required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="jumlah" class="form-label">Jumlah Unit</label>
                            <input type="number" class="form-control text-end" id="jumlah" name="jumlah" value="1" min="1" required>
                            <div class="form-text text-danger" id="stok-warning"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="total_harga" class="form-label">Total Harga Jual</label>
                            <input type="text" class="form-control text-end fw-bold bg-light" id="total_harga_display" readonly>
                            <input type="hidden" id="total_harga" name="total_harga"> 
                        </div>
                    </div>

                    <button type="submit" name="simpan_penjualan" class="btn btn-success me-2 mt-3"><i class="bi bi-check-circle-fill me-1"></i> Catat Transaksi & Kurangi Stok</button>
                    <a href="data_penjualan.php" class="btn btn-outline-danger mt-3">Batal</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobilSelect = document.getElementById('id_mobil');
            const jumlahInput = document.getElementById('jumlah');
            const hargaJualInput = document.getElementById('harga_jual_per_unit');
            const totalHargaDisplay = document.getElementById('total_harga_display');
            const totalHargaHidden = document.getElementById('total_harga');
            const stokInfo = document.getElementById('stok-info');
            const stokWarning = document.getElementById('stok-warning');
            const customerSelect = document.getElementById('id_customer');
            const inputPembeliManual = document.getElementById('input-pembeli-manual');
            const namaPembeli = document.getElementById('nama_pembeli');
            const kontakPembeli = document.getElementById('kontak_pembeli');
            
            let maxStok = 0;

            // Fungsi untuk format angka ke Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + Math.floor(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Fungsi untuk menghitung total harga
            function hitungTotal() {
                const harga = parseFloat(hargaJualInput.value) || 0;
                let jumlah = parseInt(jumlahInput.value) || 0;
                
                stokWarning.textContent = '';
                
                // Cek Stok
                if (jumlah > maxStok && maxStok > 0) {
                    stokWarning.textContent = `Peringatan: Jumlah melebihi stok yang tersedia (${maxStok} unit)!`;
                    // Opsional: Batasi input ke maxStok
                    jumlah = maxStok;
                    jumlahInput.value = maxStok;
                } else if (maxStok == 0 && mobilSelect.value != "") {
                    stokWarning.textContent = `STOK HABIS! Pilih unit lain.`;
                    jumlahInput.value = 0;
                    jumlah = 0;
                }

                const total = harga * jumlah;
                totalHargaDisplay.value = formatRupiah(total);
                totalHargaHidden.value = total; // Isi input hidden untuk proses PHP
            }

            // Event saat mobil dipilih
            mobilSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const harga = selectedOption.getAttribute('data-harga');
                const stok = selectedOption.getAttribute('data-stok');
                
                if (harga) {
                    hargaJualInput.value = harga;
                    maxStok = parseInt(stok) || 0;
                    stokInfo.textContent = maxStok;
                    jumlahInput.max = maxStok; // Set max attribute
                    jumlahInput.value = 1; // Reset jumlah ke 1
                } else {
                    hargaJualInput.value = '';
                    maxStok = 0;
                    stokInfo.textContent = '--';
                }
                hitungTotal();
            });

            // Event saat jumlah unit diubah
            jumlahInput.addEventListener('input', hitungTotal);

            // Event saat customer dipilih
            customerSelect.addEventListener('change', function() {
                // Tampilkan input manual jika ID 0 (Customer Baru) dipilih
                if (this.value === '0') {
                    inputPembeliManual.style.display = 'block';
                    namaPembeli.required = true;
                } else {
                    inputPembeliManual.style.display = 'none';
                    namaPembeli.required = false;
                    // Clear nilai manual saat customer terdaftar dipilih
                    namaPembeli.value = '';
                    kontakPembeli.value = '';
                }
            });

            // Inisialisasi tampilan (memastikan input manual tersembunyi/terlihat saat load)
            customerSelect.dispatchEvent(new Event('change'));

            // Inisialisasi hitungan total saat load
            mobilSelect.dispatchEvent(new Event('change'));
        });
    </script>
</body>
</html>