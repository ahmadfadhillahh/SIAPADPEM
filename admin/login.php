<?php
require_once __DIR__ . '/_init.php';
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = getPDO()->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        header('Location: index.php');
        exit;
    }
    flash('error', 'Username atau password salah.');
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head><body style="background:#f1f5f9">
<div class="container" style="max-width:460px;padding:60px 0">
  <div class="card">
    <h2>Login Admin</h2>
    <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
    <form method="post">
      <label>Username</label><input name="username" required>
      <label>Password</label><input type="password" name="password" required>
      <button class="btn" type="submit" style="margin-top:10px">Masuk</button>
      <p>Default: <b>admin / admin123</b></p>
    </form>
  </div>
</div>
</body></html>
