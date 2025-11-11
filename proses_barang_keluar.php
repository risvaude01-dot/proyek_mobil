<?php
// START: Proteksi Session
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
// END: Proteksi Session

// Tampilkan error saat debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. INCLUDE KONEKSI
include 'koneksi.php';

// Cek apakah ada data yang dikirimkan melalui POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. AMBIL DAN AMANKAN DATA DARI FORM
    $id_mobil_harga_kotor = $_POST['pilih_mobil'] ?? ''; 
    $jumlah_jual_kotor    = $_POST['jumlah_dijual'] ?? '';
    $nama_pembeli_kotor   = $_POST['nama_pembeli'] ?? '';
    $kontak_pembeli_kotor = $_POST['kontak_pembeli'] ?? '';

    // Cleaning data
    $id_mobil_harga = mysqli_real_escape_string($koneksi, $id_mobil_harga_kotor);
    $jumlah_jual    = mysqli_real_escape_string($koneksi, $jumlah_jual_kotor);
    $nama_pembeli   = mysqli_real_escape_string($koneksi, $nama_pembeli_kotor);
    $kontak_pembeli = mysqli_real_escape_string($koneksi, $kontak_pembeli_kotor);

    // PISAHKAN ID MOBIL dan HARGA SATUAN
    if (strpos($id_mobil_harga, '|') !== false) {
        list($id_mobil, $harga_satuan) = explode('|', $id_mobil_harga);
    } else {
        $id_mobil = '';
        $harga_satuan = 0;
    }

    // Konversi ke tipe data numerik yang aman
    $id_mobil = trim($id_mobil); 
    $jumlah_jual = intval($jumlah_jual); 
    $harga_satuan = floatval($harga_satuan); 

    // Set tanggal hari ini.
    $tanggal_jual = date("Y-m-d");

    // HITUNG TOTAL HARGA
    $total_harga = $harga_satuan * $jumlah_jual; 

    // Cek apakah data penting ada
    if ($jumlah_jual <= 0 || empty($id_mobil) || empty($nama_pembeli) || $harga_satuan <= 0) {
        header("location:barang_keluar.php?pesan=gagal&info=datakosong");
        exit();
    }
    
    // 3. PROSES PENJUALAN (INSERT DATA)
    $query_insert = "INSERT INTO tabel_penjualan (
                        id_mobil, 
                        tgl_jual,       
                        nama_pembeli, 
                        kontak_pembeli, 
                        jumlah,         
                        total_harga     
                     ) VALUES (
                        '$id_mobil', 
                        '$tanggal_jual', 
                        '$nama_pembeli', 
                        '$kontak_pembeli', 
                        '$jumlah_jual', 
                        '$total_harga' 
                     )";

    if (mysqli_query($koneksi, $query_insert)) {
        
        // 4. KURANGI STOK MOBIL (UPDATE DATA)
        $query_update_stok = "UPDATE tabel_mobil 
                              SET stok = stok - '$jumlah_jual' 
                              WHERE id_mobil = '$id_mobil'";
        
        if (mysqli_query($koneksi, $query_update_stok)) {
            // SEMUA SUKSES: Alihkan ke halaman report
            header("location:penjualan.php?pesan=sukses");
            exit();
        } else {
            // GAGAL UPDATE STOK
            echo "Error saat mengurangi stok: " . mysqli_error($koneksi); 
        }
    } else {
        // GAGAL INSERT PENJUALAN
        echo "Error saat mencatat penjualan: " . mysqli_error($koneksi); 
    }

} else {
    header("location:barang_keluar.php");
    exit();
}

mysqli_close($koneksi);
?>