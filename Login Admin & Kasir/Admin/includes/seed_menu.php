<?php
/**
 * Menu Seeder - Teras JTI
 * Run via browser: http://localhost/Project Smster 2/Login Admin & Kasir/Admin/includes/seed_menu.php
 */
require_once 'db.php';

// Base path for images (relative to User folder)
$img = [
    'nasi'    => 'assets/img/produk/nasi-telor-sambal.jpg',
    'kolagen' => 'assets/img/produk/noodle-kolagen.jpg',
    'chilli'  => 'assets/img/produk/chilli-oil.jpg',
    'ayam'    => 'assets/img/produk/ayam-pedas.jpg',
    'soto'    => 'assets/img/produk/soto-ayam.jpg',
    'lalapan' => 'assets/img/produk/nasi-lalapan.jpg',
    'mala'    => 'assets/img/produk/noodle-mala.jpg',
];

$menus = [
    // NASI TELOR SAMBAL
    ['Nasi Telor Sambal',    10000, 'makanan', $img['nasi'],    'Nasi daun jeruk dengan tambahan telur kribo, sambal dan sayur.'],
    ['Nasi Telor Cakalang',  15000, 'makanan', $img['nasi'],    'Nasi daun jeruk dengan tambahan telur kribo, tumis cakalang dan sayur.'],
    ['Nasi Telor Cumi',      18000, 'makanan', $img['nasi'],    'Nasi daun jeruk dengan tambahan telur kribo, tumis cumi dan sayur.'],
    ['Nasi Telor Daging',    20000, 'makanan', $img['nasi'],    'Nasi daun jeruk dengan tambahan telur kribo, tumis sambal daging dan sayur. 👍'],

    // NASI LALAPAN
    ['Nasi Lalapan',         17000, 'makanan', $img['lalapan'], 'Nasi putih dengan tambahan ayam goreng, tempe goreng dan sayur lalapan.'],
    ['Nasi Katsu',           17000, 'makanan', $img['lalapan'], 'Nasi putih dengan tambahan ayam katsu dan saus katsu. 👍'],

    // NOODLE KOLAGEN
    ['Noodle Kolagen Ori',   19000, 'makanan', $img['kolagen'], 'Noodles with collagen broth, milk and minced chicken.'],
    ['Noodle Kolagen Beef',  21000, 'makanan', $img['kolagen'], 'Noodles with collagen broth, milk and beef slice.'],
    ['Noodle Kolagen Charsiu', 21000, 'makanan', $img['kolagen'], 'Noodles with collagen broth, milk and chicken charsiu.'],
    ['Noodle Kolagen Katsu', 20000, 'makanan', $img['kolagen'], 'Noodles with collagen broth, milk and chicken katsu.'],

    // NOODLE MALA
    ['Noodle Mala Ori',      20000, 'makanan', $img['mala'],    'Noodles with collagen broth, milk, chilli oil and minced chicken. *Level 1-3'],
    ['Noodle Mala Beef',     22000, 'makanan', $img['mala'],    'Noodles with collagen broth, milk, chilli oil and beef slice. *Level 1-3'],
    ['Noodle Mala Charsiu',  22000, 'makanan', $img['mala'],    'Noodles with collagen broth, milk, chilli oil and chicken charsiu. *Level 1-3'],
    ['Noodle Mala Katsu',    21000, 'makanan', $img['mala'],    'Noodles with collagen broth, milk, chilli oil and chicken katsu. *Level 1-3'],

    // CHILLI OIL
    ['Chilli Oil Ori',       13000, 'makanan', $img['chilli'],  'Noodles with chilli oil and minced chicken. *Level 1-3'],
    ['Chilli Oil Beef',      21000, 'makanan', $img['chilli'],  'Noodles with chilli oil and beef slice. *Level 1-3'],
    ['Chilli Oil Charsiu',   19000, 'makanan', $img['chilli'],  'Noodles with chilli oil and chicken charsiu. *Level 1-3'],
    ['Chilli Oil Katsu',     16000, 'makanan', $img['chilli'],  'Noodles with chilli oil and chicken katsu. *Level 1-3'],

    // MAKANAN TERAS
    ['Ayam Pedas Teras',     18000, 'makanan', $img['ayam'],    'Nasi dengan ayam kuah pedas yang dimasak dengan santan, cabe rawit, bawang putih, jahe, lengkuas, serai.'],
    ['Soto Ayam Teras',      12000, 'makanan', $img['soto'],    'Nasi dengan sup ayam, mie bihun, kol, tomat, telur dan irisan daging ayam.'],
];

echo "<h2>🌿 Teras JTI Menu Seeder</h2>";
echo "<hr>";

$stmt = $pdo->prepare("INSERT INTO produk (nama, harga, kategori, gambar, deskripsi) VALUES (?, ?, ?, ?, ?)");
$count = 0;
$errors = 0;

foreach ($menus as $m) {
    try {
        // Check if already exists
        $exists = $pdo->prepare("SELECT id FROM produk WHERE nama = ?");
        $exists->execute([$m[0]]);
        if ($exists->fetch()) {
            echo "⏭️ Dilewati (sudah ada): <strong>{$m[0]}</strong><br>";
            continue;
        }
        $stmt->execute($m);
        echo "✅ <strong>{$m[0]}</strong> – Rp " . number_format($m[1], 0, ',', '.') . "<br>";
        $count++;
    } catch (PDOException $e) {
        echo "❌ Gagal: {$m[0]} – " . $e->getMessage() . "<br>";
        $errors++;
    }
}

echo "<hr>";
echo "<strong style='color:green'>✅ $count menu berhasil ditambahkan.</strong>";
if ($errors) echo " <strong style='color:red'>❌ $errors gagal.</strong>";
?>
