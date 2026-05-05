<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

$success = '';
$error = '';

// Validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Auto-migrate: add columns if not exist
try {
    $cols = $pdo->query("SHOW COLUMNS FROM produk")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('diskon', $cols)) {
        $pdo->exec("ALTER TABLE produk ADD COLUMN diskon TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Persentase diskon 0-100'");
    }
    if (!in_array('is_promo', $cols)) {
        $pdo->exec("ALTER TABLE produk ADD COLUMN is_promo TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = tampil di promo spesial'");
    }
} catch (PDOException $e) {
    // silently continue
}

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    header('Location: index.php');
    exit;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $harga     = (int)($_POST['harga'] ?? 0);
    $kategori  = $_POST['kategori'] ?? '';
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $diskon    = max(0, min(100, (int)($_POST['diskon'] ?? 0)));
    $is_promo  = isset($_POST['is_promo']) ? 1 : 0;
    $gambar_path = $produk['gambar']; // keep existing by default

    // Handle new file upload
    if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath    = $_FILES['gambar_file']['tmp_name'];
        $fileName       = $_FILES['gambar_file']['name'];
        $fileSize       = $_FILES['gambar_file']['size'];
        $fileExtension  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts    = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($fileExtension, $allowedExts)) {
            $error = "Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $error = "Ukuran file terlalu besar. Maksimal 2MB.";
        } else {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadPath  = '../../../User/assets/img/produk/' . $newFileName;
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                // Delete old file if it's a local file
                if ($produk['gambar'] && strpos($produk['gambar'], 'http') !== 0) {
                    $oldPath = '../../../User/' . $produk['gambar'];
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $gambar_path = 'assets/img/produk/' . $newFileName;
            } else {
                $error = "Gagal memindahkan file ke folder upload.";
            }
        }
    }

    if ($nama && $harga && $kategori && !$error) {
        try {
            $stmt = $pdo->prepare("UPDATE produk SET nama=?, harga=?, kategori=?, gambar=?, deskripsi=?, diskon=?, is_promo=? WHERE id=?");
            $stmt->execute([$nama, $harga, $kategori, $gambar_path, $deskripsi, $diskon, $is_promo, $id]);
            $success = "Produk berhasil diperbarui!";
            // Refresh data
            $stmt2 = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
            $stmt2->execute([$id]);
            $produk = $stmt2->fetch();
        } catch (PDOException $e) {
            $error = "DATABASE ERROR: " . $e->getMessage();
        }
    } elseif (!$error) {
        $error = "Mohon isi semua field yang wajib.";
    }
}

