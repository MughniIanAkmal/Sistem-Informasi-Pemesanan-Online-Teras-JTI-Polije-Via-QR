<?php
/**
 * TERAS JTI — One-Click Complete Setup
 * ======================================
 * Open in browser: http://localhost/Project Smster 2/setup.php
 * Run this ONCE to set up everything.
 */
require_once 'includes/db.php';
$results = [];
$errors  = [];

function ok($msg)  { global $results; $results[] = $msg; }
function err($msg) { global $errors;  $errors[]  = $msg; }

// ── 1. Tables ──
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS produk (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(255) NOT NULL, harga DECIMAL(10,2) NOT NULL, kategori VARCHAR(50) NOT NULL, gambar VARCHAR(255), deskripsi TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    ok("Tabel <code>produk</code> – OK");
} catch (PDOException $e) { err("produk: " . $e->getMessage()); }

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS pesanan (id INT AUTO_INCREMENT PRIMARY KEY, nomor_meja VARCHAR(50) DEFAULT 'Tidak Diketahui', total_harga DECIMAL(10,2) NOT NULL, metode_pembayaran VARCHAR(50), status VARCHAR(20) DEFAULT 'Masuk', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    ok("Tabel <code>pesanan</code> – OK");
} catch (PDOException $e) { err("pesanan: " . $e->getMessage()); }

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS detail_pesanan (id INT AUTO_INCREMENT PRIMARY KEY, pesanan_id INT NOT NULL, produk_id INT, nama_produk VARCHAR(255), jumlah INT NOT NULL, subtotal DECIMAL(10,2) NOT NULL, FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE, FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    ok("Tabel <code>detail_pesanan</code> – OK");
} catch (PDOException $e) { err("detail_pesanan: " . $e->getMessage()); }

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (`key` VARCHAR(100) PRIMARY KEY, `value` TEXT NOT NULL, `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    ok("Tabel <code>settings</code> – OK");
} catch (PDOException $e) { err("settings: " . $e->getMessage()); }

// ── 2. Column migrations ──
try {
    if (empty($pdo->query("SHOW COLUMNS FROM pesanan LIKE 'nomor_meja'")->fetchAll())) {
        $pdo->exec("ALTER TABLE pesanan ADD COLUMN nomor_meja VARCHAR(50) DEFAULT 'Tidak Diketahui' AFTER id");
        ok("Kolom <code>nomor_meja</code> ditambahkan ke pesanan");
    }
    if (empty($pdo->query("SHOW COLUMNS FROM detail_pesanan LIKE 'nama_produk'")->fetchAll())) {
        $pdo->exec("ALTER TABLE detail_pesanan ADD COLUMN nama_produk VARCHAR(255) AFTER produk_id");
        ok("Kolom <code>nama_produk</code> ditambahkan ke detail_pesanan");
    }
} catch (PDOException $e) { err("ALTER: " . $e->getMessage()); }

// ── 3. Default settings ──
try {
    $defaults = ['jam_buka'=>'07:00','jam_tutup'=>'22:00','hari_buka'=>'0,1,2,3,4,5,6','toko_tutup'=>'0','pesan_tutup'=>'Teras JTI sedang tutup. Kami akan segera kembali!','nama_toko'=>'Teras JTI','tagline_toko'=>'Pesan menu favoritmu sekarang'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (`key`, `value`) VALUES (?, ?)");
    foreach ($defaults as $k => $v) $stmt->execute([$k, $v]);
    ok("Default settings berhasil dimasukkan");
} catch (PDOException $e) { err("settings: " . $e->getMessage()); }

// ── 4. Menu Seeder ──
$img = ['nasi'=>'assets/img/produk/nasi-telor-sambal.jpg','kolagen'=>'assets/img/produk/noodle-kolagen.jpg','chilli'=>'assets/img/produk/chilli-oil.jpg','ayam'=>'assets/img/produk/ayam-pedas.jpg','soto'=>'assets/img/produk/soto-ayam.jpg','lalapan'=>'assets/img/produk/nasi-lalapan.jpg','mala'=>'assets/img/produk/noodle-mala.jpg'];
$menus = [
    ['Nasi Telor Sambal',10000,'makanan',$img['nasi'],'Nasi daun jeruk dengan tambahan telur kribo, sambal dan sayur.'],
    ['Nasi Telor Cakalang',15000,'makanan',$img['nasi'],'Nasi daun jeruk dengan tambahan telur kribo, tumis cakalang dan sayur.'],
    ['Nasi Telor Cumi',18000,'makanan',$img['nasi'],'Nasi daun jeruk dengan tambahan telur kribo, tumis cumi dan sayur.'],
    ['Nasi Telor Daging',20000,'makanan',$img['nasi'],'Nasi daun jeruk dengan tambahan telur kribo, tumis sambal daging dan sayur.'],
    ['Nasi Lalapan',17000,'makanan',$img['lalapan'],'Nasi putih dengan tambahan ayam goreng, tempe goreng dan sayur lalapan.'],
    ['Nasi Katsu',17000,'makanan',$img['lalapan'],'Nasi putih dengan tambahan ayam katsu dan saus katsu.'],
    ['Noodle Kolagen Ori',19000,'makanan',$img['kolagen'],'Noodles with collagen broth, milk and minced chicken.'],
    ['Noodle Kolagen Beef',21000,'makanan',$img['kolagen'],'Noodles with collagen broth, milk and beef slice.'],
    ['Noodle Kolagen Charsiu',21000,'makanan',$img['kolagen'],'Noodles with collagen broth, milk and chicken charsiu.'],
    ['Noodle Kolagen Katsu',20000,'makanan',$img['kolagen'],'Noodles with collagen broth, milk and chicken katsu.'],
    ['Noodle Mala Ori',20000,'makanan',$img['mala'],'Noodles with collagen broth, milk, chilli oil and minced chicken. *Level 1-3'],
    ['Noodle Mala Beef',22000,'makanan',$img['mala'],'Noodles with collagen broth, milk, chilli oil and beef slice. *Level 1-3'],
    ['Noodle Mala Charsiu',22000,'makanan',$img['mala'],'Noodles with collagen broth, milk, chilli oil and chicken charsiu. *Level 1-3'],
    ['Noodle Mala Katsu',21000,'makanan',$img['mala'],'Noodles with collagen broth, milk, chilli oil and chicken katsu. *Level 1-3'],
    ['Chilli Oil Ori',13000,'makanan',$img['chilli'],'Noodles with chilli oil and minced chicken. *Level 1-3'],
    ['Chilli Oil Beef',21000,'makanan',$img['chilli'],'Noodles with chilli oil and beef slice. *Level 1-3'],
    ['Chilli Oil Charsiu',19000,'makanan',$img['chilli'],'Noodles with chilli oil and chicken charsiu. *Level 1-3'],
    ['Chilli Oil Katsu',16000,'makanan',$img['chilli'],'Noodles with chilli oil and chicken katsu. *Level 1-3'],
    ['Ayam Pedas Teras',18000,'makanan',$img['ayam'],'Nasi dengan ayam kuah pedas, santan, cabe rawit, bawang putih, jahe, lengkuas, serai.'],
    ['Soto Ayam Teras',12000,'makanan',$img['soto'],'Nasi dengan sup ayam, mie bihun, kol, tomat, telur dan irisan daging ayam.'],
];
$menuCount = 0;
try {
    $stmtM = $pdo->prepare("INSERT INTO produk (nama, harga, kategori, gambar, deskripsi) VALUES (?,?,?,?,?)");
    $stmtEx= $pdo->prepare("SELECT id FROM produk WHERE nama = ?");
    foreach ($menus as $m) {
        $stmtEx->execute([$m[0]]);
        if (!$stmtEx->fetch()) { $stmtM->execute($m); $menuCount++; }
    }
    ok("$menuCount menu baru ditambahkan ke database (total " . count($menus) . " menu)");
} catch (PDOException $e) { err("Menu seeder: " . $e->getMessage()); }

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup - Teras JTI</title>
<style>
  body { font-family: sans-serif; background: #f0faf0; margin: 0; padding: 2rem; }
  .card { background: white; border-radius: 16px; padding: 2rem; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
  h1 { color: #1B5E20; margin-bottom: 0.5rem; }
  .sub { color: #666; margin-bottom: 1.5rem; font-size: 0.9rem; }
  .item { padding: 8px 0; border-bottom: 1px solid #e8f5e9; font-size: 0.9rem; }
  .item::before { content: '✅ '; }
  .err { padding: 8px 0; font-size: 0.9rem; color: #c62828; }
  .err::before { content: '❌ '; }
  .links { margin-top: 2rem; display: flex; flex-wrap: wrap; gap: 1rem; }
  .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 0.875rem; }
  .btn-green { background: #2E7D32; color: white; }
  .btn-gray  { background: #f5f5f5; color: #333; border: 1px solid #ddd; }
</style>
</head>
<body>
<div class="card">
  <h1>🌿 Setup Teras JTI</h1>
  <div class="sub">Inisialisasi database dan data menu selesai!</div>
  <?php foreach ($results as $r): ?><div class="item"><?= $r ?></div><?php endforeach; ?>
  <?php foreach ($errors  as $e): ?><div class="err"><?= $e ?></div><?php endforeach; ?>
  <div class="links">
    <a href="User/index.php" class="btn btn-green">🍽️ Buka Halaman User</a>
    <a href="Login Admin &amp; Kasir/Admin/auth/login.php" class="btn btn-green">🔒 Login Admin</a>
    <a href="verify_setup.php" class="btn btn-gray">🔍 Verifikasi Setup</a>
  </div>
</div>
</body>
</html>
