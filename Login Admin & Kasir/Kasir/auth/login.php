<?php
session_start();
require_once '../includes/db.php';

if (isset($_SESSION['kasir_logged_in'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Kredensial kasir — bisa diganti dengan query DB
    if ($username === 'Kasir' && $password === '123') {
        $_SESSION['kasir_logged_in'] = true;
        $_SESSION['kasir_user']      = $username;
        header('Location: ../dashboard/index.php');
        exit;
    } else {
        $error = 'Username atau password kasir salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kasir - Teras JTI</title>
    <link rel="stylesheet" href="../assets/css/kasir.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-wrapper">

    <div class="auth-card">
        <div class="auth-logo">
            <svg width="64" height="64" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="45" stroke="#F59E0B" stroke-width="2" stroke-dasharray="10 5"/>
                <rect x="22" y="30" width="56" height="40" rx="6" fill="#F59E0B" fill-opacity=".15" stroke="#F59E0B" stroke-width="3"/>
                <path d="M32 44h36M32 54h22" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
                <circle cx="68" cy="54" r="8" fill="#F59E0B"/>
                <path d="M65 54l2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="brand-name">TERAS JTI</div>
            <div style="font-size:.7rem; color:var(--text-muted); font-weight:700; margin-top:-6px; letter-spacing:.1em;">KASIR PANEL</div>
        </div>

        <h2 class="auth-title">Selamat Datang, Kasir 👋</h2>

        <?php if ($error): ?>
            <div style="background:rgba(239,68,68,.1); color:var(--error); padding:.875rem 1rem; border-radius:var(--radius-sm); margin-bottom:1.25rem; font-size:.875rem; font-weight:600; text-align:left; display:flex; align-items:center; gap:.5rem;">
                <i class="fa-solid fa-circle-xmark"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fa-solid fa-user input-icon"></i>
                    <input type="text" name="username" class="form-control" placeholder="Username Kasir" required autocomplete="username">
                </div>
            </div>
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-right-to-bracket" style="margin-right:.5rem;"></i>Masuk ke Kasir
            </button>
        </form>

        <div style="margin-top:2rem; font-size:.8rem; color:var(--text-muted);">
            &copy; <?= date('Y') ?> Teras JTI Management System
        </div>
    </div>

</body>
</html>
