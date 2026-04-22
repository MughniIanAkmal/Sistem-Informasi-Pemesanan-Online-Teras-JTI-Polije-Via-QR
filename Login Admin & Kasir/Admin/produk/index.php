<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

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
        <a href="tambah.php" class="btn-auth" style="text-decoration: none; width: auto; padding: 0.75rem 1.5rem;">
            <i class="fa-solid fa-plus" style="margin-right: 0.5rem;"></i> Tambah Produk
        </a>
    </div>

    <div class="content-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border);">
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Gambar</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Nama Produk</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Kategori</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase;">Harga</th>
                        <th style="padding: 1rem; font-size: 0.75rem; text-transform: uppercase; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produk)): ?>
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">Belum ada produk.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produk as $p): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem;">
                                    <?php 
                                        $imgPath = htmlspecialchars($p['gambar']);
                                        // Check if it's a URL or a local path
                                        $displayPath = (strpos($imgPath, 'http') === 0) ? $imgPath : '../../../User/' . $imgPath;
                                    ?>
                                    <img src="<?= $displayPath ?>" alt="<?= htmlspecialchars($p['nama']) ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                </td>
                                <td style="padding: 1rem; font-weight: 700;"><?= htmlspecialchars($p['nama']) ?></td>
                                <td style="padding: 1rem;">
                                    <span style="padding: 4px 10px; background: var(--primary-light); color: var(--primary-dark); border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 700;">
                                        <?= htmlspecialchars($p['kategori']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; font-weight: 800; color: var(--primary);">Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                <td style="padding: 1rem; text-align: right;">
                                    <button style="border: none; background: none; color: var(--text-muted); cursor: pointer; margin-right: 0.5rem;"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button style="border: none; background: none; color: var(--error); cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
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
