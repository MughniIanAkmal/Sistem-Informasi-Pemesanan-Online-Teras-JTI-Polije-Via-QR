<?php
session_start();
unset($_SESSION['kasir_logged_in'], $_SESSION['kasir_user']);
header('Location: ../../index.php');
exit;
?>
