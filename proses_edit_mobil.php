<?php
// proses_edit_mobil.php - LOGIC UPDATE DATA MOBIL

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php");
    exit();
}

include 'koneksi.php';

if (isset($_POST['update_mobil'])) {
    
    // 1. Ambil data POST
    $id_mobil = mysqli_real_escape_string($koneksi, $_POST['id_mobil']);
    $merk = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $model = mysqli_real_escape_string($koneksi, $_POST['model']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $warna = mysqli_real_escape_string($koneksi, $_POST['warna']);
    $harga_beli = mysqli_real_escape_string($koneksi, $_POST['harga_beli']);
    $harga_jual = mysqli_real_escape_string($koneksi, $_POST['harga_jual']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);

    // Variabel untuk menampung nama gambar
    $nama_gambar = '';

    // 2. Cek apakah ada file gambar baru yang diupload
    if ($_FILES['gambar']['name'] != "") {
        $target_dir = "gambar_mobil/";
        $timestamp = time(); // Tambahkan timestamp agar nama file unik
        $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_gambar_baru = $timestamp . '_' . $id_mobil . '.' . $file_extension;
        $target_file = $target_dir . $nama_gambar_baru;
        
        // Cek direktori, buat jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Cek apakah upload berhasil
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $nama_gambar = $nama_gambar_baru;
            
            // 3. Ambil nama gambar lama untuk dihapus
            $query_lama = mysqli_query($koneksi, "SELECT gambar FROM tabel_mobil WHERE id_mobil = '$id_mobil'");
            $data_lama = mysqli_fetch_assoc($query_lama);
            $gambar_lama = $data_lama['gambar'];

            // Hapus gambar lama (kecuali jika gambarnya kosong/default)
            if ($gambar_lama && file_exists($target_dir . $gambar_lama)) {
                unlink($target_dir . $gambar_lama);
            }

        } else {
            // Jika upload gagal, kasih notifikasi dan kembali
            header("location:edit_mobil.php?id=$id_mobil&pesan=gagal_upload");
            exit();
        }
    } else {
        // Jika tidak ada upload gambar baru, gunakan gambar lama
        $query_lama = mysqli_query($koneksi, "SELECT gambar FROM tabel_mobil WHERE id_mobil = '$id_mobil'");
        $data_lama = mysqli_fetch_assoc($query_lama);
        $nama_gambar = $data_lama['gambar']; // Pertahankan nama gambar lama
    }

    // 4. Susun Query UPDATE
    $query_update = "UPDATE tabel_mobil SET
        merk = '$merk',
        model = '$model',
        tahun = '$tahun',
        warna = '$warna',
        harga_beli = '$harga_beli',
        harga_jual = '$harga_jual',
        stok = '$stok',
        gambar = '$nama_gambar'
        WHERE id_mobil = '$id_mobil'";

    $update_success = mysqli_query($koneksi, $query_update);

    if ($update_success) {
        header("location:data_mobil.php?pesan=update_sukses");
        exit();
    } else {
        // Jika update database gagal
        header("location:edit_mobil.php?id=$id_mobil&pesan=update_gagal");
        exit();
    }

} else {
    // Jika diakses tanpa submit form
    header("location:data_mobil.php");
    exit();
}
?>