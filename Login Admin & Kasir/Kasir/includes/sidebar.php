<?php
require_once __DIR__ . '/../includes/db.php';

// Count pesanan masuk (belum diproses)
try {
    $pendingCount = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status = 'Masuk'")->fetchColumn();
} catch (PDOException $e) { $pendingCount = 0; }

$currentPath = $_SERVER['PHP_SELF'];
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <!-- Kasir icon (blue) -->
        <svg width="34" height="34" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="45" stroke="#0EA5E9" stroke-width="2" stroke-dasharray="8 4"/>
            <path d="M30 38h40M30 50h30M30 62h20" stroke="#0EA5E9" stroke-width="6" stroke-linecap="round"/>
            <circle cx="72" cy="62" r="10" fill="#0EA5E9"/>
            <path d="M68 62l3 3 5-5" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div>
            <div class="sidebar-brand-name">Teras JTI</div>
            <div class="sidebar-role">Kasir</div>
        </div>
    </div>

    <ul class="sidebar-menu">
        <div class="menu-section-label">Main</div>

        <li class="menu-item">
            <a href="../dashboard/index.php"
               class="menu-link <?= strpos($currentPath, 'dashboard') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="../pesanan/index.php"
               class="menu-link <?= strpos($currentPath, 'pesanan') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-receipt"></i>
                <span class="menu-text">Konfirmasi Pesanan</span>
                <?php if ($pendingCount > 0): ?>
                    <span class="badge-pill"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>
        </li>

        <div class="menu-section-label">Pengaturan</div>

        <li class="menu-item">
            <a href="../jam_operasional/index.php"
               class="menu-link <?= strpos($currentPath, 'jam_operasional') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-clock"></i>
                <span class="menu-text">Jam Operasional</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="../auth/logout.php" class="menu-link" style="color: rgba(239,68,68,.8);">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="menu-text">Logout</span>
        </a>
    </div>
</aside>

<script>
function toggleDropdown(el) {
    const submenu = el.nextElementSibling;
    const icon    = el.querySelector('.dropdown-icon');
    if (submenu.style.display === 'block') {
        submenu.style.display = 'none';
        icon.style.transform  = 'rotate(0deg)';
    } else {
        submenu.style.display = 'block';
        icon.style.transform  = 'rotate(180deg)';
    }
}
</script>
