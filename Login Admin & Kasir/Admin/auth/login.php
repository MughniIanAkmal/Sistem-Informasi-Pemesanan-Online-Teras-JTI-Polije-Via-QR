<?php
session_start();
require_once '../includes/db.php';

// Cek jika sudah login
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simulasi login untuk demo - Silakan ganti dengan query database asli
    // Contoh: SELECT * FROM users WHERE username = ? AND role = 'admin'
    
    if ($username === 'Admin' && $password === '123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        header('Location: ../dashboard/index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Panel - Teras JTI</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-wrapper">

    <div class="auth-card">
        <div class="auth-logo">
            <!-- Icon SVG mirip logo di gambar -->
            <svg width="60" height="60" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="45" stroke="#0EA5E9" stroke-width="2" stroke-dasharray="10 5"/>
                <path d="M50 20C33.4315 20 20 33.4315 20 50C20 66.5685 33.4315 80 50 80C66.5685 80 80 66.5685 80 50" stroke="#0EA5E9" stroke-width="8" stroke-linecap="round"/>
                <circle cx="50" cy="50" r="10" fill="#0EA5E9"/>
            </svg>
            <div class="brand-name">TERAS JTI</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-top: -10px;">BY ARSENET</div>
        </div>

        <h2 class="auth-title">Selamat Datang Di Login Panel</h2>

        <?php if ($error): ?>
            <div style="background: var(--error); color: white; padding: 10px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.875rem;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fa-solid fa-user input-icon"></i>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
            </div>

            <button type="submit" class="btn-auth">Confirm</button>
        </form>

        <div style="margin-top: 2rem; font-size: 0.8125rem; color: var(--text-muted);">
            &copy; <?= date('Y') ?> Teras JTI Management System
        </div>
    </div>

</body>
</html>
