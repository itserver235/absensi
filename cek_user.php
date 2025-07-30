<?php
// Jika belum login, redirect ke index
if (!isset($_SESSION['nip']) || !isset($_SESSION['jenis_user'])) {
  header("Location: ../index.php");
  exit;
}

// Jika halaman khusus admin, tapi user biasa mencoba masuk, redirect ke dashboard user
if (isset($only_admin) && $only_admin && $_SESSION['jenis_user'] != '1') {
  header("Location: ../user/dashboard.php");
  exit;
}

// Jika halaman khusus user, tapi admin mencoba masuk, redirect ke dashboard admin
if (isset($only_user) && $only_user && $_SESSION['jenis_user'] == '1') {
  header("Location: ../admin/dashboard_admin.php");
  exit;
}
