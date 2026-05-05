<?php
date_default_timezone_set('Asia/Jakarta');
require_once dirname(__DIR__, 2) . '/includes/db.php';

// Fetch settings
function getSetting($pdo, $key, $default = '') {
    try {
        $s = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $s->execute([$key]);
        $row = $s->fetch();
        return $row ? $row['value'] : $default;
    } catch (PDOException $e) { return $default; }
}

$tokoTutup  = getSetting($pdo, 'toko_tutup', '0');
$jamBuka    = getSetting($pdo, 'jam_buka',   '07:00');
$jamTutup   = getSetting($pdo, 'jam_tutup',  '22:00');
$hariBuka   = explode(',', getSetting($pdo, 'hari_buka', '0,1,2,3,4,5,6'));
$pesanTutup = getSetting($pdo, 'pesan_tutup', 'Teras JTI sedang tutup. Kami akan segera kembali!');
$namaToko   = getSetting($pdo, 'nama_toko',   'Teras JTI');

$today = (string) date('w');   // 0=Sun,...6=Sat
$now   = date('H:i');

$isClosed = false;
$closedReason = '';

if ($tokoTutup === '1') {
    $isClosed = true;
    $closedReason = $pesanTutup;
} elseif (!in_array($today, $hariBuka)) {
    $isClosed = true;
    $closedReason = $pesanTutup;
} elseif ($now < $jamBuka || $now >= $jamTutup) {
    $isClosed = true;
    $closedReason = $pesanTutup;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?= htmlspecialchars($namaToko) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/meja-teras-jti.css?v=<?= time() ?>">
</head>
<body>

<?php if ($isClosed): ?>
<!-- ══ CLOSED PAGE ══ -->
<div class="closed-page">
  <div class="closed-card">
    <div class="closed-icon">🔒</div>
    <h2><?= htmlspecialchars($namaToko) ?></h2>
    <p><?= htmlspecialchars($closedReason) ?></p>
    <div class="closed-hours">
      ⏰ Jam Operasional: <?= $jamBuka ?> – <?= $jamTutup ?> WIB
    </div>
  </div>
</div>
</body>
</html>
<?php exit; ?>
<?php endif; ?>

<div class="app">
