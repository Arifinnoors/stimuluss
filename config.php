<?php
/**
 * Konfigurasi koneksi database.
 * Sesuaikan DB_HOST, DB_USER, DB_PASS sesuai environment hosting.
 * XAMPP default: 127.0.0.1 / root / (kosong)
 * InfinityFree: biasanya hostname panjang dari panel, user & pass dari phpMyAdmin.
 */

// ── Auto-detect environment ──
$serverName = $_SERVER['SERVER_NAME'] ?? '';
$isLocalhost = in_array($serverName, ['localhost', '127.0.0.1', '::1'], true)
             || strpos($serverName, 'localhost') === 0;

if ($isLocalhost) {
    // XAMPP local
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'stimulus_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // InfinityFree / production hosting — ISI DENGAN DATA DARI PANEL HOSTING
    define('DB_HOST', 'sql213.infinityfree.com');   // ← ganti dari panel
    define('DB_NAME', 'if0_42618782_stimulus');          // ← ganti dari panel
    define('DB_USER', 'if0_42618782');                   // ← ganti dari panel
    define('DB_PASS', '4wmPHis9X3vyl');          // ← ganti dari panel
}

$pdo = null;
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Jangan gunakan die() — biarkan halaman tetap bisa render CSS/HTML
    // Tampilkan error di page wrap supaya user tahu apa yang salah
    $dbError = htmlspecialchars($e->getMessage());
    $pdo = null;
}

// Nama aplikasi & base path (dipakai di layout)
define('APP_NAME', 'STIMULUS');
define('APP_FULL_NAME', 'Sistem Informasi Monitoring untuk Layanan UTTP dan Standar Ukuran');

/**
 * Notifikasi email: NONAKTIF secara default karena butuh SMTP asli
 * (mis. Gmail App Password) supaya benar-benar terkirim.
 * Cara aktifkan: ubah jadi true, lalu isi 4 baris SMTP_* di bawah.
 * Notifikasi DALAM SISTEM (badge + halaman notifikasi) tetap jalan
 * normal walau ini dibiarkan false.
 */
define('EMAIL_NOTIF_ENABLED', false); // Ubah ke true setelah SMTP di-setup
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'email-instansi@gmail.com');
define('SMTP_PASS', 'app-password-16-digit');
