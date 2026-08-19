<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_login();
require_db();

$id   = (int) ($_GET['id'] ?? 0);
$role = current_role();

$stmt = $pdo->prepare(
    'SELECT p.*, u.nama AS nama_uml, u.email AS email_uml, u.instansi
     FROM permohonan p JOIN users u ON u.id = p.uml_id
     WHERE p.id = ?'
);
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) { die('Permohonan tidak ditemukan.'); }
if ($role === 'uml' && (int) $p['uml_id'] !== current_user_id()) {
    http_response_code(403);
    die('Permohonan ini bukan milik akun kamu.');
}

$logStmt = $pdo->prepare(
    'SELECT l.*, u.nama AS nama_user, u.role AS role_user
     FROM log_aktivitas l JOIN users u ON u.id = l.user_id
     WHERE l.permohonan_id = ? ORDER BY l.created_at ASC'
);
$logStmt->execute([$id]);
$riwayat = $logStmt->fetchAll();

$pemilikStatus = status_pemilik($p['status']);
$bisaBertindak = ($pemilikStatus === $role);

/* Workflow steps definition */
$workflowSteps = [
    'diajukan'                => ['label' => 'Diajukan',             'icon' => '📝'],
    'verifikasi_administrasi' => ['label' => 'Verifikasi Admin',     'icon' => '🔍'],
    'verifikasi_berkas'       => ['label' => 'Verifikasi Berkas',    'icon' => '📋'],
    'penilaian'               => ['label' => 'Penilaian',            'icon' => '🎯'],
    'review_koordinator'      => ['label' => 'Review Koordinator',   'icon' => '👁️'],
    'review_ketua_tim'        => ['label' => 'Review Ketua Tim',     'icon' => '✅'],
    'menunggu_ttd_direktur'   => ['label' => 'TTD Direktur',         'icon' => '✍️'],
    'selesai'                 => ['label' => 'Selesai',              'icon' => '🎉'],
];
$statusOrder = array_keys($workflowSteps);
$currentIndex = array_search($p['status'], $statusOrder);
if ($currentIndex === false) $currentIndex = 0;

$pageTitle = $p['kode_permohonan'];
require 'includes/header.php';
?>

<a href="dashboard.php" class="breadcrumb-link">← Kembali ke Dashboard</a>

<div class="page-header-row">
    <div class="page-header">
        <h1><?= htmlspecialchars($p['kode_permohonan']) ?></h1>
        <p><?= htmlspecialchars($p['nama_uttp']) ?> &middot; <?= jenis_label($p['jenis_permohonan']) ?></p>
    </div>
    <span class="badge <?= status_badge_class($p['status']) ?> badge-lg">
        <?= status_label($p['status']) ?>
    </span>
</div>

<?php if (!empty($_GET['sukses'])): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✅</span>
        <span>Aksi berhasil disimpan. Status permohonan telah diperbarui.</span>
    </div>
<?php endif; ?>

<!-- Workflow Progress Tracker -->
<div class="workflow-progress reveal">
    <?php foreach ($workflowSteps as $stepKey => $step): ?>
        <?php
        $stepIndex = array_search($stepKey, $statusOrder);
        $stepClass = 'future';
        if ($p['status'] === $stepKey) $stepClass = 'active';
        elseif ($stepIndex < $currentIndex) $stepClass = 'completed';
        ?>
        <div class="workflow-step <?= $stepClass ?>">
            <div class="step-circle">
                <?php if ($stepClass === 'completed'): ?>✓<?php else: ?><?= $stepIndex + 1 ?><?php endif; ?>
            </div>
            <div class="step-label"><?= $step['label'] ?></div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Detail Card -->
