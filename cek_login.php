<?php
// PASTIKAN TIDAK ADA SPASI, ENTER, ATAU KARAKTER LAIN SEBELUM <?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php ada

// Tangkap data dari form login
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

// Query untuk mencocokkan data admin
// Asumsi: Lo punya tabel 'admin' dengan kolom 'username' dan 'password'
$sql = "SELECT * FROM admin WHERE username='$username'";
$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);

// Cek apakah ada data user
if ($data) {
    // Cek kecocokan password
    // Catatan: Jika lo menggunakan HASHING (e.g., password_hash), ganti baris ini:
    // if (password_verify($password, $data['password'])) { ...
    if ($password == $data['password']) { // KODE INI BERJALAN JIKA PASSWORD DI DB MASIH POLOS (NON-HASHING)
        
        // Login Berhasil!
        $_SESSION['username'] = $username;
        $_SESSION['status_login'] = "sudah_login";

        // Arahkan ke halaman utama/dashboard
        header("location:index.php");
        exit();
        
    } else {
        // Password salah
        header("location:login.php?pesan=gagal");
        exit();
    }
} else {
    // Username tidak ditemukan
    header("location:login.php?pesan=gagal");
    exit();
}
?>