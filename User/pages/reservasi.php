
  <!-- ═══════════════════════════════════ PAGE: RESERVASI ═══════════════════════════════════ -->
  <div class="page" id="page-reservasi">
    <div class="header-simple">
      <h1>RESERVASI TERAS JTI</h1>
      <p>Lengkapi data dirimu dibawah!</p>
    </div>
    <div class="reservasi-form">
      <div class="form-card">
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" class="form-input" placeholder="Masukkan nama lengkap" id="res-nama">
        </div>
        <div class="form-group">
          <label>Nomor Telepon</label>
          <input type="tel" class="form-input" placeholder="08xxxxxxxxxx" id="res-telp">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" class="form-input" placeholder="user@gmail.com" id="res-email">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Pilih Tanggal</label>
            <input type="date" class="form-input" id="res-tanggal">
          </div>
          <div class="form-group">
            <label>Pilih Waktu</label>
            <input type="time" class="form-input" id="res-waktu">
          </div>
        </div>
        <div class="form-group">
          <label>Jumlah Tamu</label>
          <div class="guest-control">
            <button class="guest-btn" onclick="changeGuest(-1)">−</button>
            <span class="guest-num" id="guest-num">5</span>
            <button class="guest-btn" onclick="changeGuest(1)">+</button>
          </div>
        </div>
        <div class="form-group">
          <label>Catatan (opsional)</label>
          <textarea class="form-input" placeholder="Tulis catatan disini" id="res-catatan" style="height:80px;resize:none;"></textarea>
        </div>
        <div class="ringkasan-card">
          <div class="ringkasan-title">Ringkasan</div>
          <div class="ringkasan-row">
            <span>Jumlah Tamu</span>
            <span id="sum-tamu">5 orang</span>
          </div>
        </div>
        <button class="checkout-btn" onclick="submitReservasi()" style="margin-top:16px">Konfirmasi Reservasi</button>
      </div>
    </div>
  </div>
