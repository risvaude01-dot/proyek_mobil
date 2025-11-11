<?php
// proses_update_customer.php - LOGIC MEMPERBARUI DATA CUSTOMER

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php");
    exit();
}

include 'koneksi.php';

if (isset($_POST['update_customer'])) {
    // Ambil data dari form
    $id_customer = mysqli_real_escape_string($koneksi, $_POST['id_customer']);
    $nama_customer = mysqli_real_escape_string($koneksi, $_POST['nama_customer']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // Query UPDATE data
    $query_update = "
        UPDATE tabel_customer 
        SET 
            nama_customer = '$nama_customer', 
            alamat = '$alamat', 
            no_telp = '$no_telp', 
            email = '$email' 
        WHERE id_customer = '$id_customer'
    ";

    if (mysqli_query($koneksi, $query_update)) {
        // Sukses, redirect ke halaman data customer dengan pesan sukses
        header("location:data_customer.php?pesan=update_sukses");
        exit();
    } else {
        // Gagal, redirect dengan pesan error
        header("location:edit_customer.php?id=$id_customer&pesan=update_gagal&error=" . urlencode(mysqli_error($koneksi)));
        exit();
    }
} else {
    // Jika diakses tanpa melalui tombol submit
    header("location:data_customer.php");
    exit();
}
?>