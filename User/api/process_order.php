<?php
header('Content-Type: application/json');
require_once '../../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['cart'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Data pesanan tidak lengkap.']);
    exit;
}

$nomor_meja       = $data['nomor_meja']      ?? 'Tidak Diketahui';
$total            = $data['total']            ?? 0;
$payment_method   = $data['payment_method']  ?? 'Tidak Diketahui';

try {
    $pdo->beginTransaction();

    // 1. Insert into pesanan (with nomor_meja, status 'Masuk')
    $stmt = $pdo->prepare("INSERT INTO pesanan (nomor_meja, total_harga, metode_pembayaran, status) VALUES (?, ?, ?, 'Masuk')");
    $stmt->execute([$nomor_meja, $total, $payment_method]);
    $pesanan_id = $pdo->lastInsertId();

    // 2. Insert into detail_pesanan (store nama_produk for historical record)
    $stmtDetail = $pdo->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, jumlah, subtotal) VALUES (?, ?, ?, ?, ?)");

    foreach ($data['cart'] as $item) {
        $subtotal    = $item['price'] * $item['qty'];
        $nama_produk = $item['name'] ?? 'Produk Tidak Dikenal';
        $stmtDetail->execute([$pesanan_id, $item['id'], $nama_produk, $item['qty'], $subtotal]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $pesanan_id]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    file_put_contents(__DIR__ . '/debug.log', "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Gagal memproses pesanan: ' . $e->getMessage()]);
}
?>
