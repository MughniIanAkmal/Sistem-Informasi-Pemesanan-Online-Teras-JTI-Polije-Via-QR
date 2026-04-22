<?php
require_once 'db.php';

echo "<h2>Schema Fix Tool</h2>";

try {
    // Add nama_produk to detail_pesanan if it doesn't exist
    $cols = $pdo->query("SHOW COLUMNS FROM detail_pesanan LIKE 'nama_produk'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE detail_pesanan ADD COLUMN nama_produk VARCHAR(255) AFTER produk_id");
        echo "✅ Kolom <code>nama_produk</code> berhasil ditambahkan ke <code>detail_pesanan</code>.<br>";
    } else {
        echo "ℹ️ Kolom <code>nama_produk</code> sudah ada.<br>";
    }

    // Add nomor_meja to pesanan if it doesn't already exist
    $cols2 = $pdo->query("SHOW COLUMNS FROM pesanan LIKE 'nomor_meja'")->fetchAll();
    if (empty($cols2)) {
        $pdo->exec("ALTER TABLE pesanan ADD COLUMN nomor_meja VARCHAR(50) DEFAULT 'Tidak Diketahui' AFTER id");
        echo "✅ Kolom <code>nomor_meja</code> berhasil ditambahkan ke <code>pesanan</code>.<br>";
    } else {
        echo "ℹ️ Kolom <code>nomor_meja</code> sudah ada.<br>";
    }

    echo "<br><strong style='color:green'>✅ Semua schema sudah lengkap!</strong>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
