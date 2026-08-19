-- =========================================================
-- MIGRATION: menambahkan fitur notifikasi
-- Jalankan file ini HANYA jika kamu sudah pernah meng-import
-- database.sql versi sebelumnya (yang belum ada tabel notifikasi).
-- Jika ini instalasi baru, TIDAK PERLU menjalankan file ini —
-- cukup import database.sql yang sudah termasuk tabel ini.
-- =========================================================

CREATE TABLE IF NOT EXISTS notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    permohonan_id INT NOT NULL,
    pesan VARCHAR(255) NOT NULL,
    dibaca TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permohonan_id) REFERENCES permohonan(id) ON DELETE CASCADE
) ENGINE=InnoDB;
