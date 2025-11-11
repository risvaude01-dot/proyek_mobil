<?php
// START: Setup Environment & Proteksi Session
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location:login.php?pesan=belum_login");
    exit();
}

include 'koneksi.php';

// Data dummy yang sama seperti di laporan_penjualan.php
$dummy_data = [
    ['2025-10-20', 'Toyota Yaris 2024', 'Budi Santoso', 250000000, 250000000, 'Lunas'],
    ['2025-10-15', 'Honda HRV 2023', 'Siti Aminah', 320000000, 320000000, 'Lunas'],
    ['2025-10-01', 'Suzuki Ertiga 2022', 'Rahmat Jaya', 180000000, 150000000, 'DP/Cicilan'],
];

// Lo harus mengganti bagian ini dengan logic lo untuk mendapatkan data penjualan dari database.
// Contoh: $result = mysqli_query($koneksi, "SELECT * FROM tabel_penjualan ORDER BY tanggal_jual DESC");

// >>> 1. MEMANGGIL LIBRARY DOMPDF (SESUAI DENGAN LOKASI FOLDER LO) <<<
require_once 'dompdf/autoload.inc.php'; // PASTIKAN PATH INI BENAR!

use Dompdf\Dompdf;
use Dompdf\Options;

// Inisialisasi Dompdf dengan Options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'sans-serif'); // Font standar yang didukung

$dompdf = new Dompdf($options);

// >>> 2. MEMBANGUN KONTEN HTML UNTUK PDF <<<

// Siapkan isi tabel (menggunakan dummy data)
$table_rows = '';
$no = 1;
$total_penjualan = 0;

foreach ($dummy_data as $data) {
    $status_badge = ($data[5] == 'Lunas') ? 'background-color: #28a745; color: white;' : 'background-color: #ffc107; color: black;';
    $total_penjualan += $data[4];

    $table_rows .= '
        <tr>
            <td style="text-align: center;">' . $no++ . '</td>
            <td>' . $data[0] . '</td>
            <td>' . $data[1] . '</td>
            <td>' . htmlspecialchars($data[2]) . '</td>
            <td style="text-align: right;">Rp ' . number_format($data[3], 0, ',', '.') . '</td>
            <td style="text-align: right; font-weight: bold;">Rp ' . number_format($data[4], 0, ',', '.') . '</td>
            <td style="text-align: center;"><span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; ' . $status_badge . ' ">' . $data[5] . '</span></td>
        </tr>';
}

$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan Showroom</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; color: #003366; }
        .header p { margin: 2px 0; font-size: 11pt; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; text-align: left; font-size: 10pt; color: #333; }
        .total-summary { margin-top: 20px; text-align: right; font-size: 12pt; }
        .total-summary strong { color: #dc3545; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #888; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN PENJUALAN KENDARAAN</h1>
        <p>SHOWROOM CENTRAL ADMIN</p>
        <p>Periode: 1 Oktober 2025 - 31 Oktober 2025</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No.</th>
                <th style="width: 15%;">Tanggal Jual</th>
                <th style="width: 25%;">Merk & Model Mobil</th>
                <th style="width: 20%;">Pembeli</th>
                <th style="width: 15%; text-align: right;">Harga Jual</th>
                <th style="width: 15%; text-align: right;">Total Bayar</th>
                <th style="width: 5%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            ' . $table_rows . '
        </tbody>
    </table>

    <div class="total-summary">
        Total Nilai Penjualan Bersih: <strong>Rp ' . number_format($total_penjualan, 0, ',', '.') . '</strong>
    </div>

    <div class="footer">
        Dicetak oleh: ' . htmlspecialchars($_SESSION['username'] ?? 'Admin') . ' pada ' . date('d-m-Y H:i:s') . '
    </div>

</body>
</html>';

// >>> 3. EKSEKUSI DOMPDF <<<
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF ke browser untuk di-download
$dompdf->stream("Laporan_Penjualan_" . date('Ymd_His') . ".pdf", array("Attachment" => 1));

?>