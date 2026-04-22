<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

$date = $_GET['date'] ?? date('Y-m-d');

// Fetch daily report data with LEFT JOIN to prevent empty reports if product links have issues
$stmt = $pdo->prepare("
    SELECT p.id, p.total_harga, p.metode_pembayaran, p.created_at,
           GROUP_CONCAT(CONCAT(IFNULL(pr.nama, 'Produk Terhapus'), ' (', dp.jumlah, ')') SEPARATOR ', ') as produk_list
    FROM pesanan p
    LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
    LEFT JOIN produk pr ON dp.produk_id = pr.id
    WHERE DATE(p.created_at) = ?
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$stmt->execute([$date]);
$transactions = $stmt->fetchAll();

// Summaries
$total_revenue = 0;
foreach ($transactions as $t) { $total_revenue += $t['total_harga']; }
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>Laporan Harian</h1>
            <p>Data penjualan untuk tanggal <strong><?= date('d M Y', strtotime($date)) ?></strong></p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <form action="" method="GET" style="display: flex; gap: 0.5rem;">
                <input type="date" name="date" class="form-control" value="<?= $date ?>" style="padding-left: 1rem; width: auto;">
                <button type="submit" class="btn-auth" style="width: auto; margin-top: 0; padding: 0.5rem 1rem;">Filter</button>
            </form>
            <a href="export.php?type=harian&date=<?= $date ?>" class="btn-auth" style="text-decoration: none; width: auto; background: var(--success); margin-top: 0; padding: 0.75rem 1.5rem;">
                <i class="fa-solid fa-file-excel"></i> Download Excel
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Pendapatan</div>
                <div class="value">Rp <?= number_format($total_revenue, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info);">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Transaksi</div>
                <div class="value"><?= count($transactions) ?></div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">Rincian Transaksi</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border);">
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Waktu</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">ID Pesanan</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Produk (Jumlah)</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-muted);">Tidak ada data untuk tanggal ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem;"><?= date('H:i', strtotime($t['created_at'])) ?></td>
                                <td style="padding: 1rem; font-weight: 700;">#ORD-<?= $t['id'] ?></td>
                                <td style="padding: 1rem; font-size: 0.875rem;"><?= htmlspecialchars($t['produk_list']) ?></td>
                                <td style="padding: 1rem; font-weight: 800; color: var(--success);">Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
