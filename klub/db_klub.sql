-- =====================================================
--  DATABASE EXPORT: db_klub
--  Sistem Informasi Manajemen Anggota Klub Basket
--  Dibuat: 2026-10-30
-- =====================================================

CREATE DATABASE IF NOT EXISTS `db_klub`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `db_klub`;

-- -------------------------------------------------------
-- Tabel: users  (akun login)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`       INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nama`     VARCHAR(100)    NOT NULL,
    `username` VARCHAR(50)     NOT NULL UNIQUE,
    `password` VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun default: username=admin | password=admin123
INSERT INTO `users` (`nama`, `username`, `password`) VALUES
('Administrator', 'admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- -------------------------------------------------------
-- Tabel: anggota
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `anggota` (
    `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `no_anggota`      VARCHAR(20)     NOT NULL UNIQUE,
    `nama`            VARCHAR(100)    NOT NULL,
    `email`           VARCHAR(150)    NOT NULL,
    `divisi`          VARCHAR(80)     NOT NULL,
    `tahun_bergabung` YEAR            NOT NULL,
    `alamat`          TEXT            NULL,
    `dibuat_pada`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jika database sudah ada, pastikan kolom dibuat_pada tersedia:
-- ALTER TABLE `anggota` ADD COLUMN `dibuat_pada` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `alamat`;

-- Data contoh anggota Klub Basket
INSERT INTO `anggota` (`no_anggota`, `nama`, `email`, `divisi`, `tahun_bergabung`, `alamat`) VALUES
('KLB-001', 'Rizky Aditya',       'rizky@klubbasket.com',   'Manajerial',              2021, 'Jl. Pahlawan No. 12, Bandung'),
('KLB-002', 'Siti Nurhaliza',     'siti@klubbasket.com',    'Administrasi',            2022, 'Jl. Diponegoro No. 5, Bandung'),
('KLB-003', 'Budi Setiawan',      'budi@klubbasket.com',    'Kepelatihan',             2020, 'Jl. Merdeka No. 3, Bandung'),
('KLB-004', 'Dina Marlina',       'dina@klubbasket.com',    'Keuangan',                2022, 'Jl. Sudirman No. 88, Bandung'),
('KLB-005', 'Fauzan Ramadhan',    'fauzan@klubbasket.com',  'Humas & Publikasi',       2023, 'Jl. Asia Afrika No. 21, Bandung'),
('KLB-006', 'Gilang Permana',     'gilang@klubbasket.com',  'Logistik & Perlengkapan', 2021, 'Jl. Cihampelas No. 44, Bandung'),
('KLB-007', 'Hana Pertiwi',       'hana@klubbasket.com',    'Medis & Kesehatan',       2023, 'Jl. Braga No. 7, Bandung'),
('KLB-008', 'Irfan Hakim',        'irfan@klubbasket.com',   'Sponsorship',             2022, 'Jl. Pasirkaliki No. 10, Bandung'),
('KLB-009', 'Julia Savitri',      'julia@klubbasket.com',   'Media & Dokumentasi',     2024, 'Jl. Riau No. 15, Bandung'),
('KLB-010', 'Kevin Nugraha',      'kevin@klubbasket.com',   'Kepelatihan',             2020, 'Jl. Gatot Subroto No. 33, Bandung');
