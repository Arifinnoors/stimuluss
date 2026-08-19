<?php
/**
 * AJAX login endpoint — returns JSON for the beranda popup modal.
 */
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';
require_once 'includes/auth.php';
require_db();

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed.']);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['ok' => false, 'error' => 'Email dan password wajib diisi.']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, nama, role, password FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(['ok' => false, 'error' => 'Email atau password salah.']);
    exit;
}

// Success — set session and return redirect URL
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['nama']    = $user['nama'];
$_SESSION['role']    = $user['role'];

echo json_encode([
    'ok'       => true,
    'redirect' => 'dashboard.php',
    'nama'     => $user['nama'],
]);
