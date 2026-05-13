<?php
require_once 'includes/db.php';
try {
    $pdo->exec("ALTER TABLE pesanan ADD COLUMN catatan TEXT DEFAULT NULL AFTER metode_pembayaran");
    echo "Success adding catatan to pesanan.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
try {
    $pdo->exec("ALTER TABLE pesanan ADD COLUMN catatan TEXT DEFAULT NULL AFTER status");
    echo "Success adding catatan to pesanan (second attempt).\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
