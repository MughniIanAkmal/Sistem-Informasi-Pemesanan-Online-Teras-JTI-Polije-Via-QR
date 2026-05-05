<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

// Filter
$filterStatus = $_GET['status'] ?? 'semua';
$allowedStatus = ['semua', 'Masuk', 'Proses', 'Selesai'];
if (!in_array($filterStatus, $allowedStatus)) $filterStatus = 'semua';

$where = $filterStatus !== 'semua' ? "WHERE status = " . $pdo->quote($filterStatus) : '';

try {
    $orders = $pdo->query("SELECT * FROM pesanan $where ORDER BY
        FIELD(status,'Masuk','Proses','Selesai'), created_at ASC")->fetchAll();
} catch (PDOException $e) { $orders = []; }

// Counts
try {
    $counts = $pdo->query("SELECT status, COUNT(*) as c FROM pesanan GROUP BY status")->fetchAll();
    $countMap = ['Masuk' => 0, 'Proses' => 0, 'Selesai' => 0];
    foreach ($counts as $c) $countMap[$c['status']] = $c['c'];
} catch (PDOException $e) {
    $countMap = ['Masuk' => 0, 'Proses' => 0, 'Selesai' => 0];
}
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>📋 Konfirmasi Pesanan</h1>
            <p>Kelola dan perbarui status pesanan pelanggan.</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div style="display:flex; gap:.5rem; margin-bottom:1.5rem; flex-wrap:wrap;">
        <?php
        $tabs = [
            'semua'   => ['label' => 'Semua', 'count' => array_sum($countMap), 'color' => 'var(--text-muted)'],
            'Masuk'   => ['label' => '🟡 Masuk',   'count' => $countMap['Masuk'],   'color' => 'var(--warning)'],
            'Proses'  => ['label' => '🔵 Diproses', 'count' => $countMap['Proses'],  'color' => 'var(--info)'],
            'Selesai' => ['label' => '🟢 Selesai',  'count' => $countMap['Selesai'], 'color' => 'var(--success)'],
        ];
        foreach ($tabs as $key => $tab):
            $isActive = $filterStatus === $key;
        ?>
        <a href="?status=<?= $key ?>"
           style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;border-radius:var(--radius-full);text-decoration:none;font-weight:700;font-size:.875rem;transition:var(--transition);
                  background:<?= $isActive ? 'var(--primary)' : 'var(--white)' ?>;
                  color:<?= $isActive ? '#fff' : 'var(--text-muted)' ?>;
                  border:2px solid <?= $isActive ? 'var(--primary)' : 'var(--border)' ?>;
                  box-shadow:<?= $isActive ? '0 3px 10px rgba(245,158,11,.35)' : 'var(--shadow-sm)' ?>;">
            <?= $tab['label'] ?>
            <span style="background:<?= $isActive ? 'rgba(255,255,255,.25)' : 'var(--bg-page)' ?>; padding:1px 7px; border-radius:9999px; font-size:.7rem;">
                <?= $tab['count'] ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Orders List -->
    <div style="display:flex; flex-direction:column; gap:1rem;">
        <?php if (empty($orders)): ?>
            <div class="content-card" style="text-align:center; padding:3rem;">
                <div style="font-size:3rem; margin-bottom:1rem;">🎉</div>
                <div style="font-weight:700; font-size:1.125rem; margin-bottom:.5rem;">Tidak ada pesanan</div>
                <div style="color:var(--text-muted);">Semua pesanan sudah terselesaikan!</div>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $i => $order):
                [$bgColor, $txtColor, $borderColor] = match($order['status']) {
                    'Masuk'   => ['rgba(245,158,11,.06)', 'var(--warning)',   '#F59E0B'],
                    'Proses'  => ['rgba(59,130,246,.06)',  'var(--info)',      '#3B82F6'],
                    'Selesai' => ['rgba(16,185,129,.06)',  'var(--success)',   '#10B981'],
                    default   => ['var(--white)',           'var(--text-muted)', 'var(--border)'],
                };
            ?>
            <div class="order-row content-card" style="animation-delay:<?= $i * 0.05 ?>s; padding:1.25rem 1.5rem; border-left:4px solid <?= $borderColor ?>; background:<?= $bgColor ?>;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem;">
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <div style="width:48px; height:48px; border-radius:var(--radius-md); background:<?= $bgColor === 'var(--white)' ? 'var(--bg-page)' : 'rgba(255,255,255,.7)' ?>; display:flex; align-items:center; justify-content:center; font-size:1.25rem; border:2px solid <?= $borderColor ?>;">
                            <?= $order['metode_pembayaran'] === 'QRIS' ? '📲' : '💵' ?>
                        </div>
                        <div>
                            <div style="font-weight:800; font-size:1.0625rem;">#ORD-<?= $order['id'] ?></div>
                            <div style="font-size:.8125rem; color:var(--text-muted); margin-top:2px;">
                                🪑 <?= htmlspecialchars($order['nomor_meja'] ?? '-') ?> &bull;
                                <?= htmlspecialchars($order['metode_pembayaran'] ?? '-') ?> &bull;
                                <?= date('H:i, d M Y', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;">
                        <span class="status-badge" style="background:<?= 'rgba(0,0,0,.07)' ?>; color:<?= $txtColor ?>; font-size:.8125rem;">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                        <span style="font-weight:800; font-size:1.125rem; color:var(--primary);">
                            Rp <?= number_format($order['total_harga'], 0, ',', '.') ?>
                        </span>
                        <a href="view.php?id=<?= $order['id'] ?>"
                           style="display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;background:var(--primary);color:#fff;padding:.5rem 1rem;border-radius:var(--radius-sm);font-weight:700;font-size:.8rem;transition:var(--transition);box-shadow:0 3px 10px rgba(245,158,11,.3);"
                           onmouseover="this.style.transform='translateY(-1px)'"
                           onmouseout="this.style.transform=''">
                            <i class="fa-solid fa-arrow-right"></i> Kelola
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
