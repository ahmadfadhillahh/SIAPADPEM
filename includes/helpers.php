<?php
require_once __DIR__ . '/db.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $msg = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function uploadFile(array $file, string $directory): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed, true)) {
        return null;
    }

    $name = uniqid('file_', true) . '.' . $extension;
    $targetDir = __DIR__ . '/../assets/uploads/' . $directory;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $targetFile = $targetDir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        return null;
    }

    return 'assets/uploads/' . $directory . '/' . $name;
}

function deleteUploadedFile(?string $path): void
{
    if (!$path) {
        return;
    }

    $fullPath = __DIR__ . '/../' . ltrim($path, '/');
    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}
