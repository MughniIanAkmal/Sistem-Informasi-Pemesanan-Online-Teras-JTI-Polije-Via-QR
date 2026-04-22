<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

// Fetch Statistics safely
try {
    $stmtStats = $pdo->query("SELECT 
        COUNT(id) as total_orders, 
        COALESCE(SUM(total_harga), 0) as total_revenue,
        SUM(CASE WHEN status != 'Selesai' THEN 1 ELSE 0 END) as pending_orders
        FROM pesanan");
    $stats = $stmtStats->fetch();
} catch (PDOException $e) {
    $stats = ['total_orders' => 0, 'total_revenue' => 0, 'pending_orders' => 0];
}

// Fetch Total Produk
try {
    $totalProduk = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
} catch (PDOException $e) {
    $totalProduk = 0;
}

// Fetch Recent Orders
try {
    $stmtRecent = $pdo->query("SELECT * FROM pesanan ORDER BY created_at DESC LIMIT 10");
    $recentOrders = $stmtRecent->fetchAll();
} catch (PDOException $e) {
    $recentOrders = [];
}
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>Admin Dashboard</h1>
            <p>Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['admin_user']) ?></strong>.</p>
        </div>
        <div class="user-profile" style="display: flex; align-items: center; gap: 1rem;">
            <div style="text-align: right;">
                <div style="font-weight: 700; font-size: 0.875rem;">Administrator</div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">Online</div>
            </div>
            <div style="width: 44px; height: 44px; background: var(--primary-light); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: var(--primary-dark); font-weight: 800;">
                AD
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--primary-light); color: var(--primary-dark);">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Pesanan</div>
                <div class="value"><?= number_format($stats['total_orders'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Pendapatan</div>
                <div class="value">Rp <?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="label">Pesanan Masuk</div>
                <div class="value"><?= $stats['pending_orders'] ?? 0 ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--secondary);">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Produk</div>
                <div class="value"><?= $totalProduk ?></div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Pesanan Terbaru</h3>
            <a href="../laporan/harian.php" style="font-size: 0.8125rem; color: var(--primary); font-weight: 700; text-decoration: none;">Lihat Laporan</a>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border);">
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">ID Pesanan</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">No. Meja</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Metode Bayar</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Status</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Total</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Waktu</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted);">Belum ada pesanan masuk.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <?php
                                $statusColor = match($order['status']) {
                                    'Masuk'   => 'rgba(245,158,11,0.15); color: var(--warning)',
                                    'Proses'  => 'rgba(59,130,246,0.15); color: var(--info)',
                                    'Selesai' => 'rgba(16,185,129,0.15); color: var(--success)',
                                    default   => 'rgba(100,116,139,0.15); color: var(--text-muted)',
                                };
                            ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem; font-weight: 700;">#ORD-<?= $order['id'] ?></td>
                                <td style="padding: 1rem; font-weight: 600;"><?= htmlspecialchars($order['nomor_meja'] ?? '-') ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['metode_pembayaran'] ?? '-') ?></td>
                                <td style="padding: 1rem;">
                                    <span style="padding: 4px 12px; border-radius: var(--radius-full); background: <?= $statusColor ?>; font-size: 0.75rem; font-weight: 700;">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; font-weight: 700;">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                                <td style="padding: 1rem; font-size: 0.8125rem; color: var(--text-muted);"><?= date('d/m H:i', strtotime($order['created_at'])) ?></td>
                                <td style="padding: 1rem;">
                                    <a href="view_order.php?id=<?= $order['id'] ?>" style="text-decoration: none; border: none; background: var(--primary-light); color: var(--primary-dark); padding: 6px 14px; border-radius: var(--radius-sm); font-weight: 700; cursor: pointer; font-size: 0.8125rem; display: inline-block;">Detail</a>
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
