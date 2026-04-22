<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../../includes/db.php';

try {
    $stmt = $pdo->query("SELECT id, nama as name, harga as price, kategori as cat, gambar as img, deskripsi as `desc` FROM produk ORDER BY created_at DESC");
    $products = $stmt->fetchAll();

    // Prefix image path with the User assets folder base URL
    foreach ($products as &$p) {
        if (!empty($p['img']) && strpos($p['img'], 'http') !== 0) {
            // img is stored as "assets/img/produk/xxx.jpg" → make it relative to User/
            $p['img'] = '/Project Smster 2/User/' . $p['img'];
        }
    }
    unset($p);

    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
