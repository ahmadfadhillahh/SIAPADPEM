<?php
require_once __DIR__ . '/layout_top.php';
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['hero_title', 'hero_subtitle', 'tupoksi_text'];
    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($keys as $key) {
        $stmt->execute([$key, trim($_POST[$key] ?? '')]);
    }
    flash('success', 'Pengaturan berhasil disimpan.');
    header('Location: settings.php');
    exit;
}

$get = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
function setVal(PDOStatement $get, string $key): string {
    $get->execute([$key]);
    $val = $get->fetchColumn();
    return $val !== false ? $val : '';
}
?>
<h1>Kelola Teks Beranda & Tupoksi</h1>
<div class="card">
<form method="post">
  <label>Judul Hero</label>
  <input name="hero_title" value="<?= e(setVal($get, 'hero_title')) ?>">
  <label>Subtitle Hero</label>
  <textarea name="hero_subtitle"><?= e(setVal($get, 'hero_subtitle')) ?></textarea>
  <label>Tupoksi</label>
  <textarea name="tupoksi_text" rows="8"><?= e(setVal($get, 'tupoksi_text')) ?></textarea>
  <button class="btn" type="submit">Simpan</button>
</form>
</div>
<?php require_once __DIR__ . '/layout_bottom.php'; ?>
