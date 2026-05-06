<?php
require_once '../includes/db.php';

$type = $_GET['type'] ?? 'harian';
$date = $_GET['date'] ?? date('Y-m-d');
$start_date = $_GET['start'] ?? '';
$end_date = $_GET['end'] ?? '';
$month = $_GET['month'] ?? date('Y-m');

// ====================================================================
// Query preparation based on type
// ====================================================================
if ($type === 'harian') {
    // Laporan Harian - detail per transaksi
    $stmt = $pdo->prepare("
        SELECT p.id, p.total_harga, p.metode_pembayaran, p.created_at, p.nomor_meja, p.status,
               GROUP_CONCAT(CONCAT(IFNULL(dp.nama_produk, IFNULL(pr.nama, 'Produk Terhapus')), ' (', dp.jumlah, ')') SEPARATOR '; ') as produk_list
        FROM pesanan p
        LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
        LEFT JOIN produk pr ON dp.produk_id = pr.id
        WHERE DATE(p.created_at) = ?
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$date]);
    $data = $stmt->fetchAll();
    $filename = "Laporan_Harian_$date.xls";
    $title = "Laporan Harian - " . date('d M Y', strtotime($date));

    // Build HTML table
    $tableHtml = '
    <table border="1">
        <thead>
            <tr style="background-color: #4472C4; color: #FFFFFF; font-weight: bold;">
                <th>No</th>
                <th>ID Pesanan</th>
                <th>Waktu/Tanggal</th>
                <th>No. Meja</th>
                <th>Daftar Produk (Kuantitas)</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>';

    $no = 1;
    $grand_total = 0;
    foreach ($data as $row) {
        $grand_total += $row['total_harga'];
        $tableHtml .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>#ORD-' . $row['id'] . '</td>
                <td>' . date('d/m/Y H:i', strtotime($row['created_at'])) . '</td>
                <td>' . htmlspecialchars($row['nomor_meja'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['produk_list'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['metode_pembayaran'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['status'] ?? '-') . '</td>
                <td style="text-align: right;">Rp ' . number_format($row['total_harga'], 0, ',', '.') . '</td>
            </tr>';
    }

    $tableHtml .= '
            <tr style="background-color: #E2EFDA; font-weight: bold;">
                <td colspan="7" style="text-align: right;">TOTAL PENDAPATAN</td>
                <td style="text-align: right;">Rp ' . number_format($grand_total, 0, ',', '.') . '</td>
            </tr>
        </tbody>
    </table>';

} elseif ($type === 'mingguan') {
    // Laporan Mingguan - ringkasan per hari
    if (empty($start_date)) $start_date = date('Y-m-d', strtotime('monday this week'));
    if (empty($end_date)) $end_date = date('Y-m-d', strtotime('sunday this week'));

    $stmt = $pdo->prepare("
        SELECT DATE(p.created_at) as tgl, SUM(p.total_harga) as harian_total, COUNT(p.id) as trx_count
        FROM pesanan p
        WHERE DATE(p.created_at) BETWEEN ? AND ?
        GROUP BY DATE(p.created_at)
        ORDER BY DATE(p.created_at) ASC
    ");
    $stmt->execute([$start_date, $end_date]);
    $data = $stmt->fetchAll();
    $filename = "Laporan_Mingguan_" . $start_date . "_sd_" . $end_date . ".xls";
    $title = "Laporan Mingguan - " . date('d M Y', strtotime($start_date)) . " s/d " . date('d M Y', strtotime($end_date));

    $tableHtml = '
    <table border="1">
        <thead>
            <tr style="background-color: #4472C4; color: #FFFFFF; font-weight: bold;">
                <th>No</th>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Jumlah Transaksi</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>';

    $no = 1;
    $grand_total = 0;
    $total_trx = 0;
    foreach ($data as $row) {
        $grand_total += $row['harian_total'];
        $total_trx += $row['trx_count'];

        // Get Indonesian day name
        $dayNames = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $dayEn = date('l', strtotime($row['tgl']));
        $dayId = $dayNames[$dayEn] ?? $dayEn;

        $tableHtml .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . date('d/m/Y', strtotime($row['tgl'])) . '</td>
                <td>' . $dayId . '</td>
                <td style="text-align: center;">' . $row['trx_count'] . '</td>
                <td style="text-align: right;">Rp ' . number_format($row['harian_total'], 0, ',', '.') . '</td>
            </tr>';
    }

    $tableHtml .= '
            <tr style="background-color: #E2EFDA; font-weight: bold;">
                <td colspan="3" style="text-align: right;">TOTAL</td>
                <td style="text-align: center;">' . $total_trx . '</td>
                <td style="text-align: right;">Rp ' . number_format($grand_total, 0, ',', '.') . '</td>
            </tr>
        </tbody>
    </table>';

} elseif ($type === 'bulanan') {
    // Laporan Bulanan - ringkasan per hari dalam bulan
    $stmt = $pdo->prepare("
        SELECT DATE(p.created_at) as tgl, SUM(p.total_harga) as harian_total, COUNT(p.id) as trx_count
        FROM pesanan p
        WHERE DATE_FORMAT(p.created_at, '%Y-%m') = ?
        GROUP BY DATE(p.created_at)
        ORDER BY DATE(p.created_at) ASC
    ");
    $stmt->execute([$month]);
    $data = $stmt->fetchAll();
    $filename = "Laporan_Bulanan_" . $month . ".xls";
    $title = "Laporan Bulanan - " . date('F Y', strtotime($month . '-01'));

    $tableHtml = '
    <table border="1">
        <thead>
            <tr style="background-color: #4472C4; color: #FFFFFF; font-weight: bold;">
                <th>No</th>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Jumlah Transaksi</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>';

    $no = 1;
    $grand_total = 0;
    $total_trx = 0;
    foreach ($data as $row) {
        $grand_total += $row['harian_total'];
        $total_trx += $row['trx_count'];

        $dayNames = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $dayEn = date('l', strtotime($row['tgl']));
        $dayId = $dayNames[$dayEn] ?? $dayEn;

        $tableHtml .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . date('d/m/Y', strtotime($row['tgl'])) . '</td>
                <td>' . $dayId . '</td>
                <td style="text-align: center;">' . $row['trx_count'] . '</td>
                <td style="text-align: right;">Rp ' . number_format($row['harian_total'], 0, ',', '.') . '</td>
            </tr>';
    }

    $tableHtml .= '
            <tr style="background-color: #E2EFDA; font-weight: bold;">
                <td colspan="3" style="text-align: right;">TOTAL</td>
                <td style="text-align: center;">' . $total_trx . '</td>
                <td style="text-align: right;">Rp ' . number_format($grand_total, 0, ',', '.') . '</td>
            </tr>
        </tbody>
    </table>';

} else {
    die("Tipe export tidak valid.");
}

// ====================================================================
// Generate Excel-compatible HTML file
// ====================================================================
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

// Output Excel-compatible HTML
echo '
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>' . htmlspecialchars(str_replace(' ', '_', $title)) . '</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000000; padding: 8px 12px; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        th { text-align: center; }
    </style>
</head>
<body>
    <h2>' . htmlspecialchars($title) . '</h2>
    <p>Dicetak pada: ' . date('d/m/Y H:i:s') . '</p>
    ' . $tableHtml . '
</body>
</html>';

exit;
?>
