<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../../../User/assets/img/Logo Teras JTI.png" alt="Logo Teras JTI" width="32" height="32" style="object-fit: contain;">
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
                <span class="menu-text">Kelola Produk</span>
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
