<?php
// proses_penjualan.php - LOGIC SIMPAN TRANSAKSI & UPDATE STOK

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php");
    exit();
}

include 'koneksi.php';

if (isset($_POST['simpan_penjualan'])) {
    
    // Ambil data POST dan sanitasi
    $tanggal_jual = mysqli_real_escape_string($koneksi, $_POST['tgl_jual']);
    $id_customer = mysqli_real_escape_string($koneksi, $_POST['id_customer']);
    $id_mobil = mysqli_real_escape_string($koneksi, $_POST['id_mobil']);
    $harga_jual_per_unit = mysqli_real_escape_string($koneksi, $_POST['harga_jual_per_unit']);
    $jumlah_unit_jual = mysqli_real_escape_string($koneksi, $_POST['jumlah']); // Mengambil unit dari input 'jumlah'
    
    // Asumsi: Input ini ada di form penjualan.php lo
    $nama_pembeli = mysqli_real_escape_string($koneksi, $_POST['nama_pembeli'] ?? ''); 
    $kontak_pembeli = mysqli_real_escape_string($koneksi, $_POST['kontak_pembeli'] ?? ''); 

    // HITUNG TOTAL HARGA (PENTING AGAR TOTAL HARGA TERCATAT)
    $total_harga = $harga_jual_per_unit * $jumlah_unit_jual; 
    
    // Validasi dasar
    if (empty($id_customer) || empty($id_mobil) || $jumlah_unit_jual <= 0) {
        header("location:penjualan.php?pesan=gagal_validasi");
        exit();
    }
    
    // === START: CEK STOK SEBELUM INSERT ===
    $query_cek_stok = mysqli_query($koneksi, "SELECT stok FROM tabel_mobil WHERE id_mobil = '$id_mobil'");
    $data_stok = mysqli_fetch_assoc($query_cek_stok);
    $stok_tersedia = $data_stok['stok'] ?? 0;

    if ($jumlah_unit_jual > $stok_tersedia) {
        header("location:penjualan.php?pesan=stok_kurang");
        exit();
    }
    // === END: CEK STOK SEBELUM INSERT ===


    // 1. Query INSERT ke tabel_penjualan
    // KITA TAMBAHKAN KOLOM TOTAL_HARGA, NAMA_PEMBELI, DAN KONTAK_PEMBELI
    $sql_insert = "INSERT INTO tabel_penjualan (
        tgl_jual, 
        id_customer, 
        id_mobil, 
        harga_jual_per_unit, 
        jumlah, 
        total_harga,
        nama_pembeli,
        kontak_pembeli
    ) VALUES (
        '$tanggal_jual', 
        '$id_customer', 
        '$id_mobil', 
        '$harga_jual_per_unit', 
        '$jumlah_unit_jual',
        '$total_harga',      
        '$nama_pembeli',     
        '$kontak_pembeli'    
    )";

    $insert_success = mysqli_query($koneksi, $sql_insert);

    if ($insert_success) {
        // 2. Query UPDATE (KURANGI) STOK di tabel_mobil
        $sql_update_stok = "UPDATE tabel_mobil 
                            SET stok = stok - '$jumlah_unit_jual' 
                            WHERE id_mobil = '$id_mobil'";
        
        $update_success = mysqli_query($koneksi, $sql_update_stok);

        if ($update_success) {
            header("location:penjualan.php?pesan=sukses");
            exit();
        } else {
            // Jika update stok gagal, kita bisa tambahkan rollback di sini, tapi untuk sementara, kita anggap gagal total
            header("location:penjualan.php?pesan=gagal_update_stok");
            exit();
        }
    } else {
        header("location:penjualan.php?pesan=gagal_insert_transaksi");
        exit();
    }
} else {
    header("location:penjualan.php");
    exit();
}
?>