<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();
require_db();

$id   = (int) ($_GET['id'] ?? 0);
$role = current_role();

$stmt = $pdo->prepare('SELECT * FROM permohonan WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p || $p['status'] !== 'selesai' || !$p['file_skvi_final']) {
    die('Berkas SKVI belum tersedia.');
}
if ($role === 'uml' && (int) $p['uml_id'] !== current_user_id()) {
    http_response_code(403);
    die('Permohonan ini bukan milik akun kamu.');
}

$path = __DIR__ . '/' . $p['file_skvi_final'];
if (!file_exists($path)) {
    die('File tidak ditemukan di server.');
}

$namaUnduh = 'SKVI_' . $p['kode_permohonan'] . '.' . pathinfo($path, PATHINFO_EXTENSION);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $namaUnduh . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
