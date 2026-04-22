<?php
require_once 'db.php';

echo "<h2>⚙️ Settings Schema Setup</h2>";

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        `key` VARCHAR(100) PRIMARY KEY,
        `value` TEXT NOT NULL,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Tabel <code>settings</code> siap.<br>";

    // Default values
    $defaults = [
        'jam_buka'       => '07:00',
        'jam_tutup'      => '22:00',
        'hari_buka'      => '1,2,3,4,5,6,0',  // 0=Sun, 1=Mon, ..., 6=Sat (semua hari)
        'toko_tutup'     => '0',               // 1 = paksa tutup
        'pesan_tutup'    => 'Teras JTI sedang tutup. Kami akan segera kembali!',
        'nama_toko'      => 'Teras JTI',
        'tagline_toko'   => 'Pesan menu favoritmu sekarang',
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (`key`, `value`) VALUES (?, ?)");
    foreach ($defaults as $k => $v) {
        $stmt->execute([$k, $v]);
        echo "✅ Default setting: <code>$k</code> = <em>$v</em><br>";
    }

    echo "<hr><strong style='color:green'>✅ Settings table siap!</strong>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
