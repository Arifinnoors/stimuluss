<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_login();
require_db();

$userId = current_user_id();

// Ambil dulu (supaya status "belum dibaca" masih akurat saat dirender),
// baru tandai semua dibaca setelah itu.
$daftarNotifikasi = ambil_notifikasi($pdo, $userId);
tandai_semua_dibaca($pdo, $userId);

$pageTitle = 'Notifikasi';
require 'includes/header.php';
?>

<a href="dashboard.php" class="breadcrumb-link">← Kembali ke Dashboard</a>

<div class="page-header-row">
    <div class="page-header">
        <h1>Notifikasi</h1>
        <p>Pemberitahuan otomatis setiap kali ada permohonan yang menunggu tindakan kamu.</p>
    </div>
    <?php if ($daftarNotifikasi): ?>
        <span class="badge badge-info badge-lg">📬 <?= count($daftarNotifikasi) ?> notifikasi</span>
    <?php endif; ?>
</div>

<div class="card reveal" style="padding:0;">
    <ul class="notif-list">
        <?php if (!$daftarNotifikasi): ?>
            <li class="notif-empty">
                <div class="notif-empty-icon">🔔</div>
                <div class="notif-empty-text">Belum ada notifikasi</div>
                <div class="notif-empty-hint">Kamu akan menerima notifikasi ketika ada permohonan baru atau ada tindakan yang perlu dilakukan.</div>
                <a href="dashboard.php" class="btn btn-secondary btn-sm" style="margin-top:16px;">← Kembali ke Dashboard</a>
            </li>
        <?php endif; ?>
        <?php foreach ($daftarNotifikasi as $n): ?>
            <a href="permohonan_detail.php?id=<?= $n['permohonan_id'] ?>"
               class="notif-item <?= $n['dibaca'] ? '' : 'unread' ?>">
                <div class="notif-icon">
                    <?php if ($n['dibaca']): ?>📭<?php else: ?>📬<?php endif; ?>
                </div>
                <div class="notif-body">
                    <div class="notif-msg"><?= htmlspecialchars($n['pesan']) ?></div>
                    <div class="notif-time"><?= htmlspecialchars($n['kode_permohonan']) ?> &middot; <?= format_tanggal($n['created_at']) ?></div>
                </div>
            </a>
        <?php endforeach; ?>
    </ul>
</div>

<?php require 'includes/footer.php'; ?>
