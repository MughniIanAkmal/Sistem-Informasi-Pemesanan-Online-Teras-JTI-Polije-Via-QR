<?php
session_start();
// Include the database connection (using relative path from this new index.php)
require_once __DIR__ . '/../includes/db.php';

// Jika sudah login, redirect ke masing-masing dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: Admin/dashboard/index.php');
    exit;
}
if (isset($_SESSION['kasir_logged_in'])) {
    header('Location: Kasir/dashboard/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Cek Login Admin
    if (strtolower($username) === 'admin' && $password === '123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = 'Admin';
        header('Location: Admin/dashboard/index.php');
        exit;
    } 
    // Cek Login Kasir
    elseif (strtolower($username) === 'kasir' && $password === '123') {
        $_SESSION['kasir_logged_in'] = true;
        $_SESSION['kasir_user'] = 'Kasir';
        header('Location: Kasir/dashboard/index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universal Login - Teras JTI</title>
    <!-- Kita pakai CSS dari Admin sebagai base styling untuk universal login -->
    <link rel="stylesheet" href="Admin/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-wrapper">

    <div class="auth-card">
        <div class="auth-logo">
            <svg width="60" height="60" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="45" stroke="#0EA5E9" stroke-width="2" stroke-dasharray="10 5"/>
                <path d="M50 20C33.4315 20 20 33.4315 20 50C20 66.5685 33.4315 80 50 80C66.5685 80 80 66.5685 80 50" stroke="#0EA5E9" stroke-width="8" stroke-linecap="round"/>
                <circle cx="50" cy="50" r="10" fill="#0EA5E9"/>
            </svg>
            <div class="brand-name">TERAS JTI</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-top: -10px;">MANAGEMENT SYSTEM</div>
        </div>

        <h2 class="auth-title" style="margin-bottom: 0.5rem;">Selamat Datang 👋</h2>
        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">Masuk menggunakan akun <strong>Admin</strong> atau <strong>Kasir</strong> Anda.</p>

        <?php if ($error): ?>
            <div style="background: rgba(239,68,68,.1); color: var(--error); padding: 10px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.875rem; font-weight: 600; display:flex; align-items:center; justify-content:center; gap:.5rem;">
                <i class="fa-solid fa-circle-xmark"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fa-solid fa-user input-icon"></i>
                    <input type="text" name="username" class="form-control" placeholder="Username (Admin/Kasir)" required autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <i class="fa-solid fa-right-to-bracket" style="margin-right:.5rem;"></i> Masuk
            </button>
        </form>

        <div style="margin-top: 2rem; font-size: 0.8125rem; color: var(--text-muted);">
            &copy; <?= date('Y') ?> Teras JTI Management System
        </div>
    </div>

</body>
</html>
