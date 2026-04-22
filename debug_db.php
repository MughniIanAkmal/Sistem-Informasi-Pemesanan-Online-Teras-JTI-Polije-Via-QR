<?php
require_once 'includes/db.php';

echo "<h2>Database Debug</h2>";

try {
    // Check Produk
    $stmt = $pdo->query("SELECT COUNT(*) FROM produk");
    echo "Total Produk: " . $stmt->fetchColumn() . "<br>";
    
    // Check Pesanan
    $stmt = $pdo->query("SELECT COUNT(*) FROM pesanan");
    echo "Total Pesanan: " . $stmt->fetchColumn() . "<br>";
    
    // Show last 5 products
    echo "<h3>Daftar Produk (5 Terakhir):</h3>";
    $stmt = $pdo->query("SELECT id, nama, kategori, harga FROM produk ORDER BY id DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']} - Name: {$row['nama']} - Cat: {$row['kategori']} - Price: {$row['harga']}<br>";
    }

    // Show last 5 orders
    echo "<h3>Daftar Pesanan (5 Terakhir):</h3>";
    $stmt = $pdo->query("SELECT id, total_harga, created_at FROM pesanan ORDER BY id DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']} - Total: {$row['total_harga']} - Date: {$row['created_at']}<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
