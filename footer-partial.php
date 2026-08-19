<?php
/**
 * Shared footer partial — included by both beranda.php and includes/footer.php.
 * Edit THIS file to change the footer on ALL pages.
 */
$_footer_year = date('Y');
?>
<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-logos">
                <img src="assets/Logo Kemper RI.webp" alt="Logo Kementerian Perdagangan RI" class="footer-logo-img">
                <img src="assets/logo pt.png" alt="Logo STIMULUS" class="footer-logo-img footer-logo-pt">
                <strong class="footer-brand-name">STIMULUS</strong>
            </div>
            <p data-t="footer.desc"><?= t('footer.desc') ?></p>
            <p class="footer-copy" data-t="footer.copyright"><?= str_replace('%year%', $_footer_year, t('footer.copyright')) ?></p>
        </div>
        <div class="footer-links">
        </div>
        <div class="footer-contact">
            <h5 data-t="footer.kontak"><?= t('footer.kontak') ?></h5>
            <ul>
                <li><a href="mailto:stimulus@kemendag.go.id">stimulus@kemendag.go.id</a></li>
                <li>Kementerian Perdagangan RI</li>
                <li>Jl. M.I. Ridwan Rais No.5, Jakarta Pusat</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span><?= str_replace('%year%', $_footer_year, t('footer.copyright_short')) ?></span>
        <span>
            <a href="#" data-t="footer.privasi"><?= t('footer.privasi') ?></a> &middot;
            <a href="#" data-t="footer.ketentuan"><?= t('footer.ketentuan') ?></a>
        </span>
    </div>
</footer>
