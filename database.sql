CREATE DATABASE IF NOT EXISTS siapadpem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siapadpem;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT
);

CREATE TABLE IF NOT EXISTS struktur_organisasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(150) NOT NULL,
    foto_path VARCHAR(255),
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS realisasi_layanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    opd VARCHAR(150) NOT NULL,
    bulan TINYINT NOT NULL,
    triwulan ENUM('TW1','TW2','TW3','TW4') NOT NULL,
    tahun YEAR NOT NULL,
    realisasi_fisik DECIMAL(5,2) NOT NULL,
    realisasi_keuangan DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS publikasi_kegiatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    ringkasan TEXT,
    konten TEXT,
    penulis VARCHAR(100) NOT NULL,
    tanggal_publikasi DATE NOT NULL,
    gambar_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS publikasi_dokumen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    file_path VARCHAR(255) NOT NULL,
    tipe_dokumen ENUM('Publik','Terbatas') NOT NULL DEFAULT 'Publik',
    password_hash VARCHAR(255) NULL,
    tanggal_publikasi DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (username, password_hash, full_name)
VALUES ('admin', '$2y$12$aYwj4OiaIPtixy31M3OvNOxhYM9hs5zydq2fGC2ykmxgv1SUclB92', 'Administrator')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

INSERT INTO settings (setting_key, setting_value)
VALUES
('hero_title', 'Selamat Datang di Portal SIAPADPEM'),
('hero_subtitle', 'Sistem Informasi Administrasi Pemerintahan Daerah yang transparan, akuntabel, dan responsif.'),
('tupoksi_text', 'Tugas pokok dan fungsi mencakup perencanaan pembangunan, koordinasi antar perangkat daerah, pemantauan realisasi program, evaluasi capaian, serta pelaporan kinerja secara berkelanjutan untuk mendukung tata kelola pemerintahan yang baik.')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);


ALTER TABLE publikasi_dokumen
    ADD COLUMN IF NOT EXISTS tipe_dokumen ENUM('Publik','Terbatas') NOT NULL DEFAULT 'Publik' AFTER file_path,
    ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL AFTER tipe_dokumen;
