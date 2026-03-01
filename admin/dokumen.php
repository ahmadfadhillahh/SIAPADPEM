<?php
require_once __DIR__ . '/layout_top.php';
$pdo = getPDO();

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT file_path FROM publikasi_dokumen WHERE id=?');
    $stmt->execute([$id]);
    $old = $stmt->fetchColumn();
    $pdo->prepare('DELETE FROM publikasi_dokumen WHERE id=?')->execute([$id]);
    deleteUploadedFile($old ?: null);
    flash('success', 'Dokumen dihapus.');
    header('Location: dokumen.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM publikasi_dokumen WHERE id=?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tgl = $_POST['tanggal_publikasi'] ?? date('Y-m-d');
    $file = uploadFile($_FILES['file_dokumen'] ?? [], 'dokumen');

    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT file_path FROM publikasi_dokumen WHERE id=?');
        $stmt->execute([$id]);
        $old = $stmt->fetchColumn();
        $path = $file ?: $old;
        $pdo->prepare('UPDATE publikasi_dokumen SET judul=?, deskripsi=?, file_path=?, tanggal_publikasi=? WHERE id=?')
            ->execute([$judul, $deskripsi, $path, $tgl, $id]);
        if ($file && $old) deleteUploadedFile($old);
        flash('success', 'Dokumen diperbarui.');
    } else {
        if (!$file) {
            flash('error', 'File dokumen wajib diunggah.');
            header('Location: dokumen.php');
            exit;
        }
        $pdo->prepare('INSERT INTO publikasi_dokumen (judul, deskripsi, file_path, tanggal_publikasi) VALUES (?,?,?,?)')
            ->execute([$judul, $deskripsi, $file, $tgl]);
        flash('success', 'Dokumen ditambahkan.');
    }

    header('Location: dokumen.php');
    exit;
}

$rows = $pdo->query('SELECT * FROM publikasi_dokumen ORDER BY tanggal_publikasi DESC, id DESC')->fetchAll();
?>
<h1>Kelola Publikasi Dokumen</h1>
<div class="grid grid-2">
  <div class="card">
    <h3><?= $edit ? 'Edit' : 'Tambah' ?> Dokumen</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <label>Judul</label><input name="judul" required value="<?= e($edit['judul'] ?? '') ?>">
      <label>Tanggal Publikasi</label><input type="date" name="tanggal_publikasi" value="<?= e($edit['tanggal_publikasi'] ?? date('Y-m-d')) ?>">
      <label>Deskripsi</label><textarea name="deskripsi"><?= e($edit['deskripsi'] ?? '') ?></textarea>
      <label>File Dokumen (PDF/DOC)</label><input type="file" name="file_dokumen" accept=".pdf,.doc,.docx">
      <button class="btn" type="submit" style="margin-top:10px">Simpan</button>
    </form>
  </div>
  <div class="card table-wrap">
    <table class="table"><thead><tr><th>Tgl</th><th>Judul</th><th>Download</th><th>Aksi</th></tr></thead><tbody>
      <?php foreach ($rows as $r): ?>
      <tr><td><?= e($r['tanggal_publikasi']) ?></td><td><?= e($r['judul']) ?></td><td><a href="../<?= e($r['file_path']) ?>" download>Unduh</a></td><td><a href="?edit=<?= $r['id'] ?>">Edit</a> | <a data-confirm="Hapus data ini?" href="?delete=<?= $r['id'] ?>">Hapus</a></td></tr>
      <?php endforeach; ?>
    </tbody></table>
  </div>
</div>
<?php require_once __DIR__ . '/layout_bottom.php'; ?>
