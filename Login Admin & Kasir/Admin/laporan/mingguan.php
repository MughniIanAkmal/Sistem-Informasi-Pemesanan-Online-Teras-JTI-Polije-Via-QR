<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

// Calculate current week range
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));

$start_date = $_GET['start'] ?? $monday;
$end_date = $_GET['end'] ?? $sunday;

// Fetch weekly report data with LEFT JOIN
$stmt = $pdo->prepare("
    SELECT DATE(p.created_at) as tgl, SUM(p.total_harga) as harian_total, COUNT(p.id) as trx_count
    FROM pesanan p
    WHERE DATE(p.created_at) BETWEEN ? AND ?
    GROUP BY DATE(p.created_at)
    ORDER BY DATE(p.created_at) DESC
");
$stmt->execute([$start_date, $end_date]);
$summaries = $stmt->fetchAll();

$total_revenue = 0;
foreach ($summaries as $s) { $total_revenue += $s['harian_total']; }
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>Laporan Mingguan</h1>
            <p>Rentang: <strong><?= date('d M', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></strong></p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <form action="" method="GET" style="display: flex; gap: 0.5rem;">
                <input type="date" name="start" class="form-control" value="<?= $start_date ?>" style="padding-left: 1rem; width: auto;">
                <span style="align-self: center;">s/d</span>
                <input type="date" name="end" class="form-control" value="<?= $end_date ?>" style="padding-left: 1rem; width: auto;">
                <button type="submit" class="btn-auth" style="width: auto; margin-top: 0; padding: 0.5rem 1rem;">Filter</button>
            </form>
            <a href="export.php?type=mingguan&start=<?= $start_date ?>&end=<?= $end_date ?>" class="btn-auth" style="text-decoration: none; width: auto; background: var(--success); margin-top: 0; padding: 0.75rem 1.5rem;">
                <i class="fa-solid fa-file-excel"></i> Download
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                <i class="fa-solid fa-chart-bar"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Pendapatan Minggu Ini</div>
                <div class="value">Rp <?= number_format($total_revenue, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <h3 class="card-title">Ringkasan Harian</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border);">
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Tanggal</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Total Transaksi</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($summaries)): ?>
                        <tr><td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-muted);">Tidak ada data.</td></tr>
                    <?php else: ?>
                        <?php foreach ($summaries as $s): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem; font-weight: 700;"><?= date('l, d M Y', strtotime($s['tgl'])) ?></td>
                                <td style="padding: 1rem;"><?= $s['trx_count'] ?> transaksi</td>
                                <td style="padding: 1rem; font-weight: 800; color: var(--success);">Rp <?= number_format($s['harian_total'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
