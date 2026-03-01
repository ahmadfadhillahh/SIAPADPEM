<?php
require_once __DIR__ . '/includes/public_data.php';

$id = (int) ($_GET['id'] ?? 0);
$berita = $id > 0 ? getKegiatanById($id) : null;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $berita ? e($berita['judul']) : 'Kegiatan tidak ditemukan' ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <div class="container nav-wrap">
    <div class="brand">SIAPADPEM</div>
    <div class="nav-menu">
      <a href="index.php#beranda">Beranda</a>
      <a href="index.php#publikasi">Publikasi</a>
      <a class="btn" href="admin/login.php">Login Admin</a>
    </div>
  </div>
</nav>

<?php if (!$berita): ?>
<section class="section">
  <div class="container">
    <div class="card">
      <h1 class="article-title">Berita tidak ditemukan</h1>
      <p>Data kegiatan yang Anda cari tidak tersedia atau sudah dihapus.</p>
      <a class="btn" href="index.php#publikasi">Kembali ke Publikasi</a>
    </div>
  </div>
</section>
<?php else: ?>
<section class="article-hero">
  <div class="container">
    <a href="index.php#publikasi" class="read-more">← Kembali ke daftar kegiatan</a>
    <h1 class="article-title"><?= e($berita['judul']) ?></h1>
    <p class="article-meta"><?= e(date('d M Y', strtotime($berita['tanggal_publikasi']))) ?> • <?= e($berita['penulis']) ?></p>
  </div>
</section>

<section class="section" style="padding-top:20px">
  <div class="container">
    <article class="card">
      <img class="article-cover" src="<?= e($berita['gambar_path'] ?: 'https://placehold.co/1200x700?text=Kegiatan') ?>" alt="<?= e($berita['judul']) ?>">
      <p style="margin-top:20px;color:#64748b"><?= e($berita['ringkasan'] ?: '-') ?></p>
      <div class="article-content"><?= nl2br(e($berita['konten'] ?: 'Konten berita belum tersedia.')) ?></div>
    </article>
  </div>
</section>
<?php endif; ?>

<footer class="footer">
  <div class="container">
    <p>© <?= date('Y') ?> SIAPADPEM</p>
  </div>
</footer>
<script src="assets/js/app.js"></script>
</body>
</html>
