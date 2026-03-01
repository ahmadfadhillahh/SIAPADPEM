<?php
require_once __DIR__ . '/includes/public_data.php';

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;
$filters = [
    'opd' => $_GET['opd'] ?? '',
    'bulan' => $_GET['bulan'] ?? '',
    'triwulan' => $_GET['triwulan'] ?? '',
];

$heroTitle = getSetting('hero_title', 'Portal SIAPADPEM');
$heroSubtitle = getSetting('hero_subtitle', 'Informasi publik dan layanan realisasi pembangunan daerah.');
$tupoksi = getSetting('tupoksi_text', '-');
$struktur = getStruktur();
$opds = getDistinctOPD();
$layanan = getLayanan($filters);
$kegiatan = getKegiatan($perPage, $offset);
$kegiatanTotal = getKegiatanTotal();
$totalPages = max(1, (int) ceil($kegiatanTotal / $perPage));
$dokumen = getDokumen();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <title>SIAPADPEM</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<nav class="navbar">
  <div class="container nav-wrap">
    <div class="brand">SIAPADPEM</div>
    <div class="nav-menu">
      <a href="#beranda">Beranda</a>
      <a href="#profil">Profil</a>
      <a href="#layanan">Layanan</a>
      <a href="#publikasi">Publikasi</a>
      <a href="#kontak">Kontak</a>
      <button type="button" class="btn outline" id="themeToggle" aria-label="Ganti tema">🌙 Mode Gelap</button>
      <a class="btn" href="admin/login.php">Login Admin</a>
    </div>
  </div>
</nav>

<section id="beranda" class="hero">
  <div class="container reveal">
    <h1><?= e($heroTitle) ?></h1>
    <p><?= e($heroSubtitle) ?></p>
  </div>
</section>

<section id="profil" class="section">
  <div class="container">
    <h2 class="section-title reveal">Profil - Tupoksi</h2>
    <div class="card reveal"><p><?= nl2br(e($tupoksi)) ?></p></div>

    <h2 class="section-title reveal" style="margin-top:30px">Struktur Organisasi</h2>
    <div class="slider-shell reveal">
      <div class="slider-controls">
        <button type="button" class="slider-btn" data-slide="prev" aria-label="Sebelumnya">‹</button>
        <button type="button" class="slider-btn" data-slide="next" aria-label="Selanjutnya">›</button>
      </div>
      <div class="slider" id="strukturSlider">
        <?php foreach ($struktur as $item): ?>
          <article class="card person">
            <img src="<?= e($item['foto_path'] ?: 'https://placehold.co/600x400?text=Foto') ?>" alt="<?= e($item['nama']) ?>">
            <h3><?= e($item['nama']) ?></h3>
            <p><?= e($item['jabatan']) ?></p>
          </article>
        <?php endforeach; ?>
        <?php if (!$struktur): ?><p>Belum ada data struktur organisasi.</p><?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section id="layanan" class="section" style="background:#f8fafc">
  <div class="container">
    <h2 class="section-title reveal">Layanan (SIMPERA) - Grafik Realisasi</h2>
    <form class="filters reveal" method="get">
      <input type="hidden" name="page" value="1">
      <div><label>OPD</label><select name="opd"><option value="">Semua OPD</option><?php foreach ($opds as $opd): ?><option value="<?= e($opd) ?>" <?= $filters['opd']===$opd?'selected':'' ?>><?= e($opd) ?></option><?php endforeach; ?></select></div>
      <div><label>Bulan</label><select name="bulan"><option value="">Semua Bulan</option><?php for($i=1;$i<=12;$i++): ?><option value="<?= $i ?>" <?= (string)$filters['bulan']===(string)$i?'selected':'' ?>><?= $i ?></option><?php endfor; ?></select></div>
      <div><label>Triwulan</label><select name="triwulan"><option value="">Semua TW</option><?php foreach (['TW1','TW2','TW3','TW4'] as $tw): ?><option value="<?= $tw ?>" <?= $filters['triwulan']===$tw?'selected':'' ?>><?= $tw ?></option><?php endforeach; ?></select></div>
      <div style="align-self:end"><button class="btn" type="submit">Filter</button></div>
    </form>
    <div class="card reveal"><canvas id="chartLayanan" height="110"></canvas></div>
  </div>
</section>

<section id="publikasi" class="section">
  <div class="container">
    <h2 class="section-title reveal">Publikasi Kegiatan</h2>
    <div class="grid grid-3">
      <?php foreach ($kegiatan as $item): ?>
      <article class="card pub-card reveal">
        <a href="kegiatan_detail.php?id=<?= (int) $item['id'] ?>" target="_blank" rel="noopener noreferrer">
          <img src="<?= e($item['gambar_path'] ?: 'https://placehold.co/600x400?text=Kegiatan') ?>" alt="<?= e($item['judul']) ?>">
          <small class="meta"><?= e(date('d M Y', strtotime($item['tanggal_publikasi']))) ?> • <?= e($item['penulis']) ?></small>
          <h3><?= e($item['judul']) ?></h3>
          <p><?= e($item['ringkasan']) ?></p>
          <span class="read-more">Baca selengkapnya →</span>
        </a>
      </article>
      <?php endforeach; ?>
    </div>

    <div class="pagination">
      <?php for($p=1; $p<=$totalPages; $p++): ?>
      <a class="btn <?= $p===$page?'':'outline' ?>" href="?page=<?= $p ?>#publikasi"><?= $p ?></a>
      <?php endfor; ?>
    </div>

    <h2 class="section-title reveal" style="margin-top:30px">Publikasi Dokumen</h2>
    <div class="table-wrap card reveal">
      <table class="table">
        <thead><tr><th>Judul</th><th>Tanggal</th><th>Unduh</th></tr></thead>
        <tbody>
          <?php foreach ($dokumen as $doc): ?>
          <tr>
            <td><?= e($doc['judul']) ?></td>
            <td><?= e(date('d M Y', strtotime($doc['tanggal_publikasi']))) ?></td>
            <td><a class="btn" href="<?= e($doc['file_path']) ?>" download>Download</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$dokumen): ?><tr><td colspan="3">Belum ada dokumen.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<footer id="kontak" class="footer">
  <div class="container grid grid-2">
    <div>
      <h4>Kontak</h4>
      <p>Email: siapadpem@example.go.id<br>Telepon: (021) 1234567<br>Alamat: Jl. Pemerintahan No. 1</p>
    </div>
    <div>
      <h4>Tentang</h4>
      <p>Website resmi SIAPADPEM untuk informasi profil, layanan SIMPERA, dan publikasi pemerintah daerah.</p>
    </div>
  </div>
</footer>

<script>
const layananData = <?= json_encode($layanan) ?>;
const chartEl = document.getElementById('chartLayanan');
if (chartEl && layananData.length) {
  const labels = layananData.map(item => `${item.opd} (${item.bulan}/${item.tahun})`);
  new Chart(chartEl, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { label: 'Realisasi Fisik (%)', data: layananData.map(item => item.realisasi_fisik), backgroundColor: '#004a99' },
        { label: 'Realisasi Keuangan (%)', data: layananData.map(item => item.realisasi_keuangan), backgroundColor: '#00a3d7' }
      ]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
}
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
