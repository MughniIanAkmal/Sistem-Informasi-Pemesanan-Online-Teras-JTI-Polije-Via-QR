<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

// Fetch pesanan
$stmtOrder = $pdo->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmtOrder->execute([$id]);
$order = $stmtOrder->fetch();

if (!$order) {
    echo "<main class='main-content'><p style='padding:2rem'>Pesanan tidak ditemukan.</p></main>";
    include '../includes/footer.php';
    exit;
}

// Fetch items
$stmtItems = $pdo->prepare("
    SELECT dp.*, COALESCE(dp.nama_produk, pr.nama, 'Produk Terhapus') as nama, pr.harga as harga_satuan
    FROM detail_pesanan dp
    LEFT JOIN produk pr ON dp.produk_id = pr.id
    WHERE dp.pesanan_id = ?
");
$stmtItems->execute([$id]);
$items = $stmtItems->fetchAll();

// Handle POST — update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    $allowed = ['Masuk', 'Proses', 'Selesai'];
    if (in_array($newStatus, $allowed)) {
        $pdo->prepare("UPDATE pesanan SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
        header("Location: view.php?id=$id&updated=1");
        exit;
    }
}

// Quick confirm (Selesai) via GET
if (isset($_GET['confirm'])) {
    $pdo->prepare("UPDATE pesanan SET status = 'Selesai' WHERE id = ?")->execute([$id]);
    header("Location: view.php?id=$id&updated=1");
    exit;
}

[$bgColor, $txtColor] = match($order['status']) {
    'Masuk'   => ['rgba(245,158,11,.12)', 'var(--warning)'],
    'Proses'  => ['rgba(59,130,246,.12)', 'var(--info)'],
    'Selesai' => ['rgba(16,185,129,.12)', 'var(--success)'],
    default   => ['rgba(100,116,139,.12)', 'var(--text-muted)'],
};
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>🧾 Pesanan #ORD-<?= $order['id'] ?></h1>
            <p>Meja: <strong><?= htmlspecialchars($order['nomor_meja'] ?? '-') ?></strong> &bull; <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <a href="index.php" style="color:var(--text-muted);text-decoration:none;font-weight:600;font-size:.875rem;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div style="background:rgba(16,185,129,.1);color:var(--success);padding:1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;font-weight:600;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-circle-check"></i> Status pesanan berhasil diperbarui!
        </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:1.5rem; align-items:start;">

        <!-- Items -->
        <div class="content-card">
            <h3 class="card-title" style="margin-bottom:1.25rem;">🍽️ Item Pesanan</h3>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:2px solid var(--border);">
                        <th style="padding:.75rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted);">Produk</th>
                        <th style="padding:.75rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted); text-align:center;">Qty</th>
                        <th style="padding:.75rem 1rem; font-size:.75rem; text-transform:uppercase; color:var(--text-muted); text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:.75rem 1rem; font-weight:600;"><?= htmlspecialchars($item['nama']) ?></td>
                        <td style="padding:.75rem 1rem; text-align:center;">
                            <span style="background:var(--primary-light);color:var(--primary-dark);padding:3px 10px;border-radius:var(--radius-full);font-weight:700;font-size:.875rem;">
                                <?= $item['jumlah'] ?>×
                            </span>
                        </td>
                        <td style="padding:.75rem 1rem; text-align:right; font-weight:700;">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid var(--border); background:var(--bg-page);">
                        <td colspan="2" style="padding:1rem; font-weight:800;">TOTAL</td>
                        <td style="padding:1rem; text-align:right; font-weight:800; font-size:1.125rem; color:var(--primary);">
                            Rp <?= number_format($order['total_harga'], 0, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Info & Actions -->
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            <!-- Info -->
            <div class="content-card">
                <h3 class="card-title" style="margin-bottom:1rem;">📄 Informasi</h3>
                <div style="display:flex; flex-direction:column; gap:.75rem; font-size:.9rem;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);">No. Meja</span>
                        <strong><?= htmlspecialchars($order['nomor_meja'] ?? '-') ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);">Metode Bayar</span>
                        <strong><?= ($order['metode_pembayaran'] === 'QRIS' ? '📲 ' : '💵 ') . htmlspecialchars($order['metode_pembayaran'] ?? '-') ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);">Waktu</span>
                        <strong><?= date('H:i, d M Y', strtotime($order['created_at'])) ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);">Status</span>
                        <span class="status-badge" style="background:<?= $bgColor ?>; color:<?= $txtColor ?>;">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Confirm -->
            <?php if ($order['status'] !== 'Selesai'): ?>
            <div class="content-card" style="border:2px solid rgba(16,185,129,.3); background:rgba(16,185,129,.04);">
                <h3 class="card-title" style="margin-bottom:.75rem; color:var(--success);">✅ Konfirmasi Cepat</h3>
                <p style="font-size:.8125rem; color:var(--text-muted); margin-bottom:1rem; line-height:1.5;">
                    Tandai pesanan ini sebagai <strong>Selesai</strong> setelah pembayaran diterima.
                </p>
                <a href="view.php?id=<?= $id ?>&confirm=1"
                   class="btn-success" style="width:100%; justify-content:center; text-decoration:none; padding:.875rem; font-size:.9375rem;"
                   onclick="return confirm('Konfirmasi pesanan #ORD-<?= $id ?> sudah selesai dan bayar?')">
                    <i class="fa-solid fa-circle-check"></i> Selesaikan Pesanan
                </a>
            </div>
            <?php else: ?>
            <div class="content-card" style="border:2px solid rgba(16,185,129,.3); background:rgba(16,185,129,.06); text-align:center;">
                <div style="font-size:2.5rem; margin-bottom:.5rem;">🎉</div>
                <div style="font-weight:800; color:var(--success); font-size:1rem;">Pesanan Selesai</div>
                <div style="font-size:.8rem; color:var(--text-muted); margin-top:.25rem;">Transaksi telah tercatat di laporan admin.</div>
            </div>
            <?php endif; ?>

            <!-- Update Status -->
            <div class="content-card">
                <h3 class="card-title" style="margin-bottom:.875rem;">🔄 Ubah Status</h3>
                <form method="POST" style="display:flex; flex-direction:column; gap:.75rem;">
                    <select name="status" class="form-control" style="padding-left:1rem; appearance:auto;">
                        <option value="Masuk"   <?= $order['status']==='Masuk'   ? 'selected':'' ?>>🟡 Masuk</option>
                        <option value="Proses"  <?= $order['status']==='Proses'  ? 'selected':'' ?>>🔵 Diproses</option>
                        <option value="Selesai" <?= $order['status']==='Selesai' ? 'selected':'' ?>>🟢 Selesai</option>
                    </select>
                    <button type="submit" class="btn-primary" style="width:100%;">
                        <i class="fa-solid fa-floppy-disk" style="margin-right:.4rem;"></i>Simpan Status
                    </button>
                </form>
            </div>

        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
