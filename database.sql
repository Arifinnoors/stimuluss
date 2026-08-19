-- =========================================================
-- DATABASE: (Sudah dibuat via Control Panel Hosting)
-- Sistem Informasi Monitoring untuk Layanan UTTP dan Standar Ukuran
-- Cara pakai: Pilih nama database di phpMyAdmin -> Import -> pilih file ini
-- =========================================================

-- ---------------------------------------------------------
-- Tabel: users
-- Menyimpan seluruh akun: UML, Koordinator, Verifikator, Ketua Tim, Direktur
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('uml','koordinator','verifikator','ketua_tim','direktur') NOT NULL,
    instansi VARCHAR(180) DEFAULT NULL COMMENT 'Nama Unit Metrologi Legal, khusus role uml',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: permohonan
-- Satu baris = satu pengajuan SKVI dari UML, berjalan melalui
-- alur status sesuai rancangan proses bisnis STIMULUS
-- ---------------------------------------------------------
CREATE TABLE permohonan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_permohonan VARCHAR(30) NOT NULL UNIQUE,
    uml_id INT NOT NULL,
    jenis_permohonan ENUM('baru','tambah','kurang') NOT NULL,
    nama_uttp VARCHAR(180) NOT NULL,
    deskripsi TEXT,
    file_permohonan VARCHAR(255) DEFAULT NULL,

    status VARCHAR(40) NOT NULL DEFAULT 'diajukan',
    -- status yang dipakai:
    -- diajukan, verifikasi_administrasi, dikembalikan_uml,
    -- verifikasi_berkas, penilaian, review_koordinator,
    -- review_ketua_tim, menunggu_ttd_direktur, selesai

    catatan_koordinator TEXT,
    catatan_verifikator TEXT,
    jadwal_zoom DATETIME DEFAULT NULL,
    hasil_penilaian TEXT,
    file_draft_skvi VARCHAR(255) DEFAULT NULL,

    nomor_skvi VARCHAR(60) DEFAULT NULL,
    file_skvi_final VARCHAR(255) DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (uml_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: log_aktivitas
-- Audit trail: siapa melakukan apa, kapan, dan perubahan statusnya
-- ---------------------------------------------------------
CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permohonan_id INT NOT NULL,
    user_id INT NOT NULL,
    status_sebelum VARCHAR(40) DEFAULT NULL,
    status_sesudah VARCHAR(40) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (permohonan_id) REFERENCES permohonan(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: notifikasi
-- Notifikasi dalam sistem: dibuat otomatis tiap kali status
-- permohonan berpindah ke tahap yang jadi tanggung jawab role lain.
-- ---------------------------------------------------------
CREATE TABLE notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    permohonan_id INT NOT NULL,
    pesan VARCHAR(255) NOT NULL,
    dibaca TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permohonan_id) REFERENCES permohonan(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- DATA AKUN DEMO (semua password: password123)
-- Silakan login pakai salah satu email di bawah untuk mencoba
-- alur tiap role.
-- =========================================================
INSERT INTO users (nama, email, password, role, instansi) VALUES
('Budi Santoso', 'uml@demo.com', '$2y$10$8UfLN075lAK7El0hmGCcHOc3x.wtIoKPgHl6c6DN3LoC6QVLtQWTm', 'uml', 'UML Kota Bandung'),
('Siti Aminah', 'koordinator@demo.com', '$2y$10$8UfLN075lAK7El0hmGCcHOc3x.wtIoKPgHl6c6DN3LoC6QVLtQWTm', 'koordinator', NULL),
('Rudi Hartono', 'verifikator@demo.com', '$2y$10$8UfLN075lAK7El0hmGCcHOc3x.wtIoKPgHl6c6DN3LoC6QVLtQWTm', 'verifikator', NULL),
('Dewi Lestari', 'ketuatim@demo.com', '$2y$10$8UfLN075lAK7El0hmGCcHOc3x.wtIoKPgHl6c6DN3LoC6QVLtQWTm', 'ketua_tim', NULL),
('Ahmad Fauzi', 'direktur@demo.com', '$2y$10$8UfLN075lAK7El0hmGCcHOc3x.wtIoKPgHl6c6DN3LoC6QVLtQWTm', 'direktur', NULL);

-- Contoh satu permohonan supaya dashboard tidak kosong saat pertama dibuka
INSERT INTO permohonan (kode_permohonan, uml_id, jenis_permohonan, nama_uttp, deskripsi, status)
VALUES ('SKVI-2026-0001', 1, 'baru', 'Timbangan Meja Kapasitas 30 kg', 'Pengajuan SKVI baru untuk lingkup penera timbangan meja.', 'diajukan');

INSERT INTO log_aktivitas (permohonan_id, user_id, status_sebelum, status_sesudah, keterangan)
VALUES (1, 1, NULL, 'diajukan', 'Permohonan SKVI baru diajukan oleh UML melalui sistem Stimulus.');

-- Notifikasi otomatis ke Koordinator (user id 2) karena permohonan #1 menunggu tindakannya
INSERT INTO notifikasi (user_id, permohonan_id, pesan)
VALUES (2, 1, 'Permohonan SKVI-2026-0001 menunggu tindakan Anda: Verifikasi Administrasi (Koordinator).');