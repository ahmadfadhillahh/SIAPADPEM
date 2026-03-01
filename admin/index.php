<?php
require_once __DIR__ . '/layout_top.php';
$pdo = getPDO();
$counts = [
    'struktur' => (int) $pdo->query('SELECT COUNT(*) FROM struktur_organisasi')->fetchColumn(),
    'layanan' => (int) $pdo->query('SELECT COUNT(*) FROM realisasi_layanan')->fetchColumn(),
    'kegiatan' => (int) $pdo->query('SELECT COUNT(*) FROM publikasi_kegiatan')->fetchColumn(),
    'dokumen' => (int) $pdo->query('SELECT COUNT(*) FROM publikasi_dokumen')->fetchColumn(),
];
?>
<h1>Ringkasan Website</h1>
<div class="grid grid-2">
  <div class="card"><h3>Struktur Organisasi</h3><p><?= $counts['struktur'] ?> data</p></div>
  <div class="card"><h3>Layanan SIMPERA</h3><p><?= $counts['layanan'] ?> data</p></div>
  <div class="card"><h3>Publikasi Kegiatan</h3><p><?= $counts['kegiatan'] ?> data</p></div>
  <div class="card"><h3>Publikasi Dokumen</h3><p><?= $counts['dokumen'] ?> data</p></div>
</div>
<?php require_once __DIR__ . '/layout_bottom.php'; ?>
