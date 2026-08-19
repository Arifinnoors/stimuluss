<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load translations (depends on session)
require_once __DIR__ . '/translations.php';

// Handle language switch
if (isset($_GET['lang']) && in_array($_GET['lang'], ['id', 'en'])) {
    set_language($_GET['lang']);
}

// Panggil di halaman yang wajib login
function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Panggil di halaman yang butuh koneksi database
// Akan tampilkan pesan error jika koneksi gagal
function require_db(): void
{
    global $pdo;
    if ($pdo === null) {
        http_response_code(503);
        die('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Kesalahan Sistem</title></head><body style="font-family:sans-serif;padding:40px;text-align:center;">
                <h2>Koneksi Database Gagal</h2>
                <p>Sistem tidak dapat terhubung ke database. Silakan coba lagi nanti.</p>
                <p style="color:#999;font-size:13px;">Hubungi administrator jika masalah berlanjut.</p>
                <a href="index.php">&larr; Kembali ke Beranda</a>
             </body></html>');
    }
}

// Panggil di halaman yang hanya boleh diakses role tertentu
// Contoh: require_role(['koordinator', 'direktur']);
function require_role(array $rolesAllowed): void
{
    require_login();
    if (!in_array($_SESSION['role'], $rolesAllowed, true)) {
        http_response_code(403);
        die('<div style="font-family:sans-serif;padding:40px;text-align:center;">
                <h2>Akses ditolak</h2>
                <p>Halaman ini bukan untuk role akun kamu.</p>
                <a href="dashboard.php">&larr; Kembali ke dashboard</a>
             </div>');
    }
}

function current_user_id(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}

function current_role(): string
{
    return $_SESSION['role'] ?? '';
}
