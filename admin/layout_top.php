<?php require_once __DIR__ . '/_init.php'; requireLogin(); ?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body class="admin-page">
<div class="admin-layout">
  <aside class="sidebar">
    <h3>Dashboard Admin</h3>
    <p><?= e($_SESSION['admin_name'] ?? 'Admin') ?></p>
    <a href="index.php">Ringkasan</a>
    <a href="struktur.php">Struktur Organisasi</a>
    <a href="layanan.php">Layanan Realisasi Fisik dan Keuangan</a>
    <a href="kegiatan.php">Publikasi Kegiatan</a>
    <a href="dokumen.php">Publikasi Dokumen</a>
    <a href="logout.php">Logout</a>
  </aside>
  <main class="main">
    <?php if ($ok = flash('success')): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>
    <?php if ($er = flash('error')): ?><div class="alert error"><?= e($er) ?></div><?php endif; ?>
