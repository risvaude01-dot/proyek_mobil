<?php
// START: Proteksi Session
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
// END: Proteksi Session

// Tampilkan error saat debugging (opsional, hapus saat sudah production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil dan Amankan Data POST
    $id_mobil_lama  = $_POST['id_mobil'] ?? '';
    $merk           = mysqli_real_escape_string($koneksi, $_POST['merk'] ?? '');
    $model          = mysqli_real_escape_string($koneksi, $_POST['model'] ?? '');
    
    // Sanitasi numerik untuk harga
    $harga_beli     = filter_var($_POST['harga_beli'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $harga_jual     = filter_var($_POST['harga_jual'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $jumlah_masuk   = intval($_POST['jumlah_masuk'] ?? 0);
    $tanggal_beli   = mysqli_real_escape_string($koneksi, $_POST['tanggal_beli'] ?? date("Y-m-d"));

    // 2. Ambil Data File
    $nama_file      = $_FILES['gambar']['name'] ?? '';
    $file_tmp       = $_FILES['gambar']['tmp_name'] ?? '';
    $lokasi_simpan  = "img_mobil/" . $nama_file; 
    
    $id_mobil_baru = ''; 
    $gambar_query = ''; 

    // Cek data wajib
    if ($jumlah_masuk <= 0 || empty($tanggal_beli)) {
        header("location:barang_masuk.php?pesan=gagal&info=jumlahkosong");
        exit();
    }

    // 3. Logic Upload Gambar
    if (!empty($nama_file)) {
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_valid = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($ekstensi, $ekstensi_valid)) {
            if (move_uploaded_file($file_tmp, $lokasi_simpan)) {
                $gambar_query = ", gambar = '$nama_file'";
            } else {
                 header("location:barang_masuk.php?pesan=gagal&info=uploadgagal");
                 exit();
            }
        } else {
            // Ekstensi tidak valid, tapi tetap lanjutkan transaksi tanpa gambar
            $nama_file = ''; // Kosongkan nama file agar tidak masuk ke DB
        }
    }

    // 4. Proses: Tambah Mobil Baru (Jika ID mobil lama KOSONG)
    if (empty($id_mobil_lama) && !empty($merk) && !empty($model)) {
        
        if (empty($harga_beli) || empty($harga_jual)) {
            header("location:barang_masuk.php?pesan=gagal&info=hargakosong");
            exit();
        }

        // Query INSERT mobil baru
        // Kita menggunakan $nama_file karena ini mobil baru
        $query_insert_mobil = "INSERT INTO tabel_mobil (merk, model, harga_beli, harga_jual, stok, gambar) 
                               VALUES ('$merk', '$model', '$harga_beli', '$harga_jual', '$jumlah_masuk', '$nama_file')";
        
        if (mysqli_query($koneksi, $query_insert_mobil)) {
            $id_mobil_baru = mysqli_insert_id($koneksi); 
        } else {
            echo "Error saat INSERT mobil baru: " . mysqli_error($koneksi); 
            exit();
        }

    } else if (!empty($id_mobil_lama)) {
        // 5. Proses: Update Stok Mobil Lama
        $id_mobil_baru = $id_mobil_lama;
        
        // Ambil harga beli mobil lama dari DB untuk perhitungan total_harga_beli
        $q_data_lama = mysqli_query($koneksi, "SELECT harga_beli FROM tabel_mobil WHERE id_mobil = '$id_mobil_lama'");
        if ($q_data_lama && mysqli_num_rows($q_data_lama) > 0) {
            $data_lama = mysqli_fetch_assoc($q_data_lama);
            $harga_beli = $data_lama['harga_beli'];
        } else {
            header("location:barang_masuk.php?pesan=gagal&info=mobilnotfound");
            exit();
        }

        // Query UPDATE stok mobil lama (termasuk update gambar jika ada upload baru)
        $query_update_stok = "UPDATE tabel_mobil 
                              SET stok = stok + '$jumlah_masuk' $gambar_query
                              WHERE id_mobil = '$id_mobil_lama'"; 
        
        if (!mysqli_query($koneksi, $query_update_stok)) {
            echo "Error saat UPDATE stok mobil lama: " . mysqli_error($koneksi); 
            exit();
        }

    } else {
        // Data mobil tidak lengkap
        header("location:barang_masuk.php?pesan=gagal&info=datamobilkosong");
        exit();
    }

    // 6. Proses: Insert ke tabel_pembelian (Wajib untuk semua skenario)
    
    // Hitung Total Harga Beli
    $total_harga_beli = $harga_beli * $jumlah_masuk; 

    $query_insert_pembelian = "INSERT INTO tabel_pembelian (
                                id_mobil, 
                                tgl_beli,       
                                jumlah,         
                                total_harga_beli     
                             ) VALUES (
                                '$id_mobil_baru', 
                                '$tanggal_beli', 
                                '$jumlah_masuk', 
                                '$total_harga_beli' 
                             )";
    
    if (mysqli_query($koneksi, $query_insert_pembelian)) {
        // SEMUA SUKSES
        header("location:data_mobil.php?pesan=sukses");
        exit();
    } else {
        echo "Error saat mencatat pembelian: " . mysqli_error($koneksi); 
    }

} else {
    header("location:barang_masuk.php");
    exit();
}

mysqli_close($koneksi);
?>