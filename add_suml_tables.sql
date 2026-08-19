-- ============================================
-- MIGRATION: Tabel SUML & Peralatan
-- Untuk menyimpan data SUML per permohonan
-- ============================================

-- Tabel detail SUML per permohonan
CREATE TABLE IF NOT EXISTS permohonan_suml (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    permohonan_id   INT NOT NULL,
    kategori        VARCHAR(50) NOT NULL COMMENT 'Panjang, Volume, Massa, Waktu, Listrik, Suhu, Pendukung',
    nama_suml       VARCHAR(180) NOT NULL,
    tipe            ENUM('A','B') NOT NULL DEFAULT 'A' COMMENT 'A=Peralatan Utama, B=Peralatan Pendukung',
    jumlah_unit     INT DEFAULT NULL,
    kapasitas_nominal VARCHAR(100) DEFAULT NULL,
    daya_baca       VARCHAR(100) DEFAULT NULL,
    kelas           VARCHAR(20) DEFAULT NULL,
    nominal         VARCHAR(50) DEFAULT NULL,
    kepemilikan     ENUM('sendiri','kso') DEFAULT NULL,
    jenis_verifikasi ENUM('eksternal','internal') DEFAULT NULL,
    verif_terakhir  DATE DEFAULT NULL,
    verif_mendatang DATE DEFAULT NULL,
    lampiran_skhp   VARCHAR(255) DEFAULT NULL COMMENT 'Path file Lampiran SKHP',
    fields_json     JSON DEFAULT NULL COMMENT 'Special fields per item (custom_data)',
    lainnya_nama    VARCHAR(180) DEFAULT NULL COMMENT 'Jika pilih Lainnya, isi nama manual',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (permohonan_id) REFERENCES permohonan(id) ON DELETE CASCADE,
    INDEX idx_permohonan (permohonan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel komponen untuk Type B (Anak Timbangan, Bourje, dll)
CREATE TABLE IF NOT EXISTS permohonan_suml_komponen (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    permohonan_suml_id  INT NOT NULL,
    nama_komponen       VARCHAR(100) NOT NULL COMMENT 'Contoh: 1 mg, 2 kg, 5 kg + Pengait 5 kg',
    jumlah              INT DEFAULT NULL,
    daya_baca           VARCHAR(100) DEFAULT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (permohonan_suml_id) REFERENCES permohonan_suml(id) ON DELETE CASCADE,
    INDEX idx_suml (permohonan_suml_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
