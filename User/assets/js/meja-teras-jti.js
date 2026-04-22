// ────────────────────────── DATA ──────────────────────────
let menuData = []; // Will be populated from API
let cart = []; // [{id, name, price, img, qty}]
let currentCat = 'semua';
let searchQ = '';
let guestCount = 5;
let payMethod = 'qris';
let qrisTimer = null;

// ────────────────────────── FETCH MENU ──────────────────────────
async function fetchMenu() {
  try {
    console.log('Fetching menu from API...');
    const resp = await fetch('api/get_products.php?v=' + Date.now());
    if (!resp.ok) throw new Error('Query API Gagal: ' + resp.status);
    
    const json = await resp.json();

    if (json.error) {
      console.error('API Error:', json.error);
      alert('⚠️ Error dari server: ' + json.error);
      return;
    }

    menuData = json;
    console.log('Data diterima:', menuData);
    
    if (!Array.isArray(menuData)) {
      console.error('Data bukan array!');
      return;
    }

    renderMenu();
  } catch (err) {
    console.error('Terjadi kesalahan:', err);
    alert('⚠️ Gagal memuat menu: ' + err.message);
  }
}

// ────────────────────────── RENDER MENU ──────────────────────────
function renderMenu() {
  const grid = document.getElementById('menu-grid');
  const promoGrid = document.getElementById('promo-grid');
  const emptyEl = document.getElementById('empty-menu');
  const promoSec = document.getElementById('promo-section');

  if (!grid) { console.error('Kontainer menu-grid tidak ditemukan!'); return; }

  let items = menuData;

  // Render Promo (Top 2)
  if (promoGrid && promoSec && currentCat === 'semua' && !searchQ && items.length > 0) {
    promoSec.style.display = 'block';
    const promos = items.slice(0, 2);
    promoGrid.innerHTML = promos.map(p => `
      <div class="promo-card" onclick="addToCart(${p.id})">
        <img src="${p.img}" alt="${p.name}">
        <div class="promo-info">
          <div class="name">${p.name}</div>
          <div class="new-price">Rp ${Number(p.price).toLocaleString('id-ID')}</div>
        </div>
        <span class="promo-badge">HOT</span>
      </div>
    `).join('');
  } else if (promoSec) {
    promoSec.style.display = 'none';
  }

  // Filter
  if (currentCat !== 'semua') items = items.filter(i => i.cat === currentCat);
  if (searchQ) items = items.filter(i => i.name.toLowerCase().includes(searchQ.toLowerCase()));

  // Render Main Grid
  if (items.length === 0) {
    grid.innerHTML = '';
    if (emptyEl) emptyEl.style.display = 'block';
    return;
  }
  
  if (emptyEl) emptyEl.style.display = 'none';
  grid.innerHTML = items.map((item, i) => `
    <div class="menu-card" style="animation-delay:${i * 0.05}s">
      <img src="${item.img}" alt="${item.name}" loading="lazy">
      <div class="menu-body">
        <div class="menu-name">${item.name}</div>
        <div class="menu-desc">${item.desc || ''}</div>
        <div class="menu-price">Rp ${Number(item.price).toLocaleString('id-ID')}</div>
        <button class="add-btn" onclick="addToCart(${item.id})">+ Tambahkan</button>
      </div>
    </div>
  `).join('');
}

// ────────────────────────── PAGE SWITCH ──────────────────────────
function switchPage(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  
  const targetPage = document.getElementById('page-' + page);
  const targetNav = document.getElementById('nav-' + page);
  
  if (targetPage) targetPage.classList.add('active');
  if (targetNav) targetNav.classList.add('active');
  
  if (page === 'keranjang') renderCart();
}

// ────────────────────────── CART OPERATIONS ──────────────────────────
function addToCart(id) {
  const item = menuData.find(m => m.id == id);
  if (!item) return;
  
  const existing = cart.find(c => c.id == id);
  if (existing) {
    existing.qty++;
  } else {
    cart.push({ id: item.id, name: item.name, price: item.price, img: item.img, qty: 1 });
  }
  updateCartBadge();
  showToast(`✅ ${item.name} ditambahkan!`);
}

function removeFromCart(id) {
  cart = cart.filter(c => c.id != id);
  updateCartBadge();
  renderCart();
}

function changeQty(id, delta) {
  const item = cart.find(c => c.id == id);
  if (!item) return;
  item.qty += delta;
  if (item.qty <= 0) removeFromCart(id);
  else renderCart();
  updateCartBadge();
}

function updateCartBadge() {
  const total = cart.reduce((s, c) => s + c.qty, 0);
  const badge = document.getElementById('cart-badge');
  if (badge) {
    badge.textContent = total;
    badge.classList.toggle('show', total > 0);
  }
}

