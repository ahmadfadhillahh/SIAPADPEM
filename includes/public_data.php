<?php
require_once __DIR__ . '/helpers.php';

function getSetting(string $key, string $default = ''): string
{
    $stmt = getPDO()->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();
    return $value !== false ? (string) $value : $default;
}

function getStruktur(): array
{
    return getPDO()->query('SELECT * FROM struktur_organisasi ORDER BY urutan ASC, id DESC')->fetchAll();
}

function getKegiatan(int $limit = 6, int $offset = 0): array
{
    $stmt = getPDO()->prepare('SELECT * FROM publikasi_kegiatan ORDER BY tanggal_publikasi DESC, id DESC LIMIT :l OFFSET :o');
    $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}


function getKegiatanById(int $id): ?array
{
    $stmt = getPDO()->prepare('SELECT * FROM publikasi_kegiatan WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getKegiatanTotal(): int
{
    return (int) getPDO()->query('SELECT COUNT(*) FROM publikasi_kegiatan')->fetchColumn();
}

function getDokumen(): array
{
    return getPDO()->query('SELECT * FROM publikasi_dokumen ORDER BY tanggal_publikasi DESC, id DESC')->fetchAll();
}

function getLayanan(array $filters = []): array
{
    $sql = 'SELECT * FROM realisasi_layanan WHERE 1=1';
    $params = [];

    if (!empty($filters['opd'])) {
        $sql .= ' AND opd = :opd';
        $params[':opd'] = $filters['opd'];
    }
    if (!empty($filters['bulan'])) {
        $sql .= ' AND bulan = :bulan';
        $params[':bulan'] = (int) $filters['bulan'];
    }
    if (!empty($filters['triwulan'])) {
        $sql .= ' AND triwulan = :triwulan';
        $params[':triwulan'] = $filters['triwulan'];
    }

    $sql .= ' ORDER BY tahun DESC, bulan ASC';
    $stmt = getPDO()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getDistinctOPD(): array
{
    return getPDO()->query('SELECT DISTINCT opd FROM realisasi_layanan ORDER BY opd ASC')->fetchAll(PDO::FETCH_COLUMN);
}
