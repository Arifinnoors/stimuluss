<?php
// Halaman yang meng-include file ini WAJIB sudah:
// 1. require_once config.php & auth.php
// 2. mendefinisikan $pageTitle (opsional)
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="<?= get_language() ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=10">
<style>
/* Critical fallback — jika CSS external gagal load, layout tetap jalan */
.topbar{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(250,248,245,0.92);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border-bottom:1px solid #E8E4DF}
.topbar-inner{max-width:1140px;margin:0 auto;padding:14px 24px;display:flex;align-items:center;justify-content:space-between}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none}
.brand:hover{text-decoration:none}
.brand-logo-pt{width:34px;height:34px;border-radius:10px;object-fit:cover;flex-shrink:0;box-shadow:0 2px 8px rgba(15,26,46,0.10)}
.brand-logo-kemper{height:30px;width:auto;flex-shrink:0;opacity:.9}
.brand-text{display:flex;flex-direction:column;line-height:1.2}
.brand-text strong{color:#0F1A2E;font-size:17px;font-weight:800;letter-spacing:0.3px}
.brand-text small{display:none}
.topbar-user{display:flex;align-items:center;gap:14px}
.notif-bell{position:relative;display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:12px;background:#E8EDF5;border:1px solid #E2E0DC;font-size:17px;text-decoration:none;flex-shrink:0;transition:all .25s cubic-bezier(.4,0,.2,1)}
.notif-bell:hover{background:#E0F5F3;border-color:#2AA198;transform:scale(1.08);text-decoration:none}
.notif-badge{position:absolute;top:-5px;right:-5px;background:linear-gradient(135deg,#DC2626,#F87171);color:#fff;font-size:10px;font-weight:800;line-height:1;padding:3px 5px;border-radius:999px;border:2px solid #fff;min-width:17px;text-align:center;box-shadow:0 2px 8px rgba(220,38,38,0.3)}
.user-info{display:flex;flex-direction:column;text-align:right;line-height:1.2}
.user-name{color:#0F1A2E;font-weight:700;font-size:13.5px}
.user-role{color:#A8A29E;font-size:11px;font-weight:500}
.avatar-circle{width:36px;height:36px;border-radius:12px;background:linear-gradient(135deg,#1A8A7D,#2AA198);color:#fff;font-weight:700;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(42,161,152,0.2);transition:transform .2s cubic-bezier(.34,1.56,.64,1)}
.avatar-circle:hover{transform:scale(1.08)}
.btn-nav-logout{color:#44403C;text-decoration:none;font-size:13.5px;font-weight:500;padding:6px 16px;border-radius:8px;transition:color .2s ease,background .3s cubic-bezier(.4,0,.2,1)}
.btn-nav-logout:hover{color:#0F1A2E;background:#E8EDF5;text-decoration:none}
.lang-dropdown{position:relative;flex-shrink:0}
.lang-trigger{display:inline-flex;align-items:center;gap:7px;padding:7px 12px;border-radius:10px;border:1px solid #E2E0DC;background:#fff;cursor:pointer;font-size:13px;font-weight:600;color:#0F1A2E;transition:all .25s cubic-bezier(.4,0,.2,1);white-space:nowrap;font-family:'Inter',sans-serif}
.lang-trigger:hover{border-color:#2AA198;background:#F0FDFA}
.lang-trigger .lang-flag{line-height:1;display:inline-flex;align-items:center}
.lang-trigger .lang-flag img{border-radius:2px;object-fit:cover}
.lang-trigger .lang-chevron{width:14px;height:14px;transition:transform .3s cubic-bezier(.4,0,.2,1);flex-shrink:0}
.lang-dropdown.open .lang-chevron{transform:rotate(180deg)}
.lang-menu{position:absolute;top:calc(100% + 6px);right:0;min-width:200px;background:#fff;border:1px solid #E8E4DF;border-radius:14px;box-shadow:0 12px 40px rgba(15,26,46,0.12),0 2px 8px rgba(15,26,46,0.06);padding:6px;z-index:200;opacity:0;visibility:hidden;transform:translateY(-8px) scale(0.96);transition:all .25s cubic-bezier(.4,0,.2,1);transform-origin:top right}
.lang-dropdown.open .lang-menu{opacity:1;visibility:visible;transform:translateY(0) scale(1)}
.lang-option{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;text-decoration:none;color:#0F1A2E;font-size:13.5px;font-weight:500;transition:all .2s ease;cursor:pointer}
.lang-option:hover{background:#F5F3F0}
.lang-option.active{background:#F0FDFA;color:#2AA198;font-weight:700}
.lang-option .lang-flag{line-height:1;display:inline-flex;align-items:center}
.lang-option .lang-flag img{border-radius:2px;object-fit:cover}
.lang-option .lang-check{margin-left:auto;color:#2AA198;font-weight:700;font-size:14px}
.page-wrap{max-width:1140px;margin:0 auto;padding:92px 24px 60px}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,26,46,0.4);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);z-index:1000;align-items:center;justify-content:center;padding:24px;opacity:0;transition:opacity .3s ease}
.modal-overlay.modal-visible{display:flex;opacity:1}
.modal-card{background:#fff;border-radius:20px;box-shadow:0 32px 100px rgba(0,0,0,0.2);padding:40px 36px;max-width:380px;width:100%;text-align:center;transform:scale(0.85) translateY(20px);transition:transform .4s cubic-bezier(.4,0,.2,1)}
.modal-overlay.modal-visible .modal-card{transform:scale(1) translateY(0)}
.modal-icon{font-size:48px;margin-bottom:16px;display:inline-block}
.modal-title{font-size:18px;font-weight:700;color:#1C1917;margin:0 0 8px}
.modal-text{font-size:14px;color:#44403C;margin:0 0 28px;line-height:1.6}
.modal-actions{display:flex;gap:12px;justify-content:center}
.modal-actions .btn{flex:1;padding:11px 18px;border-radius:12px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 22px;border-radius:10px;font-size:13.8px;font-weight:600;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .25s cubic-bezier(.4,0,.2,1);position:relative;overflow:hidden}
.btn-secondary{background:#fff;color:#0F1A2E;border-color:#E2E0DC}
.btn-danger{background:linear-gradient(135deg,#DC2626,#F87171);color:#fff}

/* ── Mobile critical fallback ── */
@media(max-width:640px){
.topbar-inner{padding:10px 14px;gap:8px}
.brand-logo-kemper{height:20px}
.brand-logo-pt{width:26px;height:26px}
.brand-text strong{font-size:14px}
.topbar-user{gap:10px}
.user-info{display:none}
.avatar-circle{width:32px;height:32px;font-size:13px;border-radius:10px}
.page-wrap{padding:72px 14px 40px}
.modal-overlay{padding:14px}
.modal-card{padding:28px 20px;border-radius:16px}
.modal-actions{flex-direction:column;gap:10px}
.lang-trigger{padding:6px 10px;font-size:12px}
.lang-trigger .lang-flag img{width:15px;height:10px}
.lang-menu{min-width:170px}
}
</style>
</head>
<body>

<script>window._t=<?= t_json() ?>;</script>

<?php if (!empty($dbError)): ?>
<div style="max-width:600px;margin:40px auto;padding:24px;background:#FEE2E2;border:1px solid #FCA5A5;border-radius:12px;font-family:'Inter',sans-serif;">
    <h2 style="color:#991B1B;margin:0 0 8px;"><?= t('db_error.title') ?></h2>
    <p style="color:#44403C;font-size:14px;margin:0 0 12px;"><?= t('db_error.desc') ?></p>
    <p style="color:#991B1B;font-size:12px;background:#FEE2E2;padding:10px;border-radius:8px;word-break:break-all;"><?= $dbError ?></p>
    <p style="color:#44403C;font-size:13px;margin:12px 0 0;"><?= t('db_error.hint') ?></p>
</div>
<?php endif; ?>

<?php if (!empty($_SESSION['user_id']) && empty($dbError)): ?>
<nav class="topbar" id="topbarNav">
    <div class="topbar-inner">
        <a href="dashboard.php" class="brand">
            <img src="assets/Logo Kemper RI.webp" alt="Kementerian Perdagangan RI" class="brand-logo-kemper">
            <img src="assets/logo pt.png" alt="Logo STIMULUS" class="brand-logo-pt">
            <span class="brand-text"><strong><?= APP_NAME ?></strong></span>
        </a>
        <div class="topbar-user">
            <div class="lang-dropdown" id="langDropdownHeader">
                <button class="lang-trigger" id="langTriggerHeader" aria-haspopup="true" aria-expanded="false">
                    <span class="lang-flag"><img src="assets/<?= get_language() === 'id' ? 'Flag_of_Indonesia_(physical_version).svg.webp' : 'Flag_of_the_United_Kingdom_(3-5).svg' ?>" alt="<?= strtoupper(get_language()) ?>" width="18" height="12"></span>
                    <span class="lang-label"><?= get_language() === 'id' ? t('lang.id') : t('lang.en') ?></span>
                    <svg class="lang-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="lang-menu" id="langMenuHeader">
                    <a href="#" data-lang="id" class="lang-option <?= get_language() === 'id' ? 'active' : '' ?>">
                        <span class="lang-flag"><img src="assets/Flag_of_Indonesia_(physical_version).svg.webp" alt="ID" width="20" height="14"></span>
                        <span><?= t('lang.id') ?></span>
                        <?php if (get_language() === 'id'): ?><span class="lang-check">✓</span><?php endif; ?>
                    </a>
                    <a href="#" data-lang="en" class="lang-option <?= get_language() === 'en' ? 'active' : '' ?>">
                        <span class="lang-flag"><img src="assets/Flag_of_the_United_Kingdom_(3-5).svg" alt="EN" width="20" height="14"></span>
                        <span><?= t('lang.en') ?></span>
                        <?php if (get_language() === 'en'): ?><span class="lang-check">✓</span><?php endif; ?>
                    </a>
                </div>
            </div>
            <?php $jumlahBelumDibaca = hitung_notifikasi_belum_dibaca($pdo, current_user_id()); ?>
            <a href="notifikasi.php" class="notif-bell <?= $jumlahBelumDibaca > 0 ? 'has-notif' : '' ?>" title="<?= t('nav.notifikasi') ?>">
                🔔
                <?php if ($jumlahBelumDibaca > 0): ?>
                    <span class="notif-badge"><?= $jumlahBelumDibaca > 9 ? '9+' : $jumlahBelumDibaca ?></span>
                <?php endif; ?>
            </a>
            <div class="avatar-circle" title="<?= htmlspecialchars($_SESSION['nama']) ?>"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <span class="user-role"><?= htmlspecialchars(role_label($_SESSION['role'])) ?></span>
            </div>
            <a href="#" class="btn-nav-logout" id="btnLogout" data-t="nav.keluar"><?= t('nav.keluar') ?></a>
        </div>
    </div>
</nav>

<!-- Logout Confirmation Modal -->
<div class="modal-overlay" id="logoutModal">
    <div class="modal-card">
        <div class="modal-icon">🚪</div>
        <h3 class="modal-title" data-t="ui.konfirmasi_keluar"><?= t('ui.konfirmasi_keluar') ?></h3>
        <p class="modal-text" data-t="ui.konfirmasi_text"><?= t('ui.konfirmasi_text') ?></p>
        <div class="modal-actions">
            <a href="#" class="btn btn-secondary" id="btnLogoutCancel" data-t="ui.batal"><?= t('ui.batal') ?></a>
            <a href="logout.php" class="btn btn-danger" id="btnLogoutConfirm" data-t="ui.ya_keluar"><?= t('ui.ya_keluar') ?></a>
        </div>
    </div>
</div>

<script>
(function() {
    var modal = document.getElementById('logoutModal');
    var btnLogout = document.getElementById('btnLogout');
    var btnCancel = document.getElementById('btnLogoutCancel');

    btnLogout.addEventListener('click', function(e) {
        e.preventDefault();
        modal.classList.add('modal-visible');
    });

    btnCancel.addEventListener('click', function(e) {
        e.preventDefault();
        modal.classList.remove('modal-visible');
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('modal-visible');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('modal-visible')) {
            modal.classList.remove('modal-visible');
        }
    });
})();
</script>

<script>
(function() {
    var dd = document.getElementById('langDropdownHeader');
    var trigger = document.getElementById('langTriggerHeader');
    var menu = document.getElementById('langMenuHeader');
    if (!dd || !trigger || !menu) return;

    // Toggle dropdown
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        var isOpen = dd.classList.toggle('open');
        trigger.setAttribute('aria-expanded', isOpen);
    });

    document.addEventListener('click', function(e) {
        if (!dd.contains(e.target)) {
            dd.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dd.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
        }
    });

    // ── Instant language switch (no page reload) ──
    var FLAG_ID = 'assets/Flag_of_Indonesia_(physical_version).svg.webp';
    var FLAG_EN = 'assets/Flag_of_the_United_Kingdom_(3-5).svg';

    function switchLang(lang) {
        // Update session on server
        fetch('set_lang.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'lang=' + lang
        });

        // Update all [data-t] elements
        document.querySelectorAll('[data-t]').forEach(function(el) {
            var key = el.getAttribute('data-t');
            var text = window._t[key] ? window._t[key][lang] : null;
            if (text !== null) el.textContent = text;
        });

        // Update trigger flag + label
        var flagImg = trigger.querySelector('.lang-flag img');
        var label = trigger.querySelector('.lang-label');
        if (flagImg) flagImg.src = lang === 'id' ? FLAG_ID : FLAG_EN;
        if (flagImg) flagImg.alt = lang.toUpperCase();
        if (label) label.textContent = lang === 'id' ? (window._t['lang.id'] ? window._t['lang.id'].id : 'Indonesia') : (window._t['lang.en'] ? window._t['lang.en'].en : 'English');

        // Update active state in menu
        menu.querySelectorAll('.lang-option').forEach(function(opt) {
            var isActive = opt.getAttribute('data-lang') === lang;
            opt.classList.toggle('active', isActive);
            var check = opt.querySelector('.lang-check');
            if (isActive && !check) {
                var span = document.createElement('span');
                span.className = 'lang-check';
                span.textContent = '✓';
                opt.appendChild(span);
            } else if (!isActive && check) {
                check.remove();
            }
        });

        // Update html lang
        document.documentElement.lang = lang;

        // Close dropdown
        dd.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    // Bind language option clicks
    menu.querySelectorAll('.lang-option[data-lang]').forEach(function(opt) {
        opt.addEventListener('click', function(e) {
            e.preventDefault();
            switchLang(this.getAttribute('data-lang'));
        });
    });

    // Expose for landing page to reuse
    window._switchLang = switchLang;
})();
</script>
<?php endif; ?>

<main class="page-wrap">
