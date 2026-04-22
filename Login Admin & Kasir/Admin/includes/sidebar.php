<aside class="sidebar">
    <div class="sidebar-header">
        <svg width="32" height="32" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="45" stroke="#0EA5E9" stroke-width="2" stroke-dasharray="8 4"/>
            <path d="M50 20C33.4315 20 20 33.4315 20 50C20 66.5685 33.4315 80 50 80C66.5685 80 80 66.5685 80 50" stroke="#0EA5E9" stroke-width="8" stroke-linecap="round"/>
            <circle cx="50" cy="50" r="10" fill="#0EA5E9"/>
        </svg>
        <div style="font-weight: 800; font-size: 1.125rem; letter-spacing: -0.025em;">Teras JTI</div>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="../dashboard/index.php" class="menu-link <?= strpos($_SERVER['PHP_SELF'], 'dashboard') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        
        <li class="menu-item">
            <div class="menu-link dropdown-toggle <?= strpos($_SERVER['PHP_SELF'], 'laporan') !== false ? 'active' : '' ?>" onclick="toggleDropdown(this)">
                <i class="fa-solid fa-chart-line"></i>
                <span class="menu-text">Laporan</span>
                <i class="fa-solid fa-chevron-down dropdown-icon"></i>
            </div>
            <ul class="submenu" style="<?= strpos($_SERVER['PHP_SELF'], 'laporan') !== false ? 'display: block;' : '' ?>">
                <li><a href="../laporan/harian.php" class="<?= strpos($_SERVER['PHP_SELF'], 'harian.php') !== false ? 'active' : '' ?>">Harian</a></li>
                <li><a href="../laporan/mingguan.php" class="<?= strpos($_SERVER['PHP_SELF'], 'mingguan.php') !== false ? 'active' : '' ?>">Mingguan</a></li>
                <li><a href="../laporan/bulanan.php" class="<?= strpos($_SERVER['PHP_SELF'], 'bulanan.php') !== false ? 'active' : '' ?>">Bulanan</a></li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="../produk/index.php" class="menu-link <?= strpos($_SERVER['PHP_SELF'], 'produk') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-utensils"></i>
                <span class="menu-text">List Produk</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="fa-solid fa-users"></i>
                <span class="menu-text">Kelola User</span>
            </a>
        </li>

        <li class="menu-item" style="margin-top: 2rem;">
            <div style="padding: 0 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">System</div>
            <a href="../settings/index.php" class="menu-link <?= strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-gear"></i>
                <span class="menu-text">Pengaturan</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="../auth/logout.php" class="menu-link" style="color: var(--error);">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="menu-text">Logout</span>
        </a>
    </div>
</aside>

<script>
function toggleDropdown(el) {
    const submenu = el.nextElementSibling;
    const icon = el.querySelector('.dropdown-icon');
    
    if (submenu.style.display === 'block') {
        submenu.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    } else {
        submenu.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    }
}
</script>
