<?php
require_once __DIR__ . '/layout_top.php';
$pdo = getPDO();

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM realisasi_layanan WHERE id=?')->execute([(int) $_GET['delete']]);
    flash('success', 'Data layanan dihapus.');
    header('Location: layanan.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM realisasi_layanan WHERE id=?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $data = [
        trim($_POST['opd'] ?? ''),
        (int) ($_POST['bulan'] ?? 1),
        $_POST['triwulan'] ?? 'TW1',
        (int) ($_POST['tahun'] ?? date('Y')),
        (float) ($_POST['realisasi_fisik'] ?? 0),
        (float) ($_POST['realisasi_keuangan'] ?? 0),
    ];

    if ($id > 0) {
        $data[] = $id;
        $pdo->prepare('UPDATE realisasi_layanan SET opd=?, bulan=?, triwulan=?, tahun=?, realisasi_fisik=?, realisasi_keuangan=? WHERE id=?')->execute($data);
        flash('success', 'Data layanan diperbarui.');
    } else {
        $pdo->prepare('INSERT INTO realisasi_layanan (opd, bulan, triwulan, tahun, realisasi_fisik, realisasi_keuangan) VALUES (?,?,?,?,?,?)')->execute($data);
        flash('success', 'Data layanan ditambahkan.');
    }

    header('Location: layanan.php');
    exit;
}

$rows = $pdo->query('SELECT * FROM realisasi_layanan ORDER BY tahun DESC, bulan DESC, id DESC')->fetchAll();
?>
<h1>Kelola Data Layanan SIMPERA</h1>
<div class="grid grid-2">
  <div class="card">
    <h3><?= $edit ? 'Edit' : 'Tambah' ?> Data Realisasi</h3>
    <form method="post">
      <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <label>OPD</label><input name="opd" required value="<?= e($edit['opd'] ?? '') ?>">
      <label>Bulan</label><input name="bulan" type="number" min="1" max="12" required value="<?= (int)($edit['bulan'] ?? 1) ?>">
      <label>Triwulan</label><select name="triwulan"><?php foreach(['TW1','TW2','TW3','TW4'] as $tw): ?><option value="<?= $tw ?>" <?= ($edit['triwulan'] ?? '')===$tw?'selected':'' ?>><?= $tw ?></option><?php endforeach; ?></select>
      <label>Tahun</label><input name="tahun" type="number" value="<?= (int)($edit['tahun'] ?? date('Y')) ?>">
      <label>Realisasi Fisik (%)</label><input name="realisasi_fisik" type="number" step="0.01" value="<?= e($edit['realisasi_fisik'] ?? 0) ?>">
      <label>Realisasi Keuangan (%)</label><input name="realisasi_keuangan" type="number" step="0.01" value="<?= e($edit['realisasi_keuangan'] ?? 0) ?>">
      <button class="btn" type="submit" style="margin-top:10px">Simpan</button>
    </form>
  </div>
  <div class="card table-wrap">
    <table class="table"><thead><tr><th>OPD</th><th>Bulan/TW</th><th>Fisik</th><th>Keuangan</th><th>Aksi</th></tr></thead><tbody>
    <?php foreach ($rows as $r): ?>
    <tr><td><?= e($r['opd']) ?></td><td><?= e($r['bulan']) ?>/<?= e($r['triwulan']) ?></td><td><?= e($r['realisasi_fisik']) ?>%</td><td><?= e($r['realisasi_keuangan']) ?>%</td><td><a href="?edit=<?= $r['id'] ?>">Edit</a> | <a data-confirm="Hapus data ini?" href="?delete=<?= $r['id'] ?>">Hapus</a></td></tr>
    <?php endforeach; ?>
    </tbody></table>
  </div>
</div>
<?php require_once __DIR__ . '/layout_bottom.php'; ?>
