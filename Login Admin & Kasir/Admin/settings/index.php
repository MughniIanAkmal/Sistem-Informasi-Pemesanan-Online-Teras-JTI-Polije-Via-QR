<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

$success = '';
$error   = '';

// Fetch current settings
function getSetting($pdo, $key, $default = '') {
    try {
        $s = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $s->execute([$key]);
        $row = $s->fetch();
        return $row ? $row['value'] : $default;
    } catch (PDOException $e) { return $default; }
}

// Handle form save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fields = ['jam_buka', 'jam_tutup', 'pesan_tutup', 'nama_toko', 'tagline_toko'];
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");

        foreach ($fields as $f) {
            $val = $_POST[$f] ?? '';
            $stmt->execute([$f, $val, $val]);
        }

        $tutupStatus = isset($_POST['toko_tutup']) ? '1' : '0';
        $stmt->execute(['toko_tutup', $tutupStatus, $tutupStatus]);

        // Handle checkboxes for hari_buka (0=Sun, 1=Mon, 6=Sat)
        $hariSelected = $_POST['hari_buka'] ?? [];
        $hariStr = implode(',', array_map('intval', $hariSelected));
        if (empty($hariStr)) $hariStr = ''; // all closed
        $stmt->execute(['hari_buka', $hariStr, $hariStr]);

        $success = 'Pengaturan berhasil disimpan!';
    } catch (PDOException $e) {
        $error = 'Gagal menyimpan: ' . $e->getMessage();
    }
}

$jamBuka    = getSetting($pdo, 'jam_buka', '07:00');
$jamTutup   = getSetting($pdo, 'jam_tutup', '22:00');
$pesanTutup = getSetting($pdo, 'pesan_tutup', 'Teras JTI sedang tutup. Kami akan segera kembali!');
$namaToko   = getSetting($pdo, 'nama_toko', 'Teras JTI');
$tagline    = getSetting($pdo, 'tagline_toko', 'Pesan menu favoritmu sekarang');
$tokoTutup  = getSetting($pdo, 'toko_tutup', '0');
$hariBuka   = explode(',', getSetting($pdo, 'hari_buka', '0,1,2,3,4,5,6'));

