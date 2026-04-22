<?php
require_once '../includes/db.php';

$type = $_GET['type'] ?? 'harian';
$date = $_GET['date'] ?? date('Y-m-d');

// Query preparation based on type
if ($type === 'harian') {
    $stmt = $pdo->prepare("
        SELECT p.id, p.total_harga, p.metode_pembayaran, p.created_at,
               GROUP_CONCAT(CONCAT(IFNULL(pr.nama, 'Produk Terhapus'), ' (', dp.jumlah, ')') SEPARATOR '; ') as produk_list
        FROM pesanan p
        LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
        LEFT JOIN produk pr ON dp.produk_id = pr.id
        WHERE DATE(p.created_at) = ?
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$date]);
    $filename = "Laporan_Harian_$date.csv";
} else {
    // Basic fallback for weekly/monthly for now
    die("Export type not fully implemented yet.");
}

$data = $stmt->fetchAll();

// Headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, ['ID Pesanan', 'Waktu/Tanggal', 'Daftar Produk (Kuantitas)', 'Metode Pembayaran', 'Total Pendapatan']);

// Data rows
foreach ($data as $row) {
    fputcsv($output, [
        '#ORD-' . $row['id'],
        $row['created_at'],
        $row['produk_list'],
        $row['metode_pembayaran'],
        $row['total_harga']
    ]);
}

fclose($output);
exit;
?>
