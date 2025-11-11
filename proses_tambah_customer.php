<?php
// proses_tambah_customer.php - Menyimpan data customer baru ke database

session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}

include 'koneksi.php';

// 1. Ambil data dari form (Pastikan nama 'name' di form sama)
$nama_customer = $_POST['nama_customer'];
$alamat        = $_POST['alamat'];
// PERHATIAN: Gunakan nama variabel yang konsisten dengan nama input di form!
$no_telp       = $_POST['no_telp']; 
// $email         = $_POST['email'] ?? ''; // Jika lo menambahkan input email (optional)

// 2. Lakukan sanitasi data (PENTING untuk keamanan)
$nama_customer = mysqli_real_escape_string($koneksi, $nama_customer);
$alamat        = mysqli_real_escape_string($koneksi, $alamat);
$no_telp       = mysqli_real_escape_string($koneksi, $no_telp);
// $email         = mysqli_real_escape_string($koneksi, $email);

// 3. Query INSERT ke database
// PERHATIAN: Pastikan NAMA KOLOM di bawah ini SAMA PERSIS dengan di tabel_customer lo!
// Gue gunakan 'no_telp' dan kolom wajib lainnya.
$query = "INSERT INTO tabel_customer (nama_customer, alamat, no_telp) 
          VALUES ('$nama_customer', '$alamat', '$no_telp')";

if (mysqli_query($koneksi, $query)) {
    // 4. Jika INSERT berhasil, redirect ke data_customer.php dengan pesan sukses
    header("location:data_customer.php?pesan=input_berhasil");
} else {
    // 5. Jika gagal (misalnya karena koneksi atau error query), redirect ke form dengan pesan gagal
    // Untuk debugging, bisa tampilkan mysqli_error($koneksi)
    header("location:tambah_customer.php?pesan=input_gagal");
}

// Hentikan eksekusi skrip setelah redirect
exit();
?>