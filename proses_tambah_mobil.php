<?php
// proses_tambah_mobil.php - LOGIC UNTUK MENYIMPAN DATA MOBIL BARU

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php");
    exit();
}

include 'koneksi.php';

if (isset($_POST['simpan_mobil'])) {
    
    // 1. Ambil dan sanitasi data POST
    $merk = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $model = mysqli_real_escape_string($koneksi, $_POST['model']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $warna = mysqli_real_escape_string($koneksi, $_POST['warna']);
    $harga_beli = mysqli_real_escape_string($koneksi, $_POST['harga_beli']);
    $harga_jual = mysqli_real_escape_string($koneksi, $_POST['harga_jual']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);

    // Variabel untuk menampung nama gambar
    $nama_gambar = '';

    // 2. Proses File Upload
    if ($_FILES['gambar']['name'] != "") {
        $target_dir = "gambar_mobil/";
        
        // Cek direktori, buat jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate nama file unik
        $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        // Buat nama unik dari timestamp + merk + model
        $nama_file_tanpa_ext = str_replace(' ', '_', strtolower($merk . '_' . $model));
        $nama_gambar_baru = time() . '_' . $nama_file_tanpa_ext . '.' . $file_extension;
        
        $target_file = $target_dir . $nama_gambar_baru;

        // Cek apakah upload berhasil
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $nama_gambar = $nama_gambar_baru;
        } else {
            // Jika upload gagal, kasih notifikasi dan kembali ke form
            header("location:tambah_mobil.php?pesan=gagal_upload");
            exit();
        }
    }

    // 3. Susun Query INSERT
    $query_insert = "INSERT INTO tabel_mobil (
        merk, model, tahun, warna, harga_beli, harga_jual, stok, gambar
    ) VALUES (
        '$merk', '$model', '$tahun', '$warna', '$harga_beli', '$harga_jual', '$stok', '$nama_gambar'
    )";

    $insert_success = mysqli_query($koneksi, $query_insert);

    if ($insert_success) {
        header("location:data_mobil.php?pesan=tambah_sukses");
        exit();
    } else {
        // Jika insert database gagal
        header("location:data_mobil.php?pesan=tambah_gagal");
        exit();
    }

} else {
    // Jika diakses tanpa submit form
    header("location:data_mobil.php");
    exit();
}
?>