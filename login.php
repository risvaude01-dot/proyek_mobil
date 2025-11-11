<?php
// PASTIKAN TIDAK ADA SPASI, ENTER, ATAU KARAKTER LAIN SEBELUM <?php
session_start();

// Jika user sudah login, arahkan ke dashboard
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == "sudah_login") {
    header("location:index.php");
    exit();
}
// PENTING: Pastikan lo punya file 'cek_login.php' untuk memproses form ini.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Showroom Central</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* CSS Gabungan (Style LAMA lo + Background Image) */
        body {
            /* 1. BACKGROUND IMAGE */
            background: url('assets/bg_showroom.jpg') no-repeat center center fixed; 
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover; 
            
            /* 2. LAYOUT LAMA LO (Flexbox for full height) */
            display: flex;
            flex-direction: column; 
            min-height: 100vh;
        }
        
        /* Konten utama (di mana card login berada) */
        .main-content {
            flex: 1; 
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0; 
        }

        .login-card {
            /* 3. SEMI-TRANSPARENT BACKGROUND untuk Card */
            background-color: rgba(255, 255, 255, 0.9); 
            
            /* Style card LAMA lo: */
            max-width: 400px;
            width: 90%;
            padding: 30px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5); 
        }
        
        .form-control:focus {
            border-color: #007bff; 
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        
        .btn-primary {
            background-color: #003366; 
            border-color: #003366;
        }
        .btn-primary:hover {
            background-color: #002244;
            border-color: #002244;
        }
        
        /* Footer Styling */
        .footer {
            background-color: #343a40; 
            color: white;
            padding: 1rem 0;
            text-align: center;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-car-front-fill"></i> SHOWROOM CENTRAL KARAWANG</a>
            </div>
    </nav>
    <div class="main-content">
        <div class="login-card bg-white">
            
            <h3 class="text-center mb-4 fw-bold text-dark">
                <i class="bi bi-shield-lock-fill me-2 text-primary"></i> 
                ADMIN ACCESS
            </h3>
            <p class="text-center text-muted mb-4">Silakan login untuk melanjutkan</p>

            <?php 
            if(isset($_GET['pesan'])){
                if($_GET['pesan'] == "gagal"){
                    echo "<div class='alert alert-danger text-center' role='alert'>Login Gagal! Username atau Password salah.</div>";
                } else if($_GET['pesan'] == "logout"){
                    echo "<div class='alert alert-success text-center' role='alert'>Anda berhasil logout.</div>";
                } else if($_GET['pesan'] == "belum_login"){
                    echo "<div class='alert alert-warning text-center' role='alert'>Anda harus login untuk mengakses halaman admin.</div>";
                }
            }
            ?>

            <form method="post" action="cek_login.php">
                
                <div class="mb-3">
                    <label for="username" class="form-label small fw-semibold">USERNAME</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Masukkan Username Anda" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label small fw-semibold">PASSWORD</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan Password Anda" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg shadow">
                        <i class="bi bi-box-arrow-in-right me-1"></i> LOGIN
                    </button>
                </div>
            </form>
            </div>
    </div>
    <footer class="footer bg-dark">
        <div class="container">
            <span class="text-white-50 small">
                &copy; <?php echo date('Y'); ?> Showroom Management System. Crafted for Admin.
            </span>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>