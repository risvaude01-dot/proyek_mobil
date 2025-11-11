🚗 Showroom Management System (SMS) - Sistem Manajemen Showroom Mobil

Proyek ini adalah implementasi sistem manajemen inventaris dan transaksi penjualan mobil, dirancang khusus sebagai Panel Admin untuk operasional internal sebuah showroom mobil. Aplikasi ini dibangun menggunakan PHP dan MySQLi untuk memastikan pengelolaan data yang efisien dan akurat.

Tujuan utama dari sistem ini adalah untuk mengotomatisasi pencatatan stok (barang masuk), transaksi penjualan (barang keluar), dan pelaporan keuangan.

Fitur Utama Sistem

Sistem ini berfokus pada fungsi back-office dan manajemen data harian:

1. Manajemen Inventaris (Mobil)

Data Mobil (data_mobil.php): Melihat daftar lengkap inventaris mobil yang ada, termasuk detail seperti Merk, Model, Harga Beli/Jual, dan Stok.

Barang Masuk (barang_masuk.php): Modul untuk mencatat penambahan stok mobil baru, baik mobil baru yang belum pernah terdata atau penambahan jumlah stok mobil yang sudah ada.

Barang Keluar/Penjualan (barang_keluar.php): Modul utama untuk mencatat transaksi penjualan mobil, secara otomatis mengurangi stok yang tersedia dan mencatat detail pembeli.

2. Manajemen Transaksi & Laporan

Data Penjualan (data_penjualan.php): Menampilkan riwayat lengkap semua transaksi penjualan yang telah terjadi, termasuk total penjualan yang tercatat.

Pelaporan Keuangan Sederhana: Menyediakan data dasar untuk memantau arus kas dari penjualan mobil.

3. Manajemen Customer

Data Customer (data_customer.php): Menyimpan dan mengelola database informasi pelanggan (Nama, Alamat, No. Telp) untuk memudahkan pelacakan pembeli.

Edit Customer (edit_customer.php): Fungsi untuk memperbarui data pelanggan.

4. Autentikasi

Login & Logout (cek_login.php): Sistem session sederhana untuk memproteksi akses, memastikan hanya admin yang berwenang yang dapat mengakses dashboard.

Teknologi yang Digunakan

Proyek ini dibangun menggunakan stack teknologi tradisional, ideal untuk lingkungan shared hosting atau server LAMP/XAMPP:

Backend: PHP (Native)

Database: MySQLi

Frontend/Styling: Bootstrap 5.3 dan jQuery (untuk DataTables)

Cara Instalasi dan Menjalankan Proyek

# 1. Clone repositori ini
git clone [https://github.com/risvaude01-dot/showroom-management-system.git](https://github.com/risvaude01-dot/showroom-management-system.git)
cd showroom-management-system

# 2. Setup Database
#    - Buat database MySQL baru (misalnya: 'db_showroom')
#    - Import struktur tabel yang diperlukan (tabel_mobil, tabel_customer, tabel_penjualan, tabel_admin)

# 3. Konfigurasi Koneksi
#    - Pastikan file 'koneksi.php' Anda berisi konfigurasi database yang benar.

# 4. Jalankan Aplikasi
#    - Pindahkan semua file ke folder htdocs/www server lokal Anda (XAMPP/WAMPP).
#    - Akses aplikasi melalui browser Anda (contoh: http://localhost/showroom-management-system/)
#    - Lakukan Login menggunakan kredensial admin.
