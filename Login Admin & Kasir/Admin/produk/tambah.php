<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $harga = $_POST['harga'] ?? 0;
    $kategori = $_POST['kategori'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $diskon = max(0, min(100, (int)($_POST['diskon'] ?? 0)));
    $is_promo = isset($_POST['is_promo']) ? 1 : 0;
    $gambar_path = '';

    // Handle File Upload
    if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar_file']['tmp_name'];
        $fileName = $_FILES['gambar_file']['name'];
        $fileSize = $_FILES['gambar_file']['size'];
        $fileType = $_FILES['gambar_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileSize < 2 * 1024 * 1024) { // Max 2MB
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                
                // Target directory (Relative to project root)
                $uploadPath = '../../../User/assets/img/produk/' . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                    $gambar_path = 'assets/img/produk/' . $newFileName;
                } else {
                    $error = "Terjadi kesalahan saat memindahkan file ke folder upload.";
                }
            } else {
                $error = "Ukuran file terlalu besar. Maksimal 2MB.";
            }
        } else {
            $error = "Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.";
        }
    }

    if ($nama && $harga && $kategori && !$error) {
        try {
            $stmt = $pdo->prepare("INSERT INTO produk (nama, harga, kategori, gambar, deskripsi, diskon, is_promo) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$nama, $harga, $kategori, $gambar_path, $deskripsi, $diskon, $is_promo]);
            
            if ($result) {
                $success = "Produk '" . htmlspecialchars($nama) . "' BERHASIL ditambahkan!";
            } else {
                $error = "Gagal menyimpan ke database.";
            }
        } catch (PDOException $e) {
            $error = "DATABASE ERROR: " . $e->getMessage();
        }
    } elseif (!$error) {
        $error = "Mohon isi semua field yang wajib.";
    }
}
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>Tambah Produk Baru</h1>
            <p>Masukkan detail produk makanan atau minuman.</p>
        </div>
        <a href="index.php" style="color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke List
        </a>
    </div>

    <div class="content-card" style="max-width: 600px;">
        <?php if ($success): ?>
            <div style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); color: var(--error); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
                <i class="fa-solid fa-circle-xmark"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Form with multipart/form-data for file uploads -->
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem;">Nama Produk *</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Nasi Goreng Spesial" style="padding-left: 1rem;" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem;">Harga (Rp) *</label>
                    <input type="number" name="harga" class="form-control" placeholder="25000" style="padding-left: 1rem;" required>
                </div>
                <div class="form-group">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem;">Kategori *</label>
                    <select name="kategori" class="form-control" style="padding-left: 1rem; appearance: auto;" required>
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem;">Diskon (%) <span style="color:var(--text-muted); font-weight:400;">opsional</span></label>
                    <input type="number" name="diskon" class="form-control" placeholder="0" style="padding-left: 1rem;" min="0" max="100" value="0">
                </div>
                <div class="form-group">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem;">Tampil di Promo Spesial</label>
                    <label style="display:inline-flex; align-items:center; gap:.75rem; cursor:pointer; margin-top:.35rem;">
                        <input type="checkbox" name="is_promo" style="width:18px; height:18px; accent-color:var(--success);">
                        <span style="font-weight:600; font-size:.875rem;">Aktifkan Promo</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem;">Upload Gambar Produk *</label>
                <div style="border: 2px dashed var(--border); padding: 1.5rem; border-radius: var(--radius-md); text-align: center; background: var(--bg-card);">
                    <input type="file" name="gambar_file" id="gambar_file" style="display: none;" accept="image/*" required onchange="previewImage(this)">
                    <label for="gambar_file" style="cursor: pointer; display: block;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"></i>
                        <p id="file-name" style="font-size: 0.875rem; color: var(--text-muted);">Klik untuk upload atau tarik gambar ke sini</p>
                    </label>
                    <img id="image-preview" style="display: none; width: 100%; max-height: 200px; object-fit: cover; border-radius: var(--radius-sm); margin-top: 1rem;">
                </div>
            </div>

            <div class="form-group">
                <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem;">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan singkat tentang produk..." style="padding-left: 1rem;"></textarea>
            </div>

            <button type="submit" class="btn-auth">Simpan Produk</button>
        </form>
    </div>
</main>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const labelText = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            labelText.textContent = input.files[0].name;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include '../includes/footer.php'; ?>
