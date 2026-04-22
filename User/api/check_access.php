<?php
header('Content-Type: application/json');
require_once '../../includes/db.php';

// Set timezone
date_default_timezone_set('Asia/Jakarta');

try {
    $rows = $pdo->query("SELECT `key`, `value` FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    // Settings table may not exist yet — default to open
    echo json_encode(['open' => true, 'message' => '']);
    exit;
}

// Force close override
if (($rows['toko_tutup'] ?? '0') === '1') {
    echo json_encode([
        'open'    => false,
        'message' => $rows['pesan_tutup'] ?? 'Teras JTI sedang tutup.'
    ]);
    exit;
}

// Check day of week (PHP: 0=Sun, 1=Mon,...6=Sat)
$today          = (int) date('w');  // 0–6
$hariArray      = explode(',', $rows['hari_buka'] ?? '0,1,2,3,4,5,6');
$hariArrayInt   = array_map('intval', $hariArray);

if (!in_array($today, $hariArrayInt)) {
    echo json_encode([
        'open'    => false,
        'message' => ($rows['pesan_tutup'] ?? 'Teras JTI sedang tutup hari ini.')
    ]);
    exit;
}

// Check time
$now     = date('H:i');
$jamBuka = $rows['jam_buka']  ?? '07:00';
$jamTutup = $rows['jam_tutup'] ?? '22:00';

if ($now < $jamBuka || $now >= $jamTutup) {
    echo json_encode([
        'open'     => false,
        'message'  => $rows['pesan_tutup'] ?? 'Teras JTI sedang tutup.',
        'jam_buka' => $jamBuka,
        'jam_tutup'=> $jamTutup,
    ]);
    exit;
}

echo json_encode([
    'open'      => true,
    'message'   => '',
    'jam_buka'  => $jamBuka,
    'jam_tutup' => $jamTutup,
]);
?>
