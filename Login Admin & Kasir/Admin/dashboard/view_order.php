<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch pesanan
$stmtOrder = $pdo->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmtOrder->execute([$id]);
$order = $stmtOrder->fetch();

if (!$order) {
    echo "<main class='main-content'><p style='padding:2rem;'>Pesanan tidak ditemukan.</p></main>";
    include '../includes/footer.php';
    exit;
}

// Fetch detail items
$stmtItems = $pdo->prepare("
    SELECT dp.*, COALESCE(dp.nama_produk, pr.nama, 'Produk Terhapus') as nama, pr.harga as harga_satuan
    FROM detail_pesanan dp
    LEFT JOIN produk pr ON dp.produk_id = pr.id
    WHERE dp.pesanan_id = ?
");
$stmtItems->execute([$id]);
$items = $stmtItems->fetchAll();

// Handle status update removed (moved to Kasir)
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>Detail Pesanan #ORD-<?= $order['id'] ?></h1>
            <p>Meja: <strong><?= htmlspecialchars($order['nomor_meja'] ?? '-') ?></strong> &bull; <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <a href="index.php" style="color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div style="background: rgba(16,185,129,0.1); color: var(--success); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> Status pesanan berhasil diperbarui.
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">

        <!-- Items Table -->
        <div class="content-card">
            <h3 class="card-title" style="margin-bottom: 1.5rem;">Item Pesanan</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border);">
                        <th style="padding: 0.75rem 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Produk</th>
                        <th style="padding: 0.75rem 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); text-align: center;">Jumlah</th>
                        <th style="padding: 0.75rem 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 0.75rem 1rem; font-weight: 600;"><?= htmlspecialchars($item['nama']) ?></td>
                            <td style="padding: 0.75rem 1rem; text-align: center;"><?= $item['jumlah'] ?>x</td>
                            <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 700;">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px solid var(--border);">
                        <td colspan="2" style="padding: 1rem; font-weight: 800; font-size: 1rem;">TOTAL</td>
                        <td style="padding: 1rem; text-align: right; font-weight: 800; font-size: 1rem; color: var(--primary);">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Order Info & Status -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="content-card">
                <h3 class="card-title" style="margin-bottom: 1rem;">Informasi Pesanan</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">No. Meja</span>
                        <strong><?= htmlspecialchars($order['nomor_meja'] ?? '-') ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Metode Bayar</span>
                        <strong><?= htmlspecialchars($order['metode_pembayaran'] ?? '-') ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Waktu</span>
                        <strong><?= date('H:i, d M Y', strtotime($order['created_at'])) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-muted);">Status</span>
                        <?php
                            $statusColor = match($order['status']) {
                                'Masuk'   => 'rgba(245,158,11,0.15); color: var(--warning)',
                                'Proses'  => 'rgba(59,130,246,0.15); color: var(--info)',
                                'Selesai' => 'rgba(16,185,129,0.15); color: var(--success)',
                                default   => 'rgba(100,116,139,0.15); color: var(--text-muted)',
                            };
                        ?>
                        <span style="padding: 4px 12px; border-radius: var(--radius-full); background: <?= $statusColor ?>; font-size: 0.75rem; font-weight: 700;">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="content-card" style="background: rgba(100,116,139,0.05); border: 1px dashed var(--border);">
                <div style="font-size: 0.8125rem; color: var(--text-muted); text-align: center; padding: 1rem;">
                    <i class="fa-solid fa-lock" style="margin-bottom: 0.5rem; font-size: 1.25rem;"></i><br>
                    Pengubahan status pesanan saat ini dikelola melalui <strong>Panel Kasir</strong>.
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