function renderCart() {
  const emptyDiv = document.getElementById('empty-cart');
  const filledDiv = document.getElementById('cart-filled');
  if (cart.length === 0) {
    if (emptyDiv) emptyDiv.style.display = 'flex';
    if (filledDiv) filledDiv.style.display = 'none';
    return;
  }
  if (emptyDiv) emptyDiv.style.display = 'none';
  if (filledDiv) filledDiv.style.display = 'block';
  
  const list = document.getElementById('cart-items-list');
  if (list) {
    list.innerHTML = cart.map(item => `
      <div class="cart-item">
        <img src="${item.img}" alt="${item.name}">
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          <div class="cart-item-price">Rp ${Number(item.price).toLocaleString('id-ID')}</div>
          <div class="cart-item-actions">
            <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
            <span class="qty-num">${item.qty}</span>
            <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
          </div>
        </div>
        <button class="delete-btn" onclick="removeFromCart(${item.id})">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </button>
      </div>
    `).join('');
  }
  updateSummary();
}

function updateSummary() {
  const sub = cart.reduce((s, c) => s + Number(c.price) * c.qty, 0);
  const tax = Math.round(sub * 0.05);
  const total = sub + tax;
  
  const subEl = document.getElementById('summary-subtotal');
  const taxEl = document.getElementById('summary-tax');
  const totalEl = document.getElementById('summary-total');
  
  if (subEl) subEl.textContent = 'Rp ' + sub.toLocaleString('id-ID');
  if (taxEl) taxEl.textContent = 'Rp ' + tax.toLocaleString('id-ID');
  if (totalEl) totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
}


function selectPayment(method) {
  payMethod = method;
  const qrisBtn = document.getElementById('pay-qris');
  const cashBtn = document.getElementById('pay-cash');
  if (qrisBtn) qrisBtn.classList.toggle('active', method === 'qris');
  if (cashBtn) cashBtn.classList.toggle('active', method === 'cash');
}

function doCheckout() {
  if (cart.length === 0) { showToast('Keranjang masih kosong!'); return; }
  const sub   = cart.reduce((s, c) => s + Number(c.price) * c.qty, 0);
  const total = sub + Math.round(sub * 0.05);

  if (payMethod === 'qris') {
    const qAmount = document.getElementById('qris-amount');
    if (qAmount) qAmount.textContent = 'Rp ' + total.toLocaleString('id-ID');
    openModal('modal-qris');
    startQrisTimer();
  } else {
    const cAmount = document.getElementById('cash-amount');
    if (cAmount) cAmount.textContent = 'Rp ' + total.toLocaleString('id-ID');
    openModal('modal-cash');
  }
}

function startQrisTimer() {
  if (qrisTimer) clearInterval(qrisTimer);
  let seconds = 5 * 60 - 1;
  const el = document.getElementById('qris-timer');
  qrisTimer = setInterval(() => {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    if (el) el.textContent = m + ' menit : ' + String(s).padStart(2, '0') + ' detik';
    if (seconds <= 0) {
      clearInterval(qrisTimer);
      if (el) el.textContent = 'Waktu habis';
    }
    seconds--;
  }, 1000);
}

function downloadQRIS() {
  const svg  = document.querySelector('#modal-qris svg');
  if (!svg) return;
  const data = new XMLSerializer().serializeToString(svg);
  const blob = new Blob([data], { type: 'image/svg+xml' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = 'qris-teras-jti.svg';
  a.click();
  URL.revokeObjectURL(url);
}

// ────────────────────────── CHECKOUT ──────────────────────────
function getNomorMeja() {
  // Extract from the table-badge element, e.g. "Meja 1"
  const badge = document.querySelector('.table-badge');
  if (badge) {
    // strip the SVG icon text, keep only the text node
    const text = badge.innerText || badge.textContent;
    return text.trim();
  }
  return 'Meja 1';
}

async function confirmOrder(method) {
  const sub   = cart.reduce((s, c) => s + Number(c.price) * c.qty, 0);
  const total = sub + Math.round(sub * 0.05);
  const nomor_meja = getNomorMeja();

  try {
    showToast('⌛ Sedang memproses...');
    const resp = await fetch('api/process_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        cart:           cart,
        total:          total,
        payment_method: method === 'qris' ? 'QRIS' : 'Tunai',
        nomor_meja:     nomor_meja
      })
    });
    
    const result = await resp.json();
    if (result.success) {
      closeModal('modal-cash');
      closeModal('modal-qris');
      cart = [];
      updateCartBadge();
      renderCart();
      showToast('✅ Pesanan berhasil dikirim! Pesanan #' + result.order_id);
      setTimeout(() => switchPage('beranda'), 1500);
    } else {
      showToast('❌ Gagal: ' + result.error);
    }
  } catch (err) {
    console.error(err);
    showToast('❌ Gagal mengirim pesanan');
  }
}

async function confirmCashOrder() {
  await confirmOrder('cash');
}

async function confirmQrisOrder() {
  await confirmOrder('qris');
}

// ────────────────────────── MODALS & HELPERS ──────────────────────────
function openModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.classList.remove('show');
    document.body.style.overflow = '';
  }
}
function showToast(msg) {
  const t = document.getElementById('toast');
  if (t) {
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2000);
  }
}

function filterCategory(cat, btn) {
  currentCat = cat;
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  renderMenu();
}

function filterSearch() {
  const input = document.getElementById('search-input');
  searchQ = input ? input.value : '';
  renderMenu();
}

// ────────────────────────── INIT ──────────────────────────
window.addEventListener('load', () => {
  console.log('Window loaded, initializing...');
  fetchMenu();
});