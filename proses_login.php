<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username_kotor = $_POST['username'] ?? '';
    $password_kotor = $_POST['password'] ?? '';
    
    $username = mysqli_real_escape_string($koneksi, $username_kotor);
    $password = $password_kotor; 
    
    // 1. Cek User di Database
    $query = "SELECT * FROM tabel_users WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_assoc($result);
        
        // Asumsi: Password disimpan di database dengan password_hash()
        if (password_verify($password, $user_data['password'])) { 
            
            // Password Cocok, buat session
            $_SESSION['status_login'] = "sudah_login";
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['nama_lengkap'] = $user_data['nama_lengkap'] ?? $user_data['username'];
            
            // Redirect ke halaman Dashboard
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
} else {
    header("location:login.php");
    exit();
}

mysqli_close($koneksi);
?>