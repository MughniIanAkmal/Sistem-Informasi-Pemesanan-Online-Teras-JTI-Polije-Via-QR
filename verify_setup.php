<?php
header('Content-Type: text/html; charset=utf-8');
echo "<h1>System Connectivity Check</h1>";

// 1. Check DB File
$dbFile = 'includes/db.php';
if (file_exists($dbFile)) {
    echo "✅ <code>includes/db.php</code> ditemukan.<br>";
} else {
    echo "❌ <code>includes/db.php</code> TIDAK ditemukan!<br>";
    exit;
}

// 2. Check Connection
try {
    require_once $dbFile;
    echo "✅ Koneksi Database berhasil.<br>";
} catch (Exception $e) {
    echo "❌ Koneksi Database GAGAL: " . $e->getMessage() . "<br>";
    exit;
}

// 3. Check Tables
$tables = ['produk', 'pesanan', 'detail_pesanan'];
foreach ($tables as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "✅ Tabel <code>$table</code> ditemukan (Jumlah Data: $count).<br>";
    } catch (PDOException $e) {
        echo "❌ Tabel <code>$table</code> TIDAK ditemukan! Pastikan sudah menjalankan <code>init_db.php</code>.<br>";
    }
}

// 4. Check API Access
echo "<h3>API Path Check:</h3>";
echo "Lokasi API: <code>User/api/get_products.php</code> " . (file_exists('User/api/get_products.php') ? "✅" : "❌") . "<br>";
echo "Lokasi Checkout API: <code>User/api/process_order.php</code> " . (file_exists('User/api/process_order.php') ? "✅" : "❌") . "<br>";

echo "<hr><p>Jika semua bertanda ✅, sistem seharusnya sudah terkoneksi dengan benar.</p>";
?>
