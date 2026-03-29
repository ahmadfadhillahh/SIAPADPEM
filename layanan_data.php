<?php
require_once __DIR__ . '/includes/public_data.php';

header('Content-Type: application/json; charset=utf-8');

$filters = [
    'opd' => $_GET['opd'] ?? '',
    'bulan' => $_GET['bulan'] ?? '',
    'triwulan' => $_GET['triwulan'] ?? '',
];

echo json_encode([
    'ok' => true,
    'data' => getLayanan($filters),
]);
