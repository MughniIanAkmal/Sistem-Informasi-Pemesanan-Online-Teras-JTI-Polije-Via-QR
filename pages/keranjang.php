
  <!-- ═══════════════════════════════════ PAGE: KERANJANG ═══════════════════════════════════ -->
  <div class="page" id="page-keranjang">
    <div class="header-simple">
      <div style="display:flex;justify-content:space-between;align-items:center">
        <div>
          <h1>KERANJANG</h1>
          <p>Pesanan kamu ada di sini</p>
        </div>
        <svg width="28" height="28" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      </div>
    </div>

    <div id="empty-cart" class="empty-cart">
      <svg width="80" height="80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      <h3>Keranjang Kosong</h3>
      <p>Yuk, mulai tambahkan menu pilihanmu ke keranjang!</p>
      <button class="btn-primary" onclick="switchPage('beranda')">Lihat Menu</button>
    </div>

    <div id="cart-filled" style="display:none">
      <div class="cart-content">
        <div class="cart-title">🛒 Pesanan saya</div>
        <div id="cart-items-list"></div>

        <div class="cart-note">
          <label>📝 Catatan</label>
          <textarea placeholder="Contoh: ayam di pisah ya" id="cart-note"></textarea>
        </div>

        <div class="payment-section">
          <label>💳 Metode Pembayaran</label>
          <div class="payment-options">
            <div class="payment-opt active" id="pay-qris" onclick="selectPayment('qris')">
              <div class="pay-icon">📲</div>
              <div class="pay-name">QRIS</div>
            </div>
            <div class="payment-opt" id="pay-cash" onclick="selectPayment('cash')">
              <div class="pay-icon">💵</div>
              <div class="pay-name">Cash</div>
            </div>
          </div>
        </div>

        <div class="cart-summary">
          <div class="summary-row">
            <span>Subtotal</span>
            <span id="summary-subtotal">Rp 0</span>
          </div>
          <div class="summary-row">
            <span>Pajak (5%)</span>
            <span id="summary-tax">Rp 0</span>
          </div>
          <hr class="divider">
          <div class="summary-row total">
            <span>Total</span>
            <span id="summary-total">Rp 0</span>
          </div>
        </div>

        <button class="checkout-btn" onclick="doCheckout()">Checkout →</button>
      </div>
    </div>
  </div>
