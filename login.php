<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_db();

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['role']    = $user['role'];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Email atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=6">
<style>
.login-field { animation: fieldSlideIn 0.45s cubic-bezier(0.4, 0, 0.2, 1) both; }
.login-field:nth-child(1) { animation-delay: 0.25s; }
.login-field:nth-child(2) { animation-delay: 0.35s; }
@keyframes fieldSlideIn {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.login-submit {
    animation: fieldSlideIn 0.45s cubic-bezier(0.4, 0, 0.2, 1) 0.45s both;
}
.login-error {
    animation: errorShake 0.5s ease both;
}
@keyframes errorShake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-6px); }
    40% { transform: translateX(6px); }
    60% { transform: translateX(-4px); }
    80% { transform: translateX(4px); }
}
.login-footer-text {
    text-align: center; margin-top: 20px; font-size: 12px; color: #A8A29E;
    animation: fieldSlideIn 0.4s ease 0.55s both;
}
/* Floating particles */
.particles { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
.particle {
    position: absolute; border-radius: 50%; opacity: 0.12;
    animation: particleFloat linear infinite;
}
@keyframes particleFloat {
    0%   { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10%  { opacity: 0.12; }
    90%  { opacity: 0.12; }
    100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
}
</style>
</head>
<body>
<div class="particles" aria-hidden="true">
    <div class="particle" style="width:6px;height:6px;background:#D4A017;left:10%;animation-duration:14s;animation-delay:0s;"></div>
    <div class="particle" style="width:4px;height:4px;background:#2AA198;left:25%;animation-duration:18s;animation-delay:2s;"></div>
    <div class="particle" style="width:8px;height:8px;background:#D4A017;left:45%;animation-duration:16s;animation-delay:4s;"></div>
    <div class="particle" style="width:5px;height:5px;background:#fff;left:65%;animation-duration:20s;animation-delay:1s;"></div>
    <div class="particle" style="width:3px;height:3px;background:#2AA198;left:80%;animation-duration:15s;animation-delay:3s;"></div>
    <div class="particle" style="width:7px;height:7px;background:#D4A017;left:90%;animation-duration:17s;animation-delay:5s;"></div>
</div>

<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">S</div>
        <h1>STIMULUS</h1>
        <p class="login-sub">Monitoring Layanan UTTP &amp; Standar Ukuran</p>

        <?php if ($error): ?>
            <div class="alert alert-error login-error">
                <span class="alert-icon">⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="form-group login-field">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="nama@instansi.go.id">
            </div>
            <div class="form-group login-field">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-block login-submit" style="border-radius:12px;padding:12px;">Masuk →</button>
        </form>

        <details class="demo-accounts">
            <summary>🔑 Untuk uji coba tiap role</summary>
            <table>
                <tr><td>UML</td><td>uml@demo.com</td></tr>
                <tr><td>Koordinator</td><td>koordinator@demo.com</td></tr>
                <tr><td>Verifikator</td><td>verifikator@demo.com</td></tr>
                <tr><td>Ketua Tim</td><td>ketuatim@demo.com</td></tr>
                <tr><td>Direktur</td><td>direktur@demo.com</td></tr>
            </table>
            <p style="margin:10px 0 0;color:#A8A29E;font-size:12px;">Password untuk semua akun demo: <strong>password123</strong></p>
        </details>

        <div class="login-footer-text">
            &copy; <?= date('Y') ?> Kementerian Perdagangan RI
        </div>
    </div>
</div>
</body>
</html>
