<?php
require_once __DIR__ . '/layout_top.php';
$pdo = getPDO();

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT gambar_path, gambar_path_2, gambar_path_3 FROM publikasi_kegiatan WHERE id=?');
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    $pdo->prepare('DELETE FROM publikasi_kegiatan WHERE id=?')->execute([$id]);
    if ($old) {
        deleteUploadedFile($old['gambar_path'] ?? null);
        deleteUploadedFile($old['gambar_path_2'] ?? null);
        deleteUploadedFile($old['gambar_path_3'] ?? null);
    }
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
    $kontenRaw = trim($_POST['konten'] ?? '');
    $allowedTags = '<p><br><b><strong><i><em><u><ul><ol><li><h1><h2><h3><h4><blockquote><a><span><div>';
    $konten = strip_tags($kontenRaw, $allowedTags);
    $penulis = trim($_POST['penulis'] ?? '');
    $tgl = $_POST['tanggal_publikasi'] ?? date('Y-m-d');

    $img1 = uploadFile($_FILES['gambar'] ?? [], 'kegiatan');

    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT gambar_path FROM publikasi_kegiatan WHERE id=?');
        $stmt->execute([$id]);
        $old = $stmt->fetchColumn();

        $path1 = $img1 ?: $old;

        $pdo->prepare('UPDATE publikasi_kegiatan SET judul=?, ringkasan=?, konten=?, penulis=?, tanggal_publikasi=?, gambar_path=?, gambar_path_2=NULL, gambar_path_3=NULL WHERE id=?')
            ->execute([$judul, $ringkasan, $konten, $penulis, $tgl, $path1, $id]);

        if ($img1 && !empty($old)) deleteUploadedFile($old);

        flash('success', 'Kegiatan diperbarui.');
    } else {
        $pdo->prepare('INSERT INTO publikasi_kegiatan (judul, ringkasan, konten, penulis, tanggal_publikasi, gambar_path, gambar_path_2, gambar_path_3) VALUES (?,?,?,?,?,?,NULL,NULL)')
            ->execute([$judul, $ringkasan, $konten, $penulis, $tgl, $img1]);
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
    <form method="post" enctype="multipart/form-data" id="kegiatanForm">
      <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <label>Judul</label><input name="judul" required value="<?= e($edit['judul'] ?? '') ?>">
      <label>Penulis</label><input name="penulis" required value="<?= e($edit['penulis'] ?? '') ?>">
      <label>Tanggal Publikasi</label><input type="date" name="tanggal_publikasi" value="<?= e($edit['tanggal_publikasi'] ?? date('Y-m-d')) ?>">
      <label>Ringkasan</label><textarea name="ringkasan"><?= e($edit['ringkasan'] ?? '') ?></textarea>

      <label>Konten (Editor)</label>
      <div class="editor-toolbar">
        <button type="button" data-editor-cmd="bold"><b>B</b></button>
        <button type="button" data-editor-cmd="italic"><i>I</i></button>
        <button type="button" data-editor-cmd="underline"><u>U</u></button>
        <button type="button" data-editor-cmd="insertUnorderedList">• List</button>
        <button type="button" data-editor-cmd="insertOrderedList">1. List</button>
        <select id="fontSizeSelect">
          <option value="3">Font Normal</option>
          <option value="2">Kecil</option>
          <option value="4">Sedang</option>
          <option value="5">Besar</option>
          <option value="6">Sangat Besar</option>
        </select>
      </div>
      <div id="kontenEditor" class="editor-content" contenteditable="true"><?= $edit ? $edit['konten'] : '' ?></div>
      <textarea name="konten" id="kontenInput" style="display:none"></textarea>

      <label>Gambar Utama (1 gambar)</label><input type="file" name="gambar" accept="image/*">
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