<div class="card reveal">
    <div class="card-title">Detail Permohonan</div>
    <dl class="detail-grid">
        <dt>Diajukan oleh</dt>
        <dd><?= htmlspecialchars($p['nama_uml']) ?> (<?= htmlspecialchars($p['email_uml']) ?>)<?= $p['instansi'] ? ' — ' . htmlspecialchars($p['instansi']) : '' ?></dd>

        <dt>Jenis Permohonan</dt>
        <dd><?= jenis_label($p['jenis_permohonan']) ?></dd>

        <dt>Nama / Jenis UTTP</dt>
        <dd><?= htmlspecialchars($p['nama_uttp']) ?></dd>

        <dt>Deskripsi</dt>
        <dd><?= nl2br(htmlspecialchars($p['deskripsi'] ?: '-')) ?></dd>

        <dt>Berkas Permohonan</dt>
        <dd><?= $p['file_permohonan']
            ? '<a class="file-chip" href="' . htmlspecialchars($p['file_permohonan']) . '" target="_blank">📎 Lihat Berkas</a>'
            : '<span class="text-muted">Tidak ada berkas dilampirkan</span>' ?></dd>

        <dt>Tanggal Diajukan</dt>
        <dd><?= format_tanggal($p['created_at']) ?></dd>

        <?php if ($p['catatan_koordinator']): ?>
        <dt>Catatan Koordinator</dt>
        <dd><?= nl2br(htmlspecialchars($p['catatan_koordinator'])) ?></dd>
        <?php endif; ?>

        <?php if ($p['catatan_verifikator']): ?>
        <dt>Catatan Verifikator</dt>
        <dd><?= nl2br(htmlspecialchars($p['catatan_verifikator'])) ?></dd>
        <?php endif; ?>

        <?php if ($p['jadwal_zoom']): ?>
        <dt>Jadwal Penilaian (Zoom)</dt>
        <dd><?= format_tanggal($p['jadwal_zoom']) ?> — Ditmet, UML, BSML, BKML</dd>
        <?php endif; ?>

        <?php if ($p['hasil_penilaian']): ?>
        <dt>Hasil Penilaian</dt>
        <dd><?= nl2br(htmlspecialchars($p['hasil_penilaian'])) ?></dd>
        <?php endif; ?>

        <?php if ($p['file_draft_skvi']): ?>
        <dt>Draft SKVI</dt>
        <dd><a class="file-chip" href="<?= htmlspecialchars($p['file_draft_skvi']) ?>" target="_blank">📎 Lihat Draft SKVI</a></dd>
        <?php endif; ?>

        <?php if ($p['status'] === 'selesai'): ?>
        <dt>Nomor SKVI</dt>
        <dd><strong style="color:var(--green-600);font-size:16px;"><?= htmlspecialchars($p['nomor_skvi']) ?></strong></dd>
        <?php endif; ?>
    </dl>
</div>

<?php if ($p['status'] === 'selesai'): ?>
    <div class="card reveal" style="border-left:4px solid var(--green-600);">
        <div class="card-title" style="border-bottom-color:var(--green-100);">
            🎉 SKVI Terbit
        </div>
        <p>SKVI sudah ditandatangani secara digital oleh Direktur dan siap diunduh.</p>
        <a href="unduh.php?id=<?= $p['id'] ?>" class="btn btn-success btn-pill" style="margin-top:8px;">⬇ Unduh SKVI (<?= htmlspecialchars($p['nomor_skvi']) ?>)</a>
    </div>

