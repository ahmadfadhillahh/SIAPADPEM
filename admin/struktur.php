<?php
require_once __DIR__ . '/layout_top.php';
$pdo = getPDO();

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT foto_path FROM struktur_organisasi WHERE id=?');
    $stmt->execute([$id]);
    $old = $stmt->fetchColumn();
    $pdo->prepare('DELETE FROM struktur_organisasi WHERE id=?')->execute([$id]);
    deleteUploadedFile($old ?: null);
    flash('success', 'Data struktur dihapus.');
    header('Location: struktur.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM struktur_organisasi WHERE id=?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $urutan = (int) ($_POST['urutan'] ?? 0);

    $fotoPath = uploadFile($_FILES['foto'] ?? [], 'struktur');

    if ($id > 0) {
        $existing = $pdo->prepare('SELECT foto_path FROM struktur_organisasi WHERE id=?');
        $existing->execute([$id]);
        $old = $existing->fetchColumn();
        $finalFoto = $fotoPath ?: $old;
        $pdo->prepare('UPDATE struktur_organisasi SET nama=?, jabatan=?, urutan=?, foto_path=? WHERE id=?')
            ->execute([$nama, $jabatan, $urutan, $finalFoto, $id]);
        if ($fotoPath && $old) deleteUploadedFile($old);
        flash('success', 'Data struktur diperbarui.');
    } else {
        $pdo->prepare('INSERT INTO struktur_organisasi (nama, jabatan, urutan, foto_path) VALUES (?,?,?,?)')
            ->execute([$nama, $jabatan, $urutan, $fotoPath]);
        flash('success', 'Data struktur ditambahkan.');
    }

    header('Location: struktur.php');
    exit;
}

$rows = $pdo->query('SELECT * FROM struktur_organisasi ORDER BY urutan ASC, id DESC')->fetchAll();
?>
<h1>Kelola Struktur Organisasi</h1>
<div class="grid grid-2">
  <div class="card">
    <h3><?= $edit ? 'Edit' : 'Tambah' ?> Data Struktur</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <label>Nama</label><input name="nama" required value="<?= e($edit['nama'] ?? '') ?>">
      <label>Jabatan</label><input name="jabatan" required value="<?= e($edit['jabatan'] ?? '') ?>">
      <label>Urutan</label><input name="urutan" type="number" value="<?= (int)($edit['urutan'] ?? 0) ?>">
      <label>Foto</label><input name="foto" type="file" accept="image/*">
      <button class="btn" type="submit" style="margin-top:10px">Simpan</button>
    </form>
  </div>
  <div class="card table-wrap">
    <table class="table"><thead><tr><th>Nama</th><th>Jabatan</th><th>Aksi</th></tr></thead><tbody>
      <?php foreach($rows as $r): ?>
      <tr><td><?= e($r['nama']) ?></td><td><?= e($r['jabatan']) ?></td><td><a href="?edit=<?= $r['id'] ?>">Edit</a> | <a data-confirm="Hapus data ini?" href="?delete=<?= $r['id'] ?>">Hapus</a></td></tr>
      <?php endforeach; ?>
    </tbody></table>
  </div>
</div>
<?php require_once __DIR__ . '/layout_bottom.php'; ?>
