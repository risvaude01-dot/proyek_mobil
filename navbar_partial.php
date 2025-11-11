<?php 
// navbar_partial.php - Navbar Horizontal (KONSISTEN)
// Asumsi $username sudah di-define di file utama (misal: tambah_customer.php)

$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #003366;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-car-front-fill me-2"></i> SHOWROOM CENTRAL
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
                <?php if ($current_page == 'data_mobil.php'): ?>
                    <a href="tambah_mobil.php" class="btn btn-sm btn-success me-3"><i class="bi bi-plus-circle-fill me-1"></i> Tambah Data Mobil</a>
                <?php elseif ($current_page == 'data_customer.php' || $current_page == 'tambah_customer.php' || $current_page == 'edit_customer.php'): ?>
                    <a href="tambah_customer.php" class="btn btn-sm btn-success me-3"><i class="bi bi-plus-circle-fill me-1"></i> Tambah Customer Baru</a>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?php if($current_page == 'index.php') echo 'active'; ?>" href="index.php"><i class="bi bi-house-door-fill me-1"></i> Dashboard</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php if(in_array($current_page, ['data_mobil.php', 'data_customer.php', 'tambah_mobil.php', 'edit_mobil.php', 'tambah_customer.php', 'edit_customer.php'])) echo 'active'; ?>" href="#" id="dataMasterDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-database-fill me-1"></i> Data Master
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dataMasterDropdown">
                        <li><a class="dropdown-item <?php if(in_array($current_page, ['data_mobil.php', 'tambah_mobil.php', 'edit_mobil.php'])) echo 'active'; ?>" href="data_mobil.php">Data Mobil</a></li>
                        <li><a class="dropdown-item <?php if(in_array($current_page, ['data_customer.php', 'tambah_customer.php', 'edit_customer.php'])) echo 'active'; ?>" href="data_customer.php">Data Customer</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="transaksiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cash-stack me-1"></i> Transaksi
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="transaksiDropdown">
                        <li><a class="dropdown-item" href="data_penjualan.php">Riwayat Penjualan</a></li>
                        <li><a class="dropdown-item" href="penjualan.php">Catat Penjualan</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="laporan_penjualan.php"><i class="bi bi-printer-fill me-1"></i> Laporan</a>
                </li>

            </ul>
            
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3 text-white small">Halo, **<?php echo htmlspecialchars($username); ?>**</span>
                <a href="logout.php" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>

        </div>
    </div>
</nav>