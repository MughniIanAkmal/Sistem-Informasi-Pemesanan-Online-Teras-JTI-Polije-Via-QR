<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

// ── Auto-migrate: add diskon & is_promo columns if missing ──
try {
    $existingCols = $pdo->query("SHOW COLUMNS FROM produk")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('diskon', $existingCols)) {
        $pdo->exec("ALTER TABLE produk ADD COLUMN diskon TINYINT(3) UNSIGNED NOT NULL DEFAULT 0");
    }
    if (!in_array('is_promo', $existingCols)) {
        $pdo->exec("ALTER TABLE produk ADD COLUMN is_promo TINYINT(1) NOT NULL DEFAULT 0");
    }
} catch (PDOException $e) { /* silently skip */ }

// ── Handle Delete ──
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $delId = (int)$_GET['delete'];
    try {
        // Get gambar path before delete
        $stmtImg = $pdo->prepare("SELECT gambar FROM produk WHERE id = ?");
        $stmtImg->execute([$delId]);
        $row = $stmtImg->fetch();
        if ($row && $row['gambar'] && strpos($row['gambar'], 'http') !== 0) {
            $oldFile = '../../../User/' . $row['gambar'];
            if (file_exists($oldFile)) @unlink($oldFile);
        }
        $pdo->prepare("DELETE FROM produk WHERE id = ?")->execute([$delId]);
    } catch (PDOException $e) { /* ignore */ }
    header('Location: index.php?deleted=1');
    exit;
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM produk ORDER BY created_at DESC");
$produk = $stmt->fetchAll();
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>List Produk</h1>
            <p>Kelola menu yang tampil di halaman pelanggan.</p>
        </div>
        <a href="tambah.php" class="btn-auth" style="text-decoration:none; width:auto; padding:.75rem 1.5rem;">
            <i class="fa-solid fa-plus" style="margin-right:.5rem;"></i> Tambah Produk
        </a>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div style="background:rgba(16,185,129,.1); color:var(--success); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem; font-weight:600;">
            <i class="fa-solid fa-circle-check"></i> Produk berhasil dihapus.
        </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem;">
        <?php
            $total     = count($produk);
            $promoCount = count(array_filter($produk, fn($p) => ($p['is_promo'] ?? 0) == 1));
            $discounted = count(array_filter($produk, fn($p) => ($p['diskon'] ?? 0) > 0));
        ?>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--primary-light);">📦</div>
            <div class="stat-info">
                <div class="label">Total Produk</div>
                <div class="value"><?= $total ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">🔥</div>
            <div class="stat-info">
                <div class="label">Promo Spesial Aktif</div>
                <div class="value"><?= $promoCount ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2;">🏷️</div>
            <div class="stat-info">
                <div class="label">Produk Ber-diskon</div>
                <div class="value"><?= $discounted ?></div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:2px solid var(--border);">
                        <th style="padding:1rem; font-size:.75rem; text-transform:uppercase;">Gambar</th>
                        <th style="padding:1rem; font-size:.75rem; text-transform:uppercase;">Nama Produk</th>
                        <th style="padding:1rem; font-size:.75rem; text-transform:uppercase;">Kategori</th>
                        <th style="padding:1rem; font-size:.75rem; text-transform:uppercase;">Harga</th>
                        <th style="padding:1rem; font-size:.75rem; text-transform:uppercase;">Diskon</th>
                        <th style="padding:1rem; font-size:.75rem; text-transform:uppercase;">Promo</th>
                        <th style="padding:1rem; font-size:.75rem; text-transform:uppercase; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produk)): ?>
                        <tr>
                            <td colspan="7" style="padding:2rem; text-align:center; color:var(--text-muted);">Belum ada produk.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produk as $p):
                            $imgPath     = htmlspecialchars($p['gambar'] ?? '');
                            $displayPath = ($imgPath && strpos($imgPath, 'http') === 0) ? $imgPath : '../../../User/' . $imgPath;
                            $diskon      = (int)($p['diskon'] ?? 0);
                            $isPromo     = (int)($p['is_promo'] ?? 0);
                            $hargaFinal  = $p['harga'] * (1 - $diskon / 100);
                        ?>
                        <tr style="border-bottom:1px solid var(--border); transition:background .15s;" onmouseenter="this.style.background='var(--bg-page)'" onmouseleave="this.style.background=''">
                            <td style="padding:1rem;">
                                <div style="position:relative; width:54px; height:54px;">
                                    <img src="<?= $displayPath ?>" alt="<?= htmlspecialchars($p['nama']) ?>"
                                         style="width:54px; height:54px; object-fit:cover; border-radius:var(--radius-sm); border:1px solid var(--border);">
                                    <?php if ($isPromo): ?>
                                        <span style="position:absolute; top:-4px; right:-4px; width:14px; height:14px; background:#f59e0b; border-radius:50%; border:2px solid #fff; font-size:.5rem; display:flex; align-items:center; justify-content:center;">🔥</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding:1rem; font-weight:700;"><?= htmlspecialchars($p['nama']) ?></td>
                            <td style="padding:1rem;">
                                <span style="padding:4px 10px; background:var(--primary-light); color:var(--primary-dark); border-radius:var(--radius-full); font-size:.75rem; font-weight:700;">
                                    <?= htmlspecialchars($p['kategori']) ?>
                                </span>
                            </td>
                            <td style="padding:1rem;">
                                <?php if ($diskon > 0): ?>
                                    <div style="font-size:.75rem; color:var(--text-muted); text-decoration:line-through;">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>
                                    <div style="font-weight:800; color:var(--primary);">Rp <?= number_format($hargaFinal, 0, ',', '.') ?></div>
                                <?php else: ?>
                                    <div style="font-weight:800; color:var(--primary);">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding:1rem;">
                                <?php if ($diskon > 0): ?>
                                    <span style="background:#fef2f2; color:#ef4444; padding:4px 10px; border-radius:var(--radius-full); font-size:.75rem; font-weight:800;">-<?= $diskon ?>%</span>
                                <?php else: ?>
                                    <span style="color:var(--text-muted); font-size:.85rem;">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:1rem;">
                                <?php if ($isPromo): ?>
                                    <span style="background:#fef3c7; color:#d97706; padding:4px 10px; border-radius:var(--radius-full); font-size:.75rem; font-weight:800;">🔥 Promo</span>
                                <?php else: ?>
                                    <span style="color:var(--text-muted); font-size:.85rem;">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:1rem; text-align:right; white-space:nowrap;">
                                <a href="edit.php?id=<?= $p['id'] ?>"
                                   style="display:inline-flex; align-items:center; gap:.35rem; padding:.4rem .9rem; border-radius:var(--radius-sm); background:var(--primary-light); color:var(--primary-dark); font-weight:700; font-size:.8125rem; text-decoration:none; transition:var(--transition);"
                                   onmouseover="this.style.background='var(--primary)';this.style.color='#fff';"
                                   onmouseout="this.style.background='var(--primary-light)';this.style.color='var(--primary-dark)';">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="javascript:void(0)"
                                   onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nama'])) ?>')"
                                   style="display:inline-flex; align-items:center; gap:.35rem; padding:.4rem .9rem; border-radius:var(--radius-sm); background:#fef2f2; color:var(--error); font-weight:700; font-size:.8125rem; text-decoration:none; margin-left:.25rem; transition:var(--transition);"
                                   onmouseover="this.style.background='var(--error)';this.style.color='#fff';"
                                   onmouseout="this.style.background='#fef2f2';this.style.color='var(--error)';">
                                    <i class="fa-solid fa-trash"></i> Hapus
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

<!-- Delete Confirmation Modal -->
<div id="delete-modal" style="display:none; position:fixed; inset:0; z-index:999; background:rgba(0,0,0,.5); backdrop-filter:blur(4px); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:var(--radius-lg); padding:2rem; max-width:400px; width:90%; box-shadow:var(--shadow-xl); text-align:center;">
        <div style="font-size:3rem; margin-bottom:1rem;">🗑️</div>
        <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:.5rem;">Hapus Produk?</h3>
        <p style="color:var(--text-muted); margin-bottom:1.5rem;">Produk "<strong id="delete-name"></strong>" akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</p>
        <div style="display:flex; gap:.75rem; justify-content:center;">
            <button onclick="closeDeleteModal()" style="padding:.75rem 1.5rem; border:2px solid var(--border); background:#fff; border-radius:var(--radius-md); font-weight:700; cursor:pointer;">
                Batal
            </button>
            <a id="delete-confirm-btn" href="#" style="padding:.75rem 1.5rem; background:var(--error); color:#fff; border-radius:var(--radius-md); font-weight:700; text-decoration:none; display:inline-block;">
                Ya, Hapus
            </a>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('delete-name').textContent = name;
    document.getElementById('delete-confirm-btn').href = 'index.php?delete=' + id;
    const modal = document.getElementById('delete-modal');
    modal.style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
}
// Close on overlay click
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

<?php include '../includes/footer.php'; ?>
