// ────────────────────────── DATA ──────────────────────────
const menuData = [
  { id:1, name:"Nasi Goreng Spesial", cat:"makanan", price:20000, desc:"Nasi goreng dengan telur, ayam, dan sayur segar pilihan", img:"https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=300&q=80" },
  { id:2, name:"Nasi Telor Sambal", cat:"makanan", price:20000, desc:"Nasi panas dengan telur ceplok sambal tomat dan layu", img:"https://images.unsplash.com/photo-1512058564366-18510be2db19?w=300&q=80" },
  { id:3, name:"Nasi Telor Cumi", cat:"makanan", price:18000, desc:"Nasi dengan telor dadar dan cumi bumbu rica rica", img:"https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300&q=80" },
  { id:4, name:"Nasi Telor Cakalang", cat:"makanan", price:15000, desc:"Nasi dengan ikan cakalang pedas dan telur rebus", img:"https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&q=80" },
  { id:5, name:"Ayam Bakar Madu", cat:"makanan", price:25000, desc:"Ayam bakar dengan bumbu madu dan rempah pilihan", img:"https://images.unsplash.com/photo-1598103442097-8b74394b95c3?w=300&q=80" },
  { id:6, name:"Mie Goreng Spesial", cat:"makanan", price:18000, desc:"Mie goreng dengan topping ayam, telur, dan sayur", img:"https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=300&q=80" },
  { id:7, name:"Es Teh Manis", cat:"minuman", price:5000, desc:"Teh manis dingin segar, cocok untuk menemani makan", img:"https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=300&q=80" },
  { id:8, name:"Es Jeruk Peras", cat:"minuman", price:7000, desc:"Jeruk peras segar dengan es batu pilihan", img:"https://images.unsplash.com/photo-1621263764928-df1444c5e859?w=300&q=80" },
  { id:9, name:"Jus Alpukat", cat:"minuman", price:12000, desc:"Jus alpukat segar dengan susu dan madu", img:"https://images.unsplash.com/photo-1615478503562-ec2d8aa0e24e?w=300&q=80" },
  { id:10, name:"Kopi Hitam", cat:"minuman", price:6000, desc:"Kopi hitam robusta pilihan diseduh panas atau dingin", img:"https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300&q=80" },
  { id:11, name:"French Fries", cat:"snack", price:12000, desc:"Kentang goreng renyah dengan saus sambal dan mayo", img:"https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=300&q=80" },
  { id:12, name:"Chicken Wings", cat:"snack", price:24000, desc:"Sayap ayam goreng crispy bumbu pedas manis", img:"https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=300&q=80" },
  { id:13, name:"Pisang Goreng", cat:"snack", price:8000, desc:"Pisang goreng crispy dengan topping keju dan meses", img:"https://images.unsplash.com/photo-1621956808427-00eded71c671?w=300&q=80" },
  { id:14, name:"Onion Rings", cat:"snack", price:14000, desc:"Ring bawang goreng crispy dengan saus keju spesial", img:"https://images.unsplash.com/photo-1639024471283-03518883512d?w=300&q=80" },
];

// ────────────────────────── STATE ──────────────────────────
let cart = []; // [{id, name, price, img, qty}]
let currentCat = 'semua';
let searchQ = '';
let guestCount = 5;
let payMethod = 'qris';
let qrisTimer = null;

// ────────────────────────── PAGE SWITCH ──────────────────────────
function switchPage(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('page-' + page).classList.add('active');
  document.getElementById('nav-' + page).classList.add('active');
  if (page === 'keranjang') renderCart();
  document.querySelector(`#page-${page}`).scrollTop = 0;
}

// ────────────────────────── RENDER MENU ──────────────────────────
function renderMenu() {
  const grid = document.getElementById('menu-grid');
  const emptyEl = document.getElementById('empty-menu');
  const promoSec = document.getElementById('promo-section');
  let items = menuData;

  if (currentCat !== 'semua') items = items.filter(i => i.cat === currentCat);
  if (searchQ) items = items.filter(i => i.name.toLowerCase().includes(searchQ.toLowerCase()));

  // Hide promo when filtered
  promoSec.style.display = (currentCat === 'semua' && !searchQ) ? 'block' : 'none';

  if (items.length === 0) {
    grid.innerHTML = '';
    emptyEl.style.display = 'block';
    return;
  }
  emptyEl.style.display = 'none';
  grid.innerHTML = items.map((item, i) => `
    <div class="menu-card" style="animation-delay:${i * 0.04}s">
      <img src="${item.img}" alt="${item.name}" loading="lazy">
      <div class="menu-body">
        <div class="menu-name">${item.name}</div>
        <div class="menu-desc">${item.desc}</div>
        <div class="menu-price">Rp ${item.price.toLocaleString('id-ID')}</div>
        <button class="add-btn" onclick="addToCart(${item.id})">+ Tambahkan</button>
      </div>
    </div>
  `).join('');
}

function filterCategory(cat, btn) {
  currentCat = cat;
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderMenu();
}

function filterSearch() {
  searchQ = document.getElementById('search-input').value;
  renderMenu();
}

// ────────────────────────── CART OPERATIONS ──────────────────────────
function addToCart(id) {
  const item = menuData.find(m => m.id === id);
  const existing = cart.find(c => c.id === id);
  if (existing) { existing.qty++; }
  else { cart.push({ id: item.id, name: item.name, price: item.price, img: item.img, qty: 1 }); }
  updateCartBadge();
  showToast(`✅ ${item.name} ditambahkan!`);
}

