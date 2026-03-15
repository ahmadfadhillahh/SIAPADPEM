<?php
require_once __DIR__ . '/includes/helpers.php';

$id = (int) ($_REQUEST['id'] ?? 0);
$password = trim($_REQUEST['password'] ?? '');

if ($id <= 0) {
    http_response_code(400);
    exit('Dokumen tidak valid.');
}

$stmt = getPDO()->prepare('SELECT * FROM publikasi_dokumen WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) {
    http_response_code(404);
    exit('Dokumen tidak ditemukan.');
}

$tipe = $doc['tipe_dokumen'] ?? 'Publik';
if ($tipe === 'Terbatas') {
    if ($password === '' || empty($doc['password_hash']) || !password_verify($password, $doc['password_hash'])) {
        http_response_code(403);
        exit('Password dokumen salah atau belum diisi.');
    }
}

$filePath = __DIR__ . '/' . ltrim($doc['file_path'], '/');
if (!is_file($filePath)) {
    http_response_code(404);
    exit('File dokumen tidak ditemukan di server.');
}

$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$contentType = match ($ext) {
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    default => 'application/octet-stream',
};

header('Content-Description: File Transfer');
header('Content-Type: ' . $contentType);
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
header('Pragma: public');
readfile($filePath);
exit;