// Computed price after discount
$harga_diskon = $produk['harga'] * (1 - ($produk['diskon'] / 100));
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>Edit Produk</h1>
            <p>Ubah detail, harga, gambar, atau diskon produk.</p>
        </div>
        <a href="index.php" style="color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke List
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">

        <!-- ── FORM UTAMA ── -->
        <div class="content-card">
            <?php if ($success): ?>
                <div style="background: rgba(16,185,129,0.1); color: var(--success); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600; display:flex; align-items:center; gap:.5rem;">
                    <i class="fa-solid fa-circle-check"></i> <?= $success ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background: rgba(239,68,68,0.1); color: var(--error); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600; display:flex; align-items:center; gap:.5rem;">
                    <i class="fa-solid fa-circle-xmark"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <!-- Nama -->
                <div class="form-group">
                    <label style="display:block; font-weight:700; margin-bottom:.5rem; font-size:.875rem;">Nama Produk *</label>
                    <input type="text" name="nama" class="form-control" style="padding-left:1rem;"
                           value="<?= htmlspecialchars($produk['nama']) ?>" required>
                </div>

                <!-- Harga & Kategori -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label style="display:block; font-weight:700; margin-bottom:.5rem; font-size:.875rem;">Harga (Rp) *</label>
                        <input type="number" name="harga" class="form-control" style="padding-left:1rem;"
                               value="<?= $produk['harga'] ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label style="display:block; font-weight:700; margin-bottom:.5rem; font-size:.875rem;">Kategori *</label>
                        <select name="kategori" class="form-control" style="padding-left:1rem; appearance:auto;" required>
                            <option value="makanan"  <?= $produk['kategori']==='makanan'  ? 'selected':'' ?>>Makanan</option>
                            <option value="minuman"  <?= $produk['kategori']==='minuman'  ? 'selected':'' ?>>Minuman</option>
                            <option value="snack"    <?= $produk['kategori']==='snack'    ? 'selected':'' ?>>Snack</option>
                        </select>
                    </div>
                </div>

                <!-- Diskon & Promo -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label style="display:block; font-weight:700; margin-bottom:.5rem; font-size:.875rem;">
                            Diskon (%) <span style="color:var(--text-muted); font-weight:500;">0 = tanpa diskon</span>
                        </label>
                        <div style="position:relative; display:flex; align-items:center;">
                            <input type="number" name="diskon" id="diskon-input" class="form-control" style="padding-left:1rem;"
                                   value="<?= $produk['diskon'] ?? 0 ?>" min="0" max="100"
                                   oninput="updatePreview()">
                            <span style="position:absolute; right:1rem; color:var(--text-muted); font-weight:700;">%</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="display:block; font-weight:700; margin-bottom:.5rem; font-size:.875rem;">
                            Tampil di Promo Spesial
                        </label>
                        <label class="promo-toggle">
                            <input type="checkbox" name="is_promo" id="is-promo-check"
                                   <?= ($produk['is_promo'] ?? 0) ? 'checked' : '' ?>
                                   onchange="updatePreview()">
                            <span class="toggle-slider"></span>
                            <span class="toggle-label" id="promo-label"><?= ($produk['is_promo'] ?? 0) ? 'Aktif' : 'Nonaktif' ?></span>
                        </label>
                    </div>
                </div>

                <!-- Upload Gambar -->
                <div class="form-group">
                    <label style="display:block; font-weight:700; margin-bottom:.5rem; font-size:.875rem;">Ganti Gambar Produk</label>
                    <div style="border:2px dashed var(--border); padding:1.5rem; border-radius:var(--radius-md); text-align:center; background:var(--bg-card);">
                        <input type="file" name="gambar_file" id="gambar_file" style="display:none;" accept="image/*" onchange="previewImage(this)">
                        <label for="gambar_file" style="cursor:pointer; display:block;">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size:2rem; color:var(--primary); margin-bottom:.5rem;"></i>
                            <p id="file-name" style="font-size:.875rem; color:var(--text-muted);">Klik untuk upload gambar baru (opsional)</p>
                        </label>
                        <img id="image-preview" style="display:none; width:100%; max-height:200px; object-fit:cover; border-radius:var(--radius-sm); margin-top:1rem;">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="form-group">
                    <label style="display:block; font-weight:700; margin-bottom:.5rem; font-size:.875rem;">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" style="padding-left:1rem;"
                              placeholder="Penjelasan singkat tentang produk..."><?= htmlspecialchars($produk['deskripsi'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fa-solid fa-floppy-disk" style="margin-right:.5rem;"></i>Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- ── PREVIEW CARD ── -->
        <div>
            <div class="content-card" style="position:sticky; top:2rem;">
                <div style="font-weight:700; font-size:.875rem; margin-bottom:1rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">
                    Preview Produk
                </div>

                <!-- Gambar saat ini -->
                <?php
                    $imgPath = htmlspecialchars($produk['gambar'] ?? '');
                    $displayPath = ($imgPath && strpos($imgPath, 'http') === 0) ? $imgPath : '../../../User/' . $imgPath;
                ?>
                <div style="position:relative; border-radius:var(--radius-md); overflow:hidden; background:#f1f5f9; aspect-ratio:16/9; margin-bottom:1rem;">
                    <img id="preview-img"
                         src="<?= $displayPath ?>"
                         alt="<?= htmlspecialchars($produk['nama']) ?>"
                         style="width:100%; height:100%; object-fit:cover;">
                    <div id="promo-badge-preview" style="
                        display: <?= ($produk['is_promo'] ?? 0) ? 'block' : 'none' ?>;
                        position:absolute; top:.75rem; right:.75rem;
                        background: linear-gradient(135deg,#f59e0b,#ef4444);
                        color:#fff; font-size:.7rem; font-weight:800;
                        padding:4px 10px; border-radius:9999px; letter-spacing:.05em; text-transform:uppercase;">
                        🔥 PROMO
                    </div>
                </div>

                <div style="font-weight:700; font-size:1.1rem; margin-bottom:.25rem;" id="preview-name">
                    <?= htmlspecialchars($produk['nama']) ?>
                </div>
                <div id="preview-price-wrap" style="display:flex; align-items:center; gap:.5rem; flex-wrap:wrap;">
                    <span id="preview-price-original"
                          style="font-size:.875rem; color:var(--text-muted); text-decoration:line-through;
                                 display:<?= ($produk['diskon'] ?? 0) > 0 ? 'inline' : 'none' ?>;">
                        Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                    </span>
                    <span id="preview-price-final" style="font-weight:800; color:var(--primary); font-size:1.25rem;">
                        Rp <?= number_format($harga_diskon, 0, ',', '.') ?>
                    </span>
                    <span id="preview-discount-badge"
                          style="background:#fef2f2; color:#ef4444; font-size:.7rem; font-weight:800;
                                 padding:2px 8px; border-radius:9999px;
                                 display:<?= ($produk['diskon'] ?? 0) > 0 ? 'inline' : 'none' ?>;">
                        -<?= $produk['diskon'] ?? 0 ?>%
                    </span>
                </div>

                <div style="margin-top:1rem; padding:1rem; background:var(--bg-page); border-radius:var(--radius-sm); font-size:.8125rem; color:var(--text-muted); line-height:1.6;">
                    <div><i class="fa-solid fa-tag" style="width:16px;"></i> Kategori: <strong><?= htmlspecialchars($produk['kategori']) ?></strong></div>
                    <div id="preview-promo-status" style="margin-top:.25rem;">
                        <i class="fa-solid fa-fire" style="width:16px; color:<?= ($produk['is_promo'] ?? 0) ? '#f59e0b' : 'var(--border)' ?>;"></i>
                        Promo Spesial:
                        <strong style="color:<?= ($produk['is_promo'] ?? 0) ? 'var(--success)' : 'var(--text-muted)' ?>">
                            <?= ($produk['is_promo'] ?? 0) ? 'Aktif' : 'Nonaktif' ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* Toggle Switch */
.promo-toggle {
    display: inline-flex;
    align-items: center;
    gap: .75rem;
    cursor: pointer;
    margin-top: .35rem;
}
.promo-toggle input { display: none; }
.toggle-slider {
    width: 48px; height: 26px;
    background: var(--border);
    border-radius: 9999px;
    position: relative;
    transition: background .3s;
    flex-shrink: 0;
}
.toggle-slider::after {
    content: '';
    position: absolute;
    width: 20px; height: 20px;
    background: #fff;
    border-radius: 50%;
    top: 3px; left: 3px;
    transition: transform .3s;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.promo-toggle input:checked ~ .toggle-slider {
    background: var(--success);
}
.promo-toggle input:checked ~ .toggle-slider::after {
    transform: translateX(22px);
}
.toggle-label { font-weight: 700; font-size: .875rem; }
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const mainPreview = document.getElementById('preview-img');
    const label = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            mainPreview.src = e.target.result;
            label.textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updatePreview() {
    const diskon  = parseInt(document.getElementById('diskon-input').value) || 0;
    const isPromo = document.getElementById('is-promo-check').checked;
    const harga   = parseFloat('<?= $produk['harga'] ?>');

    // Price display
    const final = harga * (1 - diskon / 100);
    document.getElementById('preview-price-final').textContent =
        'Rp ' + Math.round(final).toLocaleString('id-ID');

    const origEl = document.getElementById('preview-price-original');
    const badgeEl = document.getElementById('preview-discount-badge');
    origEl.style.display  = diskon > 0 ? 'inline' : 'none';
    badgeEl.style.display = diskon > 0 ? 'inline' : 'none';
    badgeEl.textContent   = '-' + diskon + '%';
    origEl.textContent    = 'Rp ' + Math.round(harga).toLocaleString('id-ID');

    // Promo badge
    document.getElementById('promo-badge-preview').style.display = isPromo ? 'block' : 'none';

    // Promo label
    const promoLabel = document.getElementById('promo-label');
    promoLabel.textContent = isPromo ? 'Aktif' : 'Nonaktif';
}
</script>

<?php include '../includes/footer.php'; ?>