<?php elseif ($bisaBertindak): ?>
    <div class="card reveal" style="border-left:4px solid var(--teal-500);">
        <div class="card-title">Tindakan — <?= role_label($role) ?></div>

        <?php if ($p['status'] === 'diajukan' || $p['status'] === 'verifikasi_administrasi'): ?>
            <p class="text-muted">Periksa kelengkapan administrasi permohonan sebelum diteruskan ke Verifikator.</p>
            <form method="post" action="proses_aksi.php" style="margin-bottom:14px;">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="lanjut_verifikasi_berkas">
                <div class="form-group">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="catatan" placeholder="Catatan administrasi, jika ada..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-pill">✅ Verifikasi &amp; Teruskan ke Verifikator</button>
            </form>
            <hr class="section-divider">
            <form method="post" action="proses_aksi.php">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="kembalikan_uml_dari_koordinator">
                <div class="form-group">
                    <label class="form-label">Alasan pengembalian ke UML</label>
                    <textarea name="catatan" required placeholder="Contoh: mohon lampirkan surat permohonan resmi..."></textarea>
                </div>
                <button type="submit" class="btn btn-secondary">↩️ Kembalikan ke UML</button>
            </form>

        <?php elseif ($p['status'] === 'dikembalikan_uml'): ?>
            <p class="text-muted">Permohonan dikembalikan untuk dilengkapi. Silakan unggah berkas tambahan lalu kirim ulang.</p>
            <form method="post" action="proses_aksi.php" enctype="multipart/form-data">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="kirim_ulang_uml">
                <div class="form-group">
                    <label class="form-label">Berkas Tambahan (opsional)</label>
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-icon">📄</div>
                        <div class="upload-text">Klik untuk memilih berkas tambahan</div>
                        <div class="upload-hint">PDF, JPG, PNG, DOC</div>
                        <input type="file" name="file_permohonan" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-pill">📤 Lengkapi &amp; Kirim Ulang</button>
            </form>

        <?php elseif ($p['status'] === 'verifikasi_berkas'): ?>
            <p class="text-muted">Periksa berkas permohonan (otomatis &amp; manual), lalu jadwalkan penilaian melalui Zoom Meeting.</p>
            <form method="post" action="proses_aksi.php" style="margin-bottom:14px;">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="verifikasi_berkas_selesai">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">📅 Jadwal Zoom Meeting</label>
                        <input type="datetime-local" name="jadwal_zoom" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan Verifikasi (opsional)</label>
                        <input type="text" name="catatan" placeholder="Catatan singkat...">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-pill">✅ Berkas Lengkap &amp; Jadwalkan Penilaian</button>
            </form>
            <hr class="section-divider">
            <form method="post" action="proses_aksi.php">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="kembalikan_uml_dari_verifikator">
                <div class="form-group">
                    <label class="form-label">Alasan pengembalian ke UML</label>
                    <textarea name="catatan" required placeholder="Berkas yang kurang..."></textarea>
                </div>
                <button type="submit" class="btn btn-secondary">↩️ Minta Kelengkapan ke UML</button>
            </form>

        <?php elseif ($p['status'] === 'penilaian'): ?>
            <p class="text-muted">Setelah penilaian melalui Zoom Meeting (Ditmet, UML, BSML, BKML) selesai, catat hasilnya dan kirim draft SKVI ke Koordinator.</p>
            <form method="post" action="proses_aksi.php" enctype="multipart/form-data">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="kirim_draft_ke_koordinator">
                <div class="form-group">
                    <label class="form-label">Hasil Penilaian</label>
                    <textarea name="hasil_penilaian" required placeholder="Ringkasan hasil penilaian kemampuan verifikasi internal..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Unggah Draft SKVI (opsional)</label>
                    <div class="upload-zone" id="uploadZone2">
                        <div class="upload-icon">📄</div>
                        <div class="upload-text">Klik untuk memilih draft SKVI</div>
                        <div class="upload-hint">PDF, DOC</div>
                        <input type="file" name="file_draft_skvi" accept=".pdf,.doc,.docx">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-pill">📤 Catat Hasil &amp; Kirim Draft ke Koordinator</button>
            </form>

        <?php elseif ($p['status'] === 'review_koordinator'): ?>
            <p class="text-muted">Periksa form pemeriksaan dan draft SKVI dari Verifikator sebelum diteruskan ke Ketua Tim.</p>
            <form method="post" action="proses_aksi.php" style="margin-bottom:14px;">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="teruskan_ke_ketua_tim">
                <div class="form-group">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="catatan" placeholder="Catatan untuk Ketua Tim..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-pill">✅ Verifikasi &amp; Teruskan ke Ketua Tim</button>
            </form>
            <hr class="section-divider">
            <form method="post" action="proses_aksi.php">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="kembalikan_uml_dari_review">
                <div class="form-group">
                    <label class="form-label">Alasan pengembalian ke UML</label>
                    <textarea name="catatan" required placeholder="Kekurangan pada draft..."></textarea>
                </div>
                <button type="submit" class="btn btn-secondary">↩️ Kembalikan ke UML (Revisi)</button>
            </form>

        <?php elseif ($p['status'] === 'review_ketua_tim'): ?>
            <p class="text-muted">Verifikasi (paraf) draft SKVI sebelum diteruskan ke Direktur untuk ditandatangani.</p>
            <form method="post" action="proses_aksi.php">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="teruskan_ke_direktur">
                <button type="submit" class="btn btn-primary btn-pill">✅ Verifikasi (Paraf) &amp; Teruskan ke Direktur</button>
            </form>

        <?php elseif ($p['status'] === 'menunggu_ttd_direktur'): ?>
            <p class="text-muted">Tandatangani SKVI secara digital untuk menerbitkan sertifikat.</p>
            <form method="post" action="proses_aksi.php" enctype="multipart/form-data">
                <input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="aksi" value="tanda_tangani">
                <div class="form-group">
                    <label class="form-label">Nomor SKVI</label>
                    <input type="text" name="nomor_skvi" required placeholder="Contoh: 123/SKVI/DITMET/2026">
                </div>
                <div class="form-group">
                    <label class="form-label">Unggah File SKVI Final (opsional)</label>
                    <div class="upload-zone" id="uploadZone3">
                        <div class="upload-icon">📄</div>
                        <div class="upload-text">Klik untuk memilih file SKVI</div>
                        <div class="upload-hint">PDF — Jika tidak diunggah, sistem akan membuat placeholder otomatis.</div>
                        <input type="file" name="file_skvi_final" accept=".pdf">
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-pill">✍️ Tanda Tangani SKVI (Digital Signature)</button>
            </form>
        <?php endif; ?>
    </div>

<?php else: ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        <span>Permohonan ini sedang diproses oleh <strong><?= htmlspecialchars(role_label($pemilikStatus)) ?></strong>.
        Kamu akan bisa melihat perkembangannya di riwayat aktivitas di bawah.</span>
    </div>
<?php endif; ?>

<!-- Timeline -->
<div class="card reveal">
    <div class="card-title">Riwayat Aktivitas</div>
    <ul class="timeline">
        <?php if (!$riwayat): ?>
            <li style="text-align:center;color:var(--ink-400);padding:24px;border-left:none;">Belum ada riwayat aktivitas.</li>
        <?php endif; ?>
        <?php foreach ($riwayat as $r): ?>
            <li>
                <div class="tl-title">
                    <?= status_label($r['status_sesudah']) ?>
                    <span class="role-badge"><?= role_label($r['role_user']) ?></span>
                </div>
                <div class="tl-meta"><?= htmlspecialchars($r['nama_user']) ?> &middot; <?= format_tanggal($r['created_at']) ?></div>
                <div class="tl-desc"><?= htmlspecialchars($r['keterangan']) ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php require 'includes/footer.php'; ?>
