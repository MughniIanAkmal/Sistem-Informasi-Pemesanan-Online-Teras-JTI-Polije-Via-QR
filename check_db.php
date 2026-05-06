<?php
require_once __DIR__ . '/includes/db.php';

echo "=== TABLES ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) echo "$t\n";

echo "\n=== PESANAN STRUCTURE ===\n";
$cols = $pdo->query("DESCRIBE pesanan")->fetchAll();
foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . " | " . $c['Null'] . " | " . $c['Key'] . "\n";

echo "\n=== DETAIL_PESANAN STRUCTURE ===\n";
$cols = $pdo->query("DESCRIBE detail_pesanan")->fetchAll();
foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . " | " . $c['Null'] . " | " . $c['Key'] . "\n";

echo "\n=== SAMPLE PESANAN DATA ===\n";
$rows = $pdo->query("SELECT * FROM pesanan ORDER BY id DESC LIMIT 5")->fetchAll();
foreach ($rows as $r) {
    echo json_encode($r) . "\n";
}

echo "\n=== SAMPLE DETAIL_PESANAN DATA ===\n";
$rows = $pdo->query("SELECT * FROM detail_pesanan ORDER BY id DESC LIMIT 5")->fetchAll();
foreach ($rows as $r) {
    echo json_encode($r) . "\n";
}

echo "\n=== USERS TABLE CHECK ===\n";
if (in_array('users', $tables)) {
    $cols = $pdo->query("DESCRIBE users")->fetchAll();
    foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . "\n";
    echo "--- users data ---\n";
    $rows = $pdo->query("SELECT * FROM users")->fetchAll();
    foreach ($rows as $r) echo json_encode($r) . "\n";
} else {
    echo "No users table found.\n";
}
