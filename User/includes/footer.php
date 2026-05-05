
  <!-- ───── BOTTOM NAV ───── -->
  <nav class="bottom-nav">
    <div class="nav-item active" id="nav-beranda" onclick="switchPage('beranda')">
      <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m3 9 9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Beranda
    </div>
    <div class="nav-item" id="nav-reservasi" onclick="switchPage('reservasi')">
      <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Reservasi
    </div>
    <div class="nav-item" id="nav-keranjang" onclick="switchPage('keranjang')">
      <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      Keranjang
      <span class="cart-badge" id="cart-badge">0</span>
    </div>
  </nav>

  <!-- ═══════════════════════════════════ MODAL: NOMOR MEJA ═══════════════════════════════════ -->
  <div class="modal-overlay" id="modal-nomor-meja">
    <div class="modal-sheet">
      <div class="modal-handle"></div>
      <div class="modal-title">📍 Masukkan Nomor Meja</div>
      <div class="modal-sub">Silakan isi nomor meja kamu sebelum melanjutkan pembayaran</div>
      <div class="meja-input-box">
        <div class="meja-icon-wrap">
          <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            <circle cx="12" cy="9" r="2.5"/>
          </svg>
        </div>
        <label for="input-nomor-meja">Nomor Meja</label>
        <input type="number" id="input-nomor-meja" class="form-input meja-input" placeholder="Contoh: 1, 2, 3..." min="1" max="99" inputmode="numeric">
        <div class="meja-hint">Nomor meja bisa dilihat pada kartu yang ada di meja kamu</div>
      </div>
      <button class="checkout-btn meja-confirm-btn" id="btn-confirm-meja" onclick="confirmNomorMeja()">Lanjutkan ke Pembayaran →</button>
      <button class="btn-primary" style="width:100%;background:white;color:var(--gray-600);box-shadow:none;border:2px solid var(--gray-200);margin-top:10px" onclick="closeModal('modal-nomor-meja')">Kembali</button>
    </div>
  </div>

  <!-- ═══════════════════════════════════ MODAL: QRIS ═══════════════════════════════════ -->
  <div class="modal-overlay" id="modal-qris">
    <div class="modal-sheet">
      <div class="modal-handle"></div>
      <div class="modal-title">Bayar dengan QRIS</div>
      <div class="cash-box">
        <div class="cash-icon">📲</div>
        <div class="cash-title">Silakan menuju ke Kasir</div>
        <div class="cash-desc">Tunjukkan pesananmu kepada petugas kasir dan lakukan pembayaran menggunakan QRIS. Petugas kami siap membantu Anda!</div>
      </div>
      <div class="amount-box" style="margin-bottom:20px">
        <div class="amount-label">Total yang Harus Dibayar</div>
        <div class="amount-value" id="qris-amount">Rp 0</div>
      </div>
      <button class="checkout-btn" onclick="confirmQrisOrder()" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); box-shadow: 0 8px 24px rgba(34,197,94,0.3); margin-bottom: 8px;">✅ Konfirmasi Pesanan</button>
      <button class="btn-primary" style="width:100%;background:white;color:var(--gray-600);box-shadow:none;border:2px solid var(--gray-200);margin-top:10px" onclick="closeModal('modal-qris')">Kembali</button>
    </div>
  </div>

  <!-- ═══════════════════════════════════ MODAL: CASH ═══════════════════════════════════ -->
  <div class="modal-overlay" id="modal-cash">
    <div class="modal-sheet">
      <div class="modal-handle"></div>
      <div class="modal-title">Pembayaran Cash</div>
      <div class="cash-box">
        <div class="cash-icon">💵</div>
        <div class="cash-title">Silakan menuju ke Kasir</div>
        <div class="cash-desc">Tunjukkan pesananmu kepada petugas kasir dan lakukan pembayaran secara tunai. Petugas kami siap melayani Anda!</div>
      </div>
      <div class="amount-box" style="margin-bottom:20px">
        <div class="amount-label">Total yang Harus Dibayar</div>
        <div class="amount-value" id="cash-amount">Rp 0</div>
      </div>
      <button class="checkout-btn" onclick="confirmCashOrder()" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); box-shadow: 0 8px 24px rgba(34,197,94,0.3);">✅ Konfirmasi Pesanan</button>
      <button class="btn-primary" style="width:100%;background:white;color:var(--gray-600);box-shadow:none;border:2px solid var(--gray-200);margin-top:10px" onclick="closeModal('modal-cash')">Kembali</button>
    </div>
  </div>

  <!-- ───── TOAST ───── -->
  <div class="toast" id="toast"></div>
</div><!-- .app -->

<script src="assets/js/meja-teras-jti.js?v=<?= time() ?>"></script>
</body>
</html>
