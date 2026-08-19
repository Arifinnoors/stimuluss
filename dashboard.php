<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_login();
require_db();

$role   = current_role();
$userId = current_user_id();

if ($role === 'uml') {
    $stmt = $pdo->prepare('SELECT * FROM permohonan WHERE uml_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    $daftar = $stmt->fetchAll();

    $total   = count($daftar);
    $selesai = count(array_filter($daftar, fn($p) => $p['status'] === 'selesai'));
    $proses  = $total - $selesai;
} else {
    $stmt = $pdo->query(
        'SELECT p.*, u.nama AS nama_uml, u.instansi
         FROM permohonan p JOIN users u ON u.id = p.uml_id
         ORDER BY p.created_at DESC'
    );
    $semua = $stmt->fetchAll();

    $perluTindakan = array_values(array_filter($semua, fn($p) => status_pemilik($p['status']) === $role));
    $selesaiSemua  = count(array_filter($semua, fn($p) => $p['status'] === 'selesai'));

    $daftar = $semua;
}

$pageTitle = t('db.page_title');
require 'includes/header.php';
?>

<div class="page-header-row">
    <div class="page-header">
        <h1><span data-t="db.halo"><?= t('db.halo') ?></span>, <?= htmlspecialchars($_SESSION['nama']) ?> <span class="wave-emoji">👋</span></h1>
        <p data-t="<?= $role === 'uml' ? 'db.subtitle_uml' : 'db.subtitle_nonuml' ?>"><?= $role === 'uml'
            ? t('db.subtitle_uml')
            : t('db.subtitle_nonuml') ?></p>
    </div>
    <?php if ($role === 'uml'): ?>
        <a href="permohonan_baru.php" class="btn btn-primary btn-pill" data-t="db.btn_ajukan"><?= t('db.btn_ajukan') ?></a>
    <?php endif; ?>
</div>

<?php if ($role === 'uml'): ?>
    <div class="stat-grid">
        <div class="stat-card accent-teal" data-icon="📊">
            <div class="stat-icon">📊</div>
            <div class="stat-value" data-count="<?= $total ?>"><?= $total ?></div>
            <div class="stat-label" data-t="db.stat_total"><?= t('db.stat_total') ?></div>
        </div>
        <div class="stat-card accent-amber" data-icon="⏳">
            <div class="stat-icon">⏳</div>
            <div class="stat-value" data-count="<?= $proses ?>"><?= $proses ?></div>
            <div class="stat-label" data-t="db.stat_proses"><?= t('db.stat_proses') ?></div>
        </div>
        <div class="stat-card accent-green" data-icon="✅">
            <div class="stat-icon">✅</div>
            <div class="stat-value" data-count="<?= $selesai ?>"><?= $selesai ?></div>
            <div class="stat-label" data-t="db.stat_terbit"><?= t('db.stat_terbit') ?></div>
        </div>
    </div>

    <div class="card reveal">
        <div class="card-title" data-t="db.card_saya"><?= t('db.card_saya') ?></div>
        <div class="table-wrap">
        <table class="data-table">
            <thead><tr>
                <th data-t="db.th_kode"><?= t('db.th_kode') ?></th><th data-t="db.th_uttp"><?= t('db.th_uttp') ?></th><th data-t="db.th_jenis"><?= t('db.th_jenis') ?></th><th data-t="db.th_diajukan"><?= t('db.th_diajukan') ?></th><th data-t="db.th_status"><?= t('db.th_status') ?></th><th></th>
            </tr></thead>
            <tbody>
            <?php if (!$daftar): ?>
                <tr class="empty-row"><td colspan="6" data-t="db.empty_saya"><?= t('db.empty_saya') ?></td></tr>
            <?php endif; ?>
            <?php foreach ($daftar as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['kode_permohonan']) ?></strong></td>
                    <td><?= htmlspecialchars($p['nama_uttp']) ?></td>
                    <td><span class="jenis-tag"><?= jenis_label($p['jenis_permohonan']) ?></span></td>
                    <td><?= format_tanggal($p['created_at']) ?></td>
                    <td>
                        <?php
                        $dotClass = 'info';
                        if ($p['status'] === 'selesai') $dotClass = 'active';
                        elseif ($p['status'] === 'dikembalikan_uml') $dotClass = 'warning';
                        elseif (status_pemilik($p['status']) === $role) $dotClass = 'pending';
                        ?>
                        <span class="status-dot <?= $dotClass ?>"></span>
                        <span class="badge <?= status_badge_class($p['status']) ?>"><?= status_label($p['status']) ?></span>
                    </td>
                    <td><a href="permohonan_detail.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm"><?= t('db.btn_lihat') ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

<?php else: ?>
    <div class="stat-grid">
        <div class="stat-card accent-red" data-icon="📋">
            <div class="stat-icon">📋</div>
            <div class="stat-value" data-count="<?= count($perluTindakan) ?>"><?= count($perluTindakan) ?></div>
            <div class="stat-label" data-t="db.stat_perlu"><?= t('db.stat_perlu') ?></div>
        </div>
        <div class="stat-card accent-teal" data-icon="📊">
            <div class="stat-icon">📊</div>
            <div class="stat-value" data-count="<?= count($semua) ?>"><?= count($semua) ?></div>
            <div class="stat-label" data-t="db.stat_total_sys"><?= t('db.stat_total_sys') ?></div>
        </div>
        <div class="stat-card accent-green" data-icon="✅">
            <div class="stat-icon">✅</div>
            <div class="stat-value" data-count="<?= $selesaiSemua ?>"><?= $selesaiSemua ?></div>
            <div class="stat-label" data-t="db.stat_terbit"><?= t('db.stat_terbit') ?></div>
        </div>
    </div>

    <div class="card reveal">
        <div class="card-title" data-t="db.card_perlu">
            <?= t('db.card_perlu') ?>
            <?php if (count($perluTindakan) > 0): ?>
                <span class="badge badge-danger" style="font-size:11px;padding:3px 8px;margin-left:8px;"><?= count($perluTindakan) ?> <span data-t="db.baru"><?= t('db.baru') ?></span></span>
            <?php endif; ?>
        </div>
        <div class="table-wrap">
        <table class="data-table">
            <thead><tr>
                <th data-t="db.th_kode"><?= t('db.th_kode') ?></th><th data-t="db.th_uml"><?= t('db.th_uml') ?></th><th data-t="db.th_uttp"><?= t('db.th_uttp') ?></th><th data-t="db.th_jenis"><?= t('db.th_jenis') ?></th><th data-t="db.th_status"><?= t('db.th_status') ?></th><th></th>
            </tr></thead>
            <tbody>
            <?php if (!$perluTindakan): ?>
                <tr class="empty-row"><td colspan="6" data-t="db.empty_perlu"><?= t('db.empty_perlu') ?></td></tr>
            <?php endif; ?>
            <?php foreach ($perluTindakan as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['kode_permohonan']) ?></strong></td>
                    <td><?= htmlspecialchars($p['nama_uml']) ?><br><small class="text-muted"><?= htmlspecialchars($p['instansi'] ?? '') ?></small></td>
                    <td><?= htmlspecialchars($p['nama_uttp']) ?></td>
                    <td><span class="jenis-tag"><?= jenis_label($p['jenis_permohonan']) ?></span></td>
                    <td>
                        <span class="status-dot pending"></span>
                        <span class="badge <?= status_badge_class($p['status']) ?>"><?= status_label($p['status']) ?></span>
                    </td>
                    <td><a href="permohonan_detail.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm"><?= t('db.btn_proses') ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <div class="card reveal">
        <div class="card-title" data-t="db.cardSemua"><?= t('db.cardSemua') ?></div>
        <div class="table-wrap">
        <table class="data-table">
            <thead><tr>
                <th data-t="db.th_kode"><?= t('db.th_kode') ?></th><th data-t="db.th_uml"><?= t('db.th_uml') ?></th><th data-t="db.th_uttp"><?= t('db.th_uttp') ?></th><th data-t="db.th_diajukan"><?= t('db.th_diajukan') ?></th><th data-t="db.th_status"><?= t('db.th_status') ?></th><th></th>
            </tr></thead>
            <tbody>
            <?php if (!$daftar): ?>
                <tr class="empty-row"><td colspan="6" data-t="db.empty_semua"><?= t('db.empty_semua') ?></td></tr>
            <?php endif; ?>
            <?php foreach ($daftar as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['kode_permohonan']) ?></strong></td>
                    <td><?= htmlspecialchars($p['nama_uml']) ?></td>
                    <td><?= htmlspecialchars($p['nama_uttp']) ?></td>
                    <td><?= format_tanggal($p['created_at']) ?></td>
                    <td>
                        <?php
                        $dotClass = 'info';
                        if ($p['status'] === 'selesai') $dotClass = 'active';
                        elseif ($p['status'] === 'dikembalikan_uml') $dotClass = 'warning';
                        ?>
                        <span class="status-dot <?= $dotClass ?>"></span>
                        <span class="badge <?= status_badge_class($p['status']) ?>"><?= status_label($p['status']) ?></span>
                    </td>
                    <td><a href="permohonan_detail.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm"><?= t('db.btn_lihat') ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