$hariLabels = ['0' => 'Minggu', '1' => 'Senin', '2' => 'Selasa', '3' => 'Rabu', '4' => 'Kamis', '5' => 'Jumat', '6' => 'Sabtu'];
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>⚙️ Pengaturan Toko</h1>
            <p>Atur jam operasional, hari buka, dan informasi toko.</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div style="background:rgba(16,185,129,0.1);color:var(--success);padding:1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;font-weight:600;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-circle-check"></i> <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div style="background:rgba(239,68,68,0.1);color:var(--error);padding:1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;font-weight:600;">
            <i class="fa-solid fa-circle-xmark"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start;">

            <!-- Left: Hours & Status -->
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                <!-- Force Close Toggle -->
                <div class="content-card">
                    <h3 class="card-title" style="margin-bottom:1.25rem;">🔒 Status Toko</h3>
                    <label style="display:flex;align-items:center;gap:12px;cursor:pointer;padding:16px;background:<?= $tokoTutup=='1' ? 'rgba(239,68,68,0.08)' : 'rgba(16,185,129,0.08)' ?>;border-radius:var(--radius-md);border:2px solid <?= $tokoTutup=='1' ? 'rgba(239,68,68,0.2)' : 'rgba(16,185,129,0.2)' ?>;transition:all 0.2s;" id="status-label">
                        <div style="position:relative;">
                            <input type="checkbox" name="toko_tutup" value="1" id="toko_tutup" <?= $tokoTutup=='1' ? 'checked' : '' ?> onchange="updateStatusLabel(this)" style="opacity:0;position:absolute;width:0;">
                            <div id="toggle-visual" style="width:52px;height:28px;border-radius:14px;background:<?= $tokoTutup=='1' ? 'var(--error)' : 'var(--success)' ?>;position:relative;transition:background 0.2s;cursor:pointer;">
                                <div style="position:absolute;top:3px;<?= $tokoTutup=='1' ? 'right:3px' : 'left:3px' ?>;width:22px;height:22px;border-radius:50%;background:white;transition:all 0.2s;" id="toggle-knob"></div>
                            </div>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:0.9rem;" id="status-text"><?= $tokoTutup=='1' ? '🔴 Toko Sedang TUTUP' : '🟢 Toko Sedang BUKA' ?></div>
                            <div style="font-size:0.75rem;color:var(--text-muted);">Aktifkan untuk menutup toko secara paksa</div>
                        </div>
                    </label>
                </div>

                <!-- Jam Operasional -->
                <div class="content-card">
                    <h3 class="card-title" style="margin-bottom:1.25rem;">🕐 Jam Operasional</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div class="form-group">
                            <label style="display:block;font-weight:700;margin-bottom:0.5rem;font-size:0.875rem;">Jam Buka</label>
                            <input type="time" name="jam_buka" class="form-control" value="<?= $jamBuka ?>" style="padding-left:1rem;">
                        </div>
                        <div class="form-group">
                            <label style="display:block;font-weight:700;margin-bottom:0.5rem;font-size:0.875rem;">Jam Tutup</label>
                            <input type="time" name="jam_tutup" class="form-control" value="<?= $jamTutup ?>" style="padding-left:1rem;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:1rem;">
                        <label style="display:block;font-weight:700;margin-bottom:0.5rem;font-size:0.875rem;">Pesan Saat Tutup</label>
                        <textarea name="pesan_tutup" class="form-control" rows="2" style="padding-left:1rem;"><?= htmlspecialchars($pesanTutup) ?></textarea>
                    </div>
                </div>

                <!-- Hari Buka -->
                <div class="content-card">
                    <h3 class="card-title" style="margin-bottom:1.25rem;">📅 Hari Beroperasi</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <?php foreach ($hariLabels as $val => $label): ?>
                            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 14px;border:2px solid var(--border);border-radius:var(--radius-md);transition:all 0.2s;<?= in_array($val, $hariBuka) ? 'background:var(--primary-light);border-color:var(--primary);' : '' ?>">
                                <input type="checkbox" name="hari_buka[]" value="<?= $val ?>" <?= in_array($val, $hariBuka) ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:var(--primary);">
                                <span style="font-weight:700;font-size:0.875rem;"><?= $label ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Branding -->
            <div class="content-card">
                <h3 class="card-title" style="margin-bottom:1.25rem;">🏪 Informasi Toko</h3>
                <div class="form-group">
                    <label style="display:block;font-weight:700;margin-bottom:0.5rem;font-size:0.875rem;">Nama Toko</label>
                    <input type="text" name="nama_toko" class="form-control" value="<?= htmlspecialchars($namaToko) ?>" style="padding-left:1rem;">
                </div>
                <div class="form-group">
                    <label style="display:block;font-weight:700;margin-bottom:0.5rem;font-size:0.875rem;">Tagline</label>
                    <input type="text" name="tagline_toko" class="form-control" value="<?= htmlspecialchars($tagline) ?>" style="padding-left:1rem;">
                </div>

                <!-- Preview -->
                <div style="margin-top:1.5rem;background:linear-gradient(135deg,#2E7D32,#4CAF50);border-radius:var(--radius-md);padding:1.5rem;color:white;text-align:center;">
                    <div style="font-size:0.75rem;opacity:0.8;margin-bottom:4px;">Preview Header User</div>
                    <div style="font-size:1.25rem;font-weight:900;letter-spacing:0.5px;" id="preview-nama"><?= htmlspecialchars($namaToko) ?></div>
                    <div style="font-size:0.8rem;opacity:0.85;margin-top:4px;" id="preview-tagline"><?= htmlspecialchars($tagline) ?></div>
                </div>

                <button type="submit" class="btn-auth" style="margin-top:1.5rem;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
                </button>
            </div>

        </div>
    </form>
</main>

<script>
function updateStatusLabel(cb) {
    const label   = document.getElementById('status-label');
    const text    = document.getElementById('status-text');
    const toggle  = document.getElementById('toggle-visual');
    const knob    = document.getElementById('toggle-knob');
    if (cb.checked) {
        label.style.background   = 'rgba(239,68,68,0.08)';
        label.style.borderColor  = 'rgba(239,68,68,0.2)';
        text.textContent         = '🔴 Toko Sedang TUTUP';
        toggle.style.background  = 'var(--error)';
        knob.style.left = 'auto'; knob.style.right = '3px';
    } else {
        label.style.background   = 'rgba(16,185,129,0.08)';
        label.style.borderColor  = 'rgba(16,185,129,0.2)';
        text.textContent         = '🟢 Toko Sedang BUKA';
        toggle.style.background  = 'var(--success)';
        knob.style.right = 'auto'; knob.style.left = '3px';
    }
}

// Live preview
document.querySelector('[name="nama_toko"]').addEventListener('input', function() {
    document.getElementById('preview-nama').textContent = this.value;
});
document.querySelector('[name="tagline_toko"]').addEventListener('input', function() {
    document.getElementById('preview-tagline').textContent = this.value;
});

// Highlight checkbox labels
document.querySelectorAll('input[name="hari_buka[]"]').forEach(cb => {
    cb.addEventListener('change', function() {
        const lbl = this.closest('label');
        if (this.checked) {
            lbl.style.background   = 'var(--primary-light)';
            lbl.style.borderColor  = 'var(--primary)';
        } else {
            lbl.style.background   = '';
            lbl.style.borderColor  = 'var(--border)';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
