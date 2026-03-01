<?php
require_once __DIR__ . '/_init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = getPDO()->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($password, $admin['password_hash'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Username atau password salah']);
    exit;
}

$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_name'] = $admin['full_name'];

echo json_encode(['ok' => true, 'redirect' => 'admin/index.php']);
