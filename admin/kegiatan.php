<?php
require_once __DIR__ . '/layout_top.php';
$pdo = getPDO();

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT gambar_path FROM publikasi_kegiatan WHERE id=?');
    $stmt->execute([$id]);
    $old = $stmt->fetchColumn();
    $pdo->prepare('DELETE FROM publikasi_kegiatan WHERE id=?')->execute([$id]);
    deleteUploadedFile($old ?: null);
    flash('success', 'Kegiatan dihapus.');
    header('Location: kegiatan.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM publikasi_kegiatan WHERE id=?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $ringkasan = trim($_POST['ringkasan'] ?? '');
    $konten = trim($_POST['konten'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tgl = $_POST['tanggal_publikasi'] ?? date('Y-m-d');
    $img = uploadFile($_FILES['gambar'] ?? [], 'kegiatan');

    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT gambar_path FROM publikasi_kegiatan WHERE id=?');
        $stmt->execute([$id]);
        $old = $stmt->fetchColumn();
        $path = $img ?: $old;
        $pdo->prepare('UPDATE publikasi_kegiatan SET judul=?, ringkasan=?, konten=?, penulis=?, tanggal_publikasi=?, gambar_path=? WHERE id=?')
            ->execute([$judul, $ringkasan, $konten, $penulis, $tgl, $path, $id]);
        if ($img && $old) deleteUploadedFile($old);
        flash('success', 'Kegiatan diperbarui.');
    } else {
        $pdo->prepare('INSERT INTO publikasi_kegiatan (judul, ringkasan, konten, penulis, tanggal_publikasi, gambar_path) VALUES (?,?,?,?,?,?)')
            ->execute([$judul, $ringkasan, $konten, $penulis, $tgl, $img]);
        flash('success', 'Kegiatan ditambahkan.');
    }
    header('Location: kegiatan.php');
    exit;
}

$rows = $pdo->query('SELECT * FROM publikasi_kegiatan ORDER BY tanggal_publikasi DESC, id DESC')->fetchAll();
?>
<h1>Kelola Publikasi Kegiatan</h1>
<div class="grid grid-2">
  <div class="card">
    <h3><?= $edit ? 'Edit' : 'Tambah' ?> Kegiatan</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <label>Judul</label><input name="judul" required value="<?= e($edit['judul'] ?? '') ?>">
      <label>Penulis</label><input name="penulis" required value="<?= e($edit['penulis'] ?? '') ?>">
      <label>Tanggal Publikasi</label><input type="date" name="tanggal_publikasi" value="<?= e($edit['tanggal_publikasi'] ?? date('Y-m-d')) ?>">
      <label>Ringkasan</label><textarea name="ringkasan"><?= e($edit['ringkasan'] ?? '') ?></textarea>
      <label>Konten</label><textarea name="konten" rows="5"><?= e($edit['konten'] ?? '') ?></textarea>
      <label>Gambar</label><input type="file" name="gambar" accept="image/*">
      <button class="btn" type="submit" style="margin-top:10px">Simpan</button>
    </form>
  </div>
  <div class="card table-wrap">
    <table class="table"><thead><tr><th>Tgl</th><th>Judul</th><th>Penulis</th><th>Aksi</th></tr></thead><tbody>
      <?php foreach ($rows as $r): ?>
      <tr><td><?= e($r['tanggal_publikasi']) ?></td><td><?= e($r['judul']) ?></td><td><?= e($r['penulis']) ?></td><td><a href="?edit=<?= $r['id'] ?>">Edit</a> | <a data-confirm="Hapus data ini?" href="?delete=<?= $r['id'] ?>">Hapus</a></td></tr>
      <?php endforeach; ?>
    </tbody></table>
  </div>
</div>
<?php require_once __DIR__ . '/layout_bottom.php'; ?>
