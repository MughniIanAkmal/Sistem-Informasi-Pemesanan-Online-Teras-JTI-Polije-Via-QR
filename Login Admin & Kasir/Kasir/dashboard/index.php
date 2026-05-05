<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

// Stats
try {
    $stats = $pdo->query("
        SELECT
            COUNT(id) as total_orders,
            SUM(CASE WHEN status = 'Masuk'   THEN 1 ELSE 0 END) as masuk,
            SUM(CASE WHEN status = 'Proses'  THEN 1 ELSE 0 END) as proses,
            SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai,
            COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total_harga END), 0) as pendapatan_hari_ini
        FROM pesanan
    ")->fetch();
} catch (PDOException $e) {
    $stats = ['total_orders' => 0, 'masuk' => 0, 'proses' => 0, 'selesai' => 0, 'pendapatan_hari_ini' => 0];
}

// Recent orders (today)
try {
    $recentOrders = $pdo->query("
        SELECT * FROM pesanan
        ORDER BY created_at DESC
        LIMIT 15
    ")->fetchAll();
} catch (PDOException $e) { $recentOrders = []; }
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>🏪 Kasir Dashboard</h1>
            <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['kasir_user']) ?></strong> &bull; <?= date('l, d F Y') ?></p>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <div style="text-align:right;">
                <div style="font-weight:700; font-size:.875rem;"><?= htmlspecialchars($_SESSION['kasir_user']) ?></div>
                <div style="font-size:.75rem; color:var(--text-muted);">Kasir • Online</div>
            </div>
            <div style="width:44px;height:44px;background:var(--primary-light);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:var(--primary-dark);font-weight:800;font-size:1rem;">
                KS
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,.12); color:var(--primary);">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="stat-info">
                <div class="label">Pesanan Masuk</div>
                <div class="value" style="color:var(--primary);"><?= $stats['masuk'] ?? 0 ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,.12); color:var(--info);">
                <i class="fa-solid fa-spinner"></i>
            </div>
            <div class="stat-info">
                <div class="label">Sedang Diproses</div>
                <div class="value" style="color:var(--info);"><?= $stats['proses'] ?? 0 ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.12); color:var(--success);">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-info">
                <div class="label">Selesai Hari Ini</div>
                <div class="value" style="color:var(--success);"><?= $stats['selesai'] ?? 0 ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.12); color:var(--success);">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <div class="stat-info">
                <div class="label">Pendapatan Hari Ini</div>
                <div class="value" style="font-size:1.1rem;">Rp <?= number_format($stats['pendapatan_hari_ini'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Action -->
    <div style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;">
        <a href="../pesanan/index.php" class="btn-success" style="text-decoration:none; padding:.75rem 1.5rem; font-size:.9375rem;">
            <i class="fa-solid fa-receipt"></i> Kelola Pesanan Masuk
            <?php if (($stats['masuk'] ?? 0) > 0): ?>
                <span style="background:#fff; color:var(--primary); padding:2px 8px; border-radius:9999px; font-size:.75rem; font-weight:800; margin-left:.25rem;"><?= $stats['masuk'] ?></span>
            <?php endif; ?>
        </a>
        <a href="../jam_operasional/index.php" style="text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; padding:.75rem 1.5rem; background:var(--primary-light); color:var(--primary-dark); border-radius:var(--radius-md); font-weight:700; font-size:.9375rem; transition:var(--transition);"
           onmouseover="this.style.background='var(--primary)';this.style.color='#fff'"
           onmouseout="this.style.background='var(--primary-light)';this.style.color='var(--primary-dark)'">
            <i class="fa-solid fa-clock"></i> Jam Operasional
        </a>
    </div>

    <!-- Recent Orders Table -->
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">📋 Daftar Pesanan Terbaru</h3>
            <a href="../pesanan/index.php" style="font-size:.8125rem; color:var(--primary); font-weight:700; text-decoration:none;">
                Lihat Semua →
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:580px;">
                <thead>
                    <tr style="text-align:left; border-bottom:2px solid var(--border);">
                        <th style="padding:.875rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted);">ID</th>
                        <th style="padding:.875rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted);">No. Meja</th>
                        <th style="padding:.875rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted);">Metode</th>
                        <th style="padding:.875rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted);">Status</th>
                        <th style="padding:.875rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted);">Total</th>
                        <th style="padding:.875rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted);">Waktu</th>
                        <th style="padding:.875rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted); text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="7" style="padding:2rem; text-align:center; color:var(--text-muted);">Belum ada pesanan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $i => $order):
                            [$bgColor, $txtColor] = match($order['status']) {
                                'Masuk'   => ['rgba(245,158,11,.12)', 'var(--warning)'],
                                'Proses'  => ['rgba(59,130,246,.12)', 'var(--info)'],
                                'Selesai' => ['rgba(16,185,129,.12)', 'var(--success)'],
                                default   => ['rgba(100,116,139,.12)', 'var(--text-muted)'],
                            };
                        ?>
                        <tr class="order-row" style="border-bottom:1px solid var(--border); animation-delay:<?= $i * 0.04 ?>s;"
                            onmouseenter="this.style.background='var(--bg-page)'" onmouseleave="this.style.background=''">
                            <td style="padding:.875rem 1rem; font-weight:700;">#ORD-<?= $order['id'] ?></td>
                            <td style="padding:.875rem 1rem; font-weight:600;"><?= htmlspecialchars($order['nomor_meja'] ?? '-') ?></td>
                            <td style="padding:.875rem 1rem;">
                                <span style="display:inline-flex;align-items:center;gap:.3rem;">
                                    <?= $order['metode_pembayaran'] === 'QRIS' ? '📲' : '💵' ?>
                                    <?= htmlspecialchars($order['metode_pembayaran'] ?? '-') ?>
                                </span>
                            </td>
                            <td style="padding:.875rem 1rem;">
                                <span class="status-badge" style="background:<?= $bgColor ?>; color:<?= $txtColor ?>;">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td style="padding:.875rem 1rem; font-weight:700; color:var(--primary);">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                            <td style="padding:.875rem 1rem; font-size:.8125rem; color:var(--text-muted);"><?= date('d/m H:i', strtotime($order['created_at'])) ?></td>
                            <td style="padding:.875rem 1rem; text-align:right;">
                                <a href="../pesanan/view.php?id=<?= $order['id'] ?>"
                                   style="display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;background:var(--primary-light);color:var(--primary-dark);padding:5px 14px;border-radius:var(--radius-sm);font-weight:700;font-size:.8rem;transition:var(--transition);"
                                   onmouseover="this.style.background='var(--primary)';this.style.color='#fff'"
                                   onmouseout="this.style.background='var(--primary-light)';this.style.color='var(--primary-dark)'">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