function removeFromCart(id) {
  cart = cart.filter(c => c.id !== id);
  updateCartBadge();
  renderCart();
}

function changeQty(id, delta) {
  const item = cart.find(c => c.id === id);
  if (!item) return;
  item.qty += delta;
  if (item.qty <= 0) removeFromCart(id);
  else renderCart();
  updateCartBadge();
}

function updateCartBadge() {
  const total = cart.reduce((s, c) => s + c.qty, 0);
  const badge = document.getElementById('cart-badge');
  badge.textContent = total;
  if (total > 0) { badge.classList.add('show'); }
  else { badge.classList.remove('show'); }
}

function renderCart() {
  const emptyDiv = document.getElementById('empty-cart');
  const filledDiv = document.getElementById('cart-filled');
  if (cart.length === 0) {
    emptyDiv.style.display = 'flex';
    filledDiv.style.display = 'none';
    return;
  }
  emptyDiv.style.display = 'none';
  filledDiv.style.display = 'block';
  const list = document.getElementById('cart-items-list');
  list.innerHTML = cart.map(item => `
    <div class="cart-item">
      <img src="${item.img}" alt="${item.name}">
      <div class="cart-item-info">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-price">Rp ${item.price.toLocaleString('id-ID')}</div>
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
  updateSummary();
}

function updateSummary() {
  const sub = cart.reduce((s, c) => s + c.price * c.qty, 0);
  const tax = Math.round(sub * 0.05);
  const total = sub + tax;
  document.getElementById('summary-subtotal').textContent = 'Rp ' + sub.toLocaleString('id-ID');
  document.getElementById('summary-tax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
  document.getElementById('summary-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function getTotal() {
  const sub = cart.reduce((s, c) => s + c.price * c.qty, 0);
  return sub + Math.round(sub * 0.05);
}

function selectPayment(method) {
  payMethod = method;
  document.getElementById('pay-qris').classList.toggle('active', method === 'qris');
  document.getElementById('pay-cash').classList.toggle('active', method === 'cash');
}

function doCheckout() {
  if (cart.length === 0) { showToast('Keranjang masih kosong!'); return; }
  const total = getTotal();
  if (payMethod === 'qris') {
    document.getElementById('qris-amount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    openModal('modal-qris');
    startQrisTimer();
  } else {
    document.getElementById('cash-amount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    openModal('modal-cash');
  }
}

// ────────────────────────── QRIS TIMER ──────────────────────────
function startQrisTimer() {
  if (qrisTimer) clearInterval(qrisTimer);
  let secs = 299;
  document.getElementById('qris-timer').textContent = formatTimer(secs);
  qrisTimer = setInterval(() => {
    secs--;
    if (secs <= 0) {
      clearInterval(qrisTimer);
      document.getElementById('qris-timer').textContent = 'Waktu habis!';
      return;
    }
    document.getElementById('qris-timer').textContent = formatTimer(secs);
  }, 1000);
}

function formatTimer(s) {
  const m = Math.floor(s / 60);
  const sec = s % 60;
  return `${m} menit : ${sec.toString().padStart(2,'0')} detik`;
}

function downloadQRIS() { showToast('📥 QRIS berhasil didownload!'); }

function confirmCashOrder() {
  closeModal('modal-cash');
  cart = [];
  updateCartBadge();
  renderCart();
  showToast('✅ Pesanan dikonfirmasi! Silakan ke kasir.');
  setTimeout(() => switchPage('beranda'), 1200);
}

// ────────────────────────── RESERVASI ──────────────────────────
function changeGuest(delta) {
  guestCount = Math.max(1, Math.min(20, guestCount + delta));
  document.getElementById('guest-num').textContent = guestCount;
  document.getElementById('sum-tamu').textContent = guestCount + ' orang';
  document.getElementById('sum-deposit').textContent = 'Rp ' + (guestCount * 20000).toLocaleString('id-ID');
}

function submitReservasi() {
  const nama = document.getElementById('res-nama').value;
  const telp = document.getElementById('res-telp').value;
  const email = document.getElementById('res-email').value;
  const tanggal = document.getElementById('res-tanggal').value;
  const waktu = document.getElementById('res-waktu').value;
  if (!nama || !telp || !email || !tanggal || !waktu) {
    showToast('⚠️ Lengkapi semua data terlebih dahulu!');
    return;
  }
  showToast(`🎉 Reservasi ${nama} berhasil dikonfirmasi!`);
  // Reset
  ['res-nama','res-telp','res-email','res-tanggal','res-waktu','res-catatan'].forEach(id => document.getElementById(id).value = '');
  guestCount = 1; changeGuest(0);
}

// ────────────────────────── MODAL ──────────────────────────
function openModal(id) {
  document.getElementById(id).classList.add('show');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  document.getElementById(id).classList.remove('show');
  document.body.style.overflow = '';
  if (id === 'modal-qris' && qrisTimer) { clearInterval(qrisTimer); qrisTimer = null; }
}
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(overlay.id); });
});

// ────────────────────────── TOAST ──────────────────────────
let toastTimeout;
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimeout);
  toastTimeout = setTimeout(() => t.classList.remove('show'), 2200);
}

// ────────────────────────── INIT ──────────────────────────
// Set today as default date for reservasi
const today = new Date().toISOString().split('T')[0];
document.getElementById('res-tanggal').value = today;
document.getElementById('res-waktu').value = '19:00';
changeGuest(0);
renderMenu();