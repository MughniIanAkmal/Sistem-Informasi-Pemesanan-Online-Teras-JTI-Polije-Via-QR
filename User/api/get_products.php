<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../../includes/db.php';

try {
    // Fetch all columns including diskon and is_promo (added via admin edit)
    $stmt = $pdo->query("SELECT id, nama as name, harga as price, kategori as cat, gambar as img, deskripsi as `desc`, COALESCE(diskon,0) as diskon, COALESCE(is_promo,0) as is_promo FROM produk ORDER BY created_at DESC");
    $products = $stmt->fetchAll();

    foreach ($products as &$p) {
        // Prefix local image path
        if (!empty($p['img']) && strpos($p['img'], 'http') !== 0) {
            $p['img'] = '/Project Smster 2/User/' . $p['img'];
        }
        // Compute discounted price
        $p['diskon']       = (int)$p['diskon'];
        $p['is_promo']     = (int)$p['is_promo'];
        $p['price_final']  = $p['diskon'] > 0
            ? round($p['price'] * (1 - $p['diskon'] / 100))
            : (float)$p['price'];
    }
    unset($p);

    echo json_encode($products);
} catch (PDOException $e) {
    // Fallback: columns may not exist yet on old installs
    try {
        $stmt = $pdo->query("SELECT id, nama as name, harga as price, kategori as cat, gambar as img, deskripsi as `desc` FROM produk ORDER BY created_at DESC");
        $products = $stmt->fetchAll();
        foreach ($products as &$p) {
            if (!empty($p['img']) && strpos($p['img'], 'http') !== 0) {
                $p['img'] = '/Project Smster 2/User/' . $p['img'];
            }
            $p['diskon']      = 0;
            $p['is_promo']    = 0;
            $p['price_final'] = (float)$p['price'];
        }
        unset($p);
        echo json_encode($products);
    } catch (PDOException $e2) {
        http_response_code(500);
        echo json_encode(['error' => $e2->getMessage()]);
    }
}
?>
