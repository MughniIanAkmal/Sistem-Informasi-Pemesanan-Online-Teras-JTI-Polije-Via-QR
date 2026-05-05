<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['kasir_logged_in'])) {
    header('Location: ../../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Dashboard - Teras JTI</title>
    <link rel="stylesheet" href="../assets/css/kasir.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
