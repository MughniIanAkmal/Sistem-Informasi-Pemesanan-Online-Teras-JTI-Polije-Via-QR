<?php
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../includes/db.php';

$success = '';
$error   = '';

function getSetting($pdo, $key, $default = '') {
    try {
        $s = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $s->execute([$key]);
        $row = $s->fetch();
        return $row ? $row['value'] : $default;
    } catch (PDOException $e) { return $default; }
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fields = ['jam_buka', 'jam_tutup', 'pesan_tutup'];
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");

        foreach ($fields as $f) {
            $val = $_POST[$f] ?? '';
            $stmt->execute([$f, $val, $val]);
        }

        $tutupStatus = isset($_POST['toko_tutup']) ? '1' : '0';
        $stmt->execute(['toko_tutup', $tutupStatus, $tutupStatus]);

        $hariSelected = $_POST['hari_buka'] ?? [];
        $hariStr = implode(',', array_map('intval', $hariSelected));
        $stmt->execute(['hari_buka', $hariStr, $hariStr]);

        $success = 'Jam operasional berhasil disimpan!';
    } catch (PDOException $e) {
        $error = 'Gagal menyimpan: ' . $e->getMessage();
    }
}

$jamBuka    = getSetting($pdo, 'jam_buka', '07:00');
$jamTutup   = getSetting($pdo, 'jam_tutup', '22:00');
$pesanTutup = getSetting($pdo, 'pesan_tutup', 'Teras JTI sedang tutup. Kami akan segera kembali!');
$tokoTutup  = getSetting($pdo, 'toko_tutup', '0');
$hariBuka   = explode(',', getSetting($pdo, 'hari_buka', '0,1,2,3,4,5,6'));

$hariLabels = ['0'=>'Minggu','1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'];

// Current time info
$now    = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$buka   = new DateTime($jamBuka,   new DateTimeZone('Asia/Jakarta'));
$tutup  = new DateTime($jamTutup,  new DateTimeZone('Asia/Jakarta'));
$isOpen = $tokoTutup !== '1' && $now >= $buka && $now <= $tutup && in_array($now->format('w'), $hariBuka);
?>

<main class="main-content">
    <div class="header-top">
        <div class="page-title">
            <h1>🕐 Jam Operasional</h1>
            <p>Atur jam buka, hari beroperasi, dan status toko.</p>
        </div>
        <!-- Live Status Indicator -->
        <div style="display:flex;align-items:center;gap:.6rem;padding:.6rem 1.25rem;border-radius:var(--radius-full);
                    background:<?= $isOpen ? 'rgba(16,185,129,.12)' : 'rgba(239,68,68,.1)' ?>;
                    border:2px solid <?= $isOpen ? 'rgba(16,185,129,.3)' : 'rgba(239,68,68,.25)' ?>;">
            <span style="width:10px;height:10px;border-radius:50%;background:<?= $isOpen ? 'var(--success)' : 'var(--error)' ?>;
                         box-shadow:0 0 0 3px <?= $isOpen ? 'rgba(16,185,129,.2)' : 'rgba(239,68,68,.2)' ?>;
                         animation:pulse 2s infinite;"></span>
            <span style="font-weight:700;font-size:.875rem;color:<?= $isOpen ? 'var(--success)' : 'var(--error)' ?>;">
                <?= $isOpen ? 'Toko Sedang BUKA' : 'Toko Sedang TUTUP' ?>
            </span>
        </div>
    </div>

    <?php if ($success): ?>
        <div style="background:rgba(16,185,129,.1);color:var(--success);padding:1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;font-weight:600;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-circle-check"></i> <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div style="background:rgba(239,68,68,.1);color:var(--error);padding:1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;font-weight:600;">
            <i class="fa-solid fa-circle-xmark"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start;">

            <!-- Left Column -->
            <div style="display:flex; flex-direction:column; gap:1.5rem;">

                <!-- Status Toggle -->
                <div class="content-card">
                    <h3 class="card-title" style="margin-bottom:1.25rem;">🔒 Status Toko</h3>
                    <label id="status-label" style="display:flex;align-items:center;gap:14px;cursor:pointer;padding:16px;
                        background:<?= $tokoTutup=='1' ? 'rgba(239,68,68,.08)' : 'rgba(16,185,129,.08)' ?>;
                        border-radius:var(--radius-md);border:2px solid <?= $tokoTutup=='1' ? 'rgba(239,68,68,.2)' : 'rgba(16,185,129,.2)' ?>;
                        transition:all .2s;">
                        <div style="position:relative;">
                            <input type="checkbox" name="toko_tutup" value="1" id="toko_tutup"
                                   <?= $tokoTutup=='1' ? 'checked':'' ?> onchange="updateStatusLabel(this)"
                                   style="opacity:0;position:absolute;width:0;">
                            <div id="toggle-visual" style="width:52px;height:28px;border-radius:14px;
                                background:<?= $tokoTutup=='1' ? 'var(--error)' : 'var(--success)' ?>;
                                position:relative;transition:background .2s;cursor:pointer;">
                                <div id="toggle-knob" style="position:absolute;top:3px;
                                    <?= $tokoTutup=='1' ? 'right:3px':'left:3px' ?>;
                                    width:22px;height:22px;border-radius:50%;background:white;
                                    transition:all .2s;box-shadow:0 1px 3px rgba(0,0,0,.2);"></div>
                            </div>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:.9rem;" id="status-text">
                                <?= $tokoTutup=='1' ? '🔴 Toko Sedang TUTUP' : '🟢 Toko Sedang BUKA' ?>
                            </div>
                            <div style="font-size:.75rem;color:var(--text-muted);">Aktifkan untuk menutup toko sementara</div>
                        </div>
                    </label>
                </div>

                <!-- Jam -->
                <div class="content-card">
                    <h3 class="card-title" style="margin-bottom:1.25rem;">⏰ Jam Operasional</h3>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label style="display:block;font-weight:700;margin-bottom:.5rem;font-size:.875rem;">Jam Buka</label>
                            <input type="time" name="jam_buka" class="form-control" value="<?= $jamBuka ?>" style="padding-left:1rem;">
                        </div>
                        <div class="form-group">
                            <label style="display:block;font-weight:700;margin-bottom:.5rem;font-size:.875rem;">Jam Tutup</label>
                            <input type="time" name="jam_tutup" class="form-control" value="<?= $jamTutup ?>" style="padding-left:1rem;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:1rem;">
                        <label style="display:block;font-weight:700;margin-bottom:.5rem;font-size:.875rem;">Pesan Saat Tutup</label>
                        <textarea name="pesan_tutup" class="form-control" rows="2" style="padding-left:1rem;"><?= htmlspecialchars($pesanTutup) ?></textarea>
                    </div>

                    <!-- Today info -->
                    <div style="margin-top:1rem;padding:1rem;background:var(--bg-page);border-radius:var(--radius-md);font-size:.8125rem;color:var(--text-muted);">
                        <i class="fa-solid fa-circle-info" style="color:var(--primary);margin-right:.4rem;"></i>
                        Waktu sekarang: <strong style="color:var(--text-main);"><?= $now->format('H:i, l') ?></strong>
                    </div>
                </div>

                <!-- Hari Buka -->
                <div class="content-card">
                    <h3 class="card-title" style="margin-bottom:1.25rem;">📅 Hari Beroperasi</h3>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem;">
                        <?php foreach ($hariLabels as $val => $label):
                            $checked = in_array((string)$val, $hariBuka);
                        ?>
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 14px;
                            border:2px solid <?= $checked ? 'var(--primary)' : 'var(--border)' ?>;
                            border-radius:var(--radius-md);transition:all .2s;
                            background:<?= $checked ? 'var(--primary-light)' : '' ?>;"
                            id="hari-label-<?= $val ?>">
                            <input type="checkbox" name="hari_buka[]" value="<?= $val ?>" <?= $checked ? 'checked':'' ?>
                                   style="width:16px;height:16px;accent-color:var(--primary);"
                                   onchange="updateHariLabel(this,'<?= $val ?>')">
                            <span style="font-weight:700;font-size:.875rem;"><?= $label ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Summary Card -->
            <div>
                <div class="content-card" style="position:sticky;top:2rem;">
                    <h3 class="card-title" style="margin-bottom:1.25rem;">📊 Ringkasan Jadwal</h3>

                    <div style="background:linear-gradient(135deg,#F59E0B,#D97706);border-radius:var(--radius-md);padding:1.5rem;color:#fff;margin-bottom:1.25rem;text-align:center;">
                        <div style="font-size:.75rem;opacity:.8;margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em;">Jam Operasional</div>
                        <div style="font-size:2rem;font-weight:900;letter-spacing:1px;"><?= $jamBuka ?> – <?= $jamTutup ?></div>
                        <div style="font-size:.8rem;opacity:.85;margin-top:6px;">
                            <?php
                                $hariNama = [];
                                foreach ($hariBuka as $h) { if (isset($hariLabels[$h])) $hariNama[] = $hariLabels[$h]; }
                                echo implode(', ', $hariNama) ?: 'Tidak ada hari terpilih';
                            ?>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:.625rem;font-size:.875rem;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--text-muted);">Status Saat Ini</span>
                            <span style="font-weight:700;color:<?= $isOpen ? 'var(--success)' : 'var(--error)' ?>;">
                                <?= $isOpen ? '🟢 Buka' : '🔴 Tutup' ?>
                            </span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:var(--text-muted);">Forced Tutup</span>
                            <span style="font-weight:700;"><?= $tokoTutup=='1' ? '✅ Aktif' : '—' ?></span>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top:1.5rem;">
                        <i class="fa-solid fa-floppy-disk" style="margin-right:.4rem;"></i>Simpan Perubahan
                    </button>
                </div>
            </div>

        </div>
    </form>
</main>

<script>
function updateStatusLabel(cb) {
    const label  = document.getElementById('status-label');
    const text   = document.getElementById('status-text');
    const toggle = document.getElementById('toggle-visual');
    const knob   = document.getElementById('toggle-knob');
    if (cb.checked) {
        label.style.background   = 'rgba(239,68,68,.08)';
        label.style.borderColor  = 'rgba(239,68,68,.2)';
        text.textContent         = '🔴 Toko Sedang TUTUP';
        toggle.style.background  = 'var(--error)';
        knob.style.left = 'auto'; knob.style.right = '3px';
    } else {
        label.style.background   = 'rgba(16,185,129,.08)';
        label.style.borderColor  = 'rgba(16,185,129,.2)';
        text.textContent         = '🟢 Toko Sedang BUKA';
        toggle.style.background  = 'var(--success)';
        knob.style.right = 'auto'; knob.style.left = '3px';
    }
}

function updateHariLabel(cb, val) {
    const lbl = document.getElementById('hari-label-' + val);
    if (cb.checked) {
        lbl.style.background   = 'var(--primary-light)';
        lbl.style.borderColor  = 'var(--primary)';
    } else {
        lbl.style.background   = '';
        lbl.style.borderColor  = 'var(--border)';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
