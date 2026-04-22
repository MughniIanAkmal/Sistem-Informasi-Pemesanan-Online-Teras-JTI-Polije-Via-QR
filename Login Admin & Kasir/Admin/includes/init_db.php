<?php
require_once 'db.php';

try {
    // ── Table: produk ──
    $pdo->exec("CREATE TABLE IF NOT EXISTS produk (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(255) NOT NULL,
        harga DECIMAL(10,2) NOT NULL,
        kategori VARCHAR(50) NOT NULL,
        gambar VARCHAR(255),
        deskripsi TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── Table: pesanan ──
    $pdo->exec("CREATE TABLE IF NOT EXISTS pesanan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nomor_meja VARCHAR(50) DEFAULT 'Tidak Diketahui',
        total_harga DECIMAL(10,2) NOT NULL,
        metode_pembayaran VARCHAR(50),
        status VARCHAR(20) DEFAULT 'Masuk',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── Table: detail_pesanan ──
    $pdo->exec("CREATE TABLE IF NOT EXISTS detail_pesanan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pesanan_id INT NOT NULL,
        produk_id INT,
        nama_produk VARCHAR(255),
        jumlah INT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
        FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── Alter: Add nomor_meja if it doesn't exist (for existing installs) ──
    $cols = $pdo->query("SHOW COLUMNS FROM pesanan LIKE 'nomor_meja'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE pesanan ADD COLUMN nomor_meja VARCHAR(50) DEFAULT 'Tidak Diketahui' AFTER id");
        echo "✅ Kolom nomor_meja berhasil ditambahkan ke tabel pesanan.<br>";
    }

    echo "✅ Semua tabel database berhasil dibuat/diverifikasi.<br>";
    echo "✅ <code>produk</code> – OK<br>";
    echo "✅ <code>pesanan</code> – OK<br>";
    echo "✅ <code>detail_pesanan</code> – OK<br>";

} catch (PDOException $e) {
    die("❌ Inisialisasi database gagal: " . $e->getMessage());
}
?>
