  <!-- ═══════════════════════════════════ PAGE: BERANDA ═══════════════════════════════════ -->
  <div class="page active" id="page-beranda">

    <div class="header">
      <div class="header-top">
        <div class="header-brand">
          <h1>MEJA TERAS JTI</h1>
          <p>Pesan menu favoritmu sekarang</p>
        </div>

      </div>
      <div class="search-bar">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Cari menu..." id="search-input" oninput="filterSearch()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="14" y2="12"/><line x1="4" y1="18" x2="11" y2="18"/></svg>
      </div>
    </div>

    <div class="category-tabs" id="cat-tabs">
      <button class="cat-btn active" onclick="filterCategory('semua', this)">Semua</button>
      <button class="cat-btn" onclick="filterCategory('makanan', this)">🍽️ Makanan</button>
      <button class="cat-btn" onclick="filterCategory('minuman', this)">🥤 Minuman</button>
      <button class="cat-btn" onclick="filterCategory('snack', this)">🍟 Snack</button>
    </div>

    <div id="promo-section" class="section" style="display: none;">
      <div class="section-title">🔥 Promo Spesial</div>
      <div class="promo-grid" id="promo-grid">
        <!-- Promo items will be rendered here dynamically -->
      </div>
    </div>

    <div class="section" id="menu-section">
      <div class="section-title" id="menu-section-title">🍴 Menu</div>
      <div class="menu-grid" id="menu-grid"></div>
      <div class="empty-menu" id="empty-menu" style="display:none">Tidak ada menu ditemukan 😢</div>
    </div>
  </div>
