<?php
// get_mobil.php
// FILE INI HARUS BERADA DI FOLDER YANG SAMA DENGAN KONEKSI.PHP
include 'koneksi.php';

$query = "SELECT id_mobil, merk_mobil, model_mobil FROM tabel_mobil ORDER BY merk_mobil";
$result = mysqli_query($koneksi, $query);

$mobil_options = '';
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $mobil_options .= '<option value="' . $row['id_mobil'] . '">' . htmlspecialchars($row['merk_mobil'] . ' ' . $row['model_mobil']) . '</option>';
    }
} else {
    $mobil_options .= '<option value="">-- Tambahkan Data Mobil Dulu --</option>';
}

echo $mobil_options;
?>