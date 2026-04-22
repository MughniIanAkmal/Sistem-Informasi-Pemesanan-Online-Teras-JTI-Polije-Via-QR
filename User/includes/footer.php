
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

  <!-- ═══════════════════════════════════ MODAL: QRIS ═══════════════════════════════════ -->
  <div class="modal-overlay" id="modal-qris">
    <div class="modal-sheet">
      <div class="modal-handle"></div>
      <div class="modal-title">Bayar dengan QRIS</div>
      <div class="modal-sub">Scan atau download QRIS untuk melanjutkan<br>pembayaran Anda melalui E-Wallet atau Mobile Banking</div>
      <div class="qris-box">
        <div class="qris-svg-wrap">
          <!-- Static QR Pattern (SVG) -->
          <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="image-rendering:pixelated">
            <!-- Corner squares -->
            <rect x="5" y="5" width="30" height="30" fill="none" stroke="#000" stroke-width="4"/>
            <rect x="12" y="12" width="16" height="16" fill="#000"/>
            <rect x="65" y="5" width="30" height="30" fill="none" stroke="#000" stroke-width="4"/>
            <rect x="72" y="12" width="16" height="16" fill="#000"/>
            <rect x="5" y="65" width="30" height="30" fill="none" stroke="#000" stroke-width="4"/>
            <rect x="12" y="72" width="16" height="16" fill="#000"/>
            <!-- Dots pattern -->
            <rect x="42" y="5" width="6" height="6" fill="#000"/><rect x="52" y="5" width="6" height="6" fill="#000"/>
            <rect x="42" y="15" width="6" height="6" fill="#000"/><rect x="58" y="15" width="6" height="6" fill="#000"/>
            <rect x="47" y="25" width="6" height="6" fill="#000"/><rect x="57" y="25" width="6" height="6" fill="#000"/>
            <rect x="5" y="42" width="6" height="6" fill="#000"/><rect x="15" y="42" width="6" height="6" fill="#000"/>
            <rect x="25" y="42" width="6" height="6" fill="#000"/><rect x="5" y="52" width="6" height="6" fill="#000"/>
            <rect x="20" y="52" width="6" height="6" fill="#000"/><rect x="30" y="52" width="6" height="6" fill="#000"/>
            <rect x="10" y="57" width="6" height="6" fill="#000"/><rect x="25" y="62" width="6" height="6" fill="#000"/>
            <rect x="42" y="42" width="6" height="6" fill="#000"/><rect x="52" y="42" width="6" height="6" fill="#000"/>
            <rect x="62" y="42" width="6" height="6" fill="#000"/><rect x="72" y="42" width="6" height="6" fill="#000"/>
            <rect x="82" y="42" width="6" height="6" fill="#000"/><rect x="42" y="52" width="6" height="6" fill="#000"/>
            <rect x="57" y="52" width="6" height="6" fill="#000"/><rect x="67" y="52" width="6" height="6" fill="#000"/>
            <rect x="47" y="62" width="6" height="6" fill="#000"/><rect x="62" y="62" width="6" height="6" fill="#000"/>
            <rect x="42" y="72" width="6" height="6" fill="#000"/><rect x="52" y="72" width="6" height="6" fill="#000"/>
            <rect x="62" y="72" width="6" height="6" fill="#000"/><rect x="77" y="72" width="6" height="6" fill="#000"/>
            <rect x="42" y="82" width="6" height="6" fill="#000"/><rect x="57" y="82" width="6" height="6" fill="#000"/>
            <rect x="72" y="82" width="6" height="6" fill="#000"/><rect x="87" y="82" width="6" height="6" fill="#000"/>
          </svg>
        </div>
        <div class="qris-label">QRIS · TERAS JTI</div>
      </div>
      <div class="timer-wrap">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span id="qris-timer">4 menit : 59 detik</span>
      </div>
      <div class="amount-box">
        <div class="amount-label">Jumlah Tagihan</div>
        <div class="amount-value" id="qris-amount">Rp 0</div>
      </div>
      <div class="qris-info">⚠️ Pastikan Anda memiliki aplikasi E-Wallet/Mobile Banking yang mendukung QRIS di perangkat Anda untuk menyelesaikan pembayaran.</div>
      <button class="checkout-btn" onclick="confirmQrisOrder()" style="background: var(--success); margin-bottom: 8px;">✅ Konfirmasi Sudah Bayar</button>
      <button class="checkout-btn" onclick="downloadQRIS()" style="background: var(--primary-light); color: var(--primary-dark);">⬇️ Download QRIS</button>
      <button class="btn-primary" style="width:100%;background:white;color:var(--gray-600);box-shadow:none;border:2px solid var(--gray-200)" onclick="closeModal('modal-qris')">Tutup</button>
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
      <button class="checkout-btn" onclick="confirmCashOrder()">✅ Konfirmasi Pesanan</button>
      <button class="btn-primary" style="width:100%;background:white;color:var(--gray-600);box-shadow:none;border:2px solid var(--gray-200);margin-top:10px" onclick="closeModal('modal-cash')">Kembali</button>
    </div>
  </div>

  <!-- ───── TOAST ───── -->
  <div class="toast" id="toast"></div>
</div><!-- .app -->

<script src="assets/js/meja-teras-jti.js"></script>
</body>
</html>
