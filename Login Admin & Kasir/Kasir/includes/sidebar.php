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
        <img src="../../../User/assets/img/Logo Teras JTI.png" alt="Logo Teras JTI" width="34" height="34" style="object-fit: contain;">
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
                <span class="menu-text">Kelola Pesanan</span>
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
