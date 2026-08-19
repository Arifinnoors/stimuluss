<?php
/**
 * Translations — ID / EN
 * Helper: t('key') returns text for current session language.
 * Auto-translates missing EN keys via Google Translate (free, no API key).
 */

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'id';
}

function set_language(string $lang): void {
    $_SESSION['lang'] = in_array($lang, ['id', 'en']) ? $lang : 'id';
}

function get_language(): string {
    return $_SESSION['lang'] ?? 'id';
}

/**
 * Auto-translate text via Google Translate (free endpoint, no API key).
 * Caches result in session to avoid repeated calls.
 */
function auto_translate(string $text, string $target = 'en'): string {
    if (trim($text) === '') return $text;
    $cacheKey = 'at_' . md5($text . $target);
    // Check session cache
    if (isset($_SESSION['auto_translations'][$cacheKey])) {
        return $_SESSION['auto_translations'][$cacheKey];
    }
    try {
        $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=id&tl='
               . urlencode($target) . '&dt=t&q=' . urlencode($text);
        $ctx = stream_context_create(['http' => ['timeout' => 3]]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) return $text;
        $json = json_decode($response, true);
        if (!is_array($json) || !isset($json[0])) return $text;
        $translated = '';
        foreach ($json[0] as $segment) {
            if (isset($segment[0])) $translated .= $segment[0];
        }
        $result = $translated !== '' ? $translated : $text;
        // Cache in session (limit to 200 entries to prevent bloat)
        if (!isset($_SESSION['auto_translations'])) {
            $_SESSION['auto_translations'] = [];
        }
        if (count($_SESSION['auto_translations']) < 200) {
            $_SESSION['auto_translations'][$cacheKey] = $result;
        }
        return $result;
    } catch (\Throwable $e) {
        return $text;
    }
}

function t(string $key): string {
    global $translations;
    $lang = get_language();
    // 1. Check manual translation for current lang
    $text = $translations[$lang][$key] ?? null;
    if ($text !== null) return $text;
    // 2. Get Indonesian source text as fallback
    $source = $translations['id'][$key] ?? null;
    if ($source === null) return $key;
    // 3. If lang is 'id', return source directly
    if ($lang === 'id') return $source;
    // 4. Auto-translate missing EN key
    return auto_translate($source, $lang);
}

/**
 * Output <span data-t="key">text</span> for JS instant switching.
 * Usage: <?= tp('hero.title') ?> or <?= tp('hero.title', '<h1>') ?>
 */
function tp(string $key, string $tag = 'span'): string {
    global $translations;
    $text = t($key);
    $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    return '<' . $tag . ' data-t="' . $key . '">' . $safe . '</' . $tag . '>';
}

/**
 * Output all translations as JSON for JS instant switching.
 * Called once in the page to embed the full dictionary.
 */
function t_json(): string {
    global $translations;
    $output = [];
    foreach ($translations['id'] as $key => $idText) {
        $enText = $translations['en'][$key] ?? null;
        if ($enText === null) $enText = auto_translate($idText, 'en');
        $output[$key] = ['id' => $idText, 'en' => $enText];
    }
    return json_encode($output, JSON_UNESCAPED_UNICODE);
}

$translations = [

/* ──────────────────────────────────────────────
   NAVIGATION
   ────────────────────────────────────────────── */
'id' => [
    'nav.tentang'        => 'Tentang',
    'nav.alur_kerja'     => 'Alur Kerja',
    'nav.faq'            => 'FAQ',
    'nav.masuk'          => 'Masuk',
    'nav.keluar'         => 'Keluar',
    'nav.notifikasi'     => 'Notifikasi',
    'nav.langsung_konten'=> 'Langsung ke konten utama',
    'lang.pilih'         => 'Pilih Bahasa',
    'lang.id'            => 'Indonesia',
    'lang.en'            => 'English',

    /* Hero */
    'hero.title'         => 'Sistem Informasi <br><span class="highlight">Monitoring Layanan UTTP</span> &amp; Standar Ukuran',
    'hero.subtitle'      => 'Platform digital terpadu untuk mengelola proses verifikasi, penilaian, dan penerbitan Surat Keterangan Verifikasi Instalasi (SKVI) bagi Unit Tera/Tera Ulang Persyaratan Teknis (UTTP).',
    'hero.btn_masuk'     => 'Masuk ke Sistem',
    'hero.btn_alur'      => 'Lihat Alur Kerja &darr;',

    /* Tentang */
    'about.label'        => 'Tentang',
    'about.title'        => 'Apa itu STIMULUS?',
    'about.desc1'        => '<strong>STIMULUS</strong> (<em>Sistem Informasi Monitoring untuk Layanan UTTP dan Standar Ukuran</em>) adalah aplikasi web yang dirancang untuk mendigitalisasi seluruh alur kerja permohonan verifikasi Unit Tera/Tera Ulang Persyaratan Teknis (UTTP).',
    'about.desc2'        => 'Sistem ini mengotomatiskan proses dari pengajuan permohonan oleh UML (Unit Metrologi Legal) hingga penerbitan Surat Keterangan Verifikasi Instalasi (SKVI) oleh Direktur, dengan mekanisme approval berlapis, notifikasi real-time, dan audit trail lengkap.',
    'about.feat1'        => 'Pengajuan &amp; dokumen digital tanpa kertas',
    'about.feat2'        => 'Alur approval multi-role yang terstruktur',
    'about.feat3'        => 'Notifikasi otomatis ke setiap pemangku kepentingan',
    'about.feat4'        => 'Jejak audit (audit trail) untuk setiap perubahan status',
    'about.feat5'        => 'Penerbitan SKVI digital dengan tanda tangan elektronik',
    'about.skvi_title'   => 'SKVI Digital',
    'about.skvi_desc'    => 'Surat Keterangan Verifikasi Internal yang diterbitkan secara digital, terintegrasi dengan seluruh proses verifikasi dan penilaian di lapangan.',

    /* Alur Kerja / Roles */
    'roles.label'        => 'Alur Kerja',
    'roles.title'        => '5 Peran dalam Alur Kerja',
    'roles.subtitle'     => 'Setiap peran memiliki tanggung jawab spesifik dalam memastikan proses verifikasi UTTP berjalan lancar.',
    'roles.uml_desc'     => 'Unit Metrology Legal — mengajukan permohonan verifikasi UTTP',
    'roles.koord_desc'   => 'Verifikasi administrasi &amp; review draft SKVI',
    'roles.verify_desc'  => 'Verifikasi berkas &amp; penilaian melalui Zoom meeting',
    'roles.kt_desc'      => 'Review &amp; paraf draft SKVI sebelum tanda tangan',
    'roles.dir_desc'     => 'Tanda tangan &amp; menerbitkan SKVI resmi',

    /* FAQ */
    'faq.label'          => 'FAQ',
    'faq.title'          => 'Pertanyaan yang Sering Diajukan',
    'faq.q1'             => 'Apa itu STIMULUS?',
    'faq.a1'             => 'STIMULUS adalah Sistem Informasi Monitoring Layanan UTTP &amp; Standar Ukuran, platform digital dari Kementerian Perdagangan RI untuk mengelola seluruh proses verifikasi dan penerbitan SKVI secara elektronik.',
    'faq.q2'             => 'Siapa yang bisa menggunakan sistem ini?',
    'faq.a2'             => 'Sistem ini digunakan oleh 5 peran: UML (Unit Metrology Legal), Koordinator, Verifikator, Ketua Tim, dan Direktur. Setiap peran memiliki akses dan fitur yang sesuai dengan tanggung jawabnya.',
    'faq.q3'             => 'Bagaimana cara mengajukan permohonan?',
    'faq.a3'             => 'Login sebagai UML, lalu klik "Ajukan Permohonan Baru" pada dashboard. Isi data yang diperlukan, unggah dokumen pendukung, dan submit. Anda akan menerima notifikasi setiap kali status permohonan berubah.',
    'faq.q4'             => 'Berapa lama proses verifikasi?',
    'faq.a4'             => 'Proses verifikasi melalui 5 tahap: pengajuan, administrasi, verifikasi lapangan, paraf ketua tim, hingga penerbitan SKVI oleh Direktur. Durasi bergantung pada kelengkapan dokumen dan ketersediaan verifikator.',
    'faq.q5'             => 'Apakah ada biaya untuk menggunakan sistem ini?',
    'faq.a5'             => 'Tidak. STIMULUS adalah layanan digital dari Kementerian Perdagangan RI yang dapat digunakan secara gratis oleh seluruh Unit Metrology Legal di Indonesia.',

    /* Login Modal */
    'login.title'        => 'Masuk ke STIMULUS',
    'login.subtitle'     => 'Sistem Informasi Monitoring Layanan UTTP &amp; Standar Ukuran',
    'login.email'        => 'Email',
    'login.email_ph'     => 'nama@instansi.go.id',
    'login.password'     => 'Password',
    'login.submit'       => 'Masuk',
    'login.demo_title'   => 'Untuk uji coba tiap role',
    'login.footer'       => 'Kementerian Perdagangan RI',
    'login.demo_hint'    => 'Password untuk semua akun demo: <strong>password123</strong>',

    /* UI */
    'ui.batal'           => 'Batal',
    'ui.ya_keluar'       => 'Ya, Keluar',
    'ui.konfirmasi_keluar' => 'Konfirmasi Keluar',
    'ui.konfirmasi_text' => 'Apakah Anda yakin ingin keluar dari sistem STIMULUS?',

    /* DB Error */
    'db_error.title'     => 'Koneksi Database Gagal',
    'db_error.desc'      => 'Aplikasi tidak dapat terhubung ke database. Periksa konfigurasi di <code>config.php</code>.',
    'db_error.hint'      => 'Pastikan Anda sudah membuat database dan user di panel hosting, lalu update nilai <code>DB_HOST</code>, <code>DB_NAME</code>, <code>DB_USER</code>, <code>DB_PASS</code> di file <code>config.php</code>.',

    /* Status Labels */
    'status.diajukan'              => 'Diajukan',
    'status.verifikasi_administrasi' => 'Verifikasi Administrasi (Koordinator)',
    'status.dikembalikan_uml'      => 'Dikembalikan ke UML (Perlu Kelengkapan)',
    'status.verifikasi_berkas'     => 'Verifikasi Berkas (Verifikator)',
    'status.penilaian'             => 'Penilaian Zoom Meeting',
    'status.review_koordinator'    => 'Review Draft SKVI (Koordinator)',
    'status.review_ketua_tim'      => 'Review Draft SKVI (Ketua Tim)',
    'status.menunggu_ttd_direktur' => 'Menunggu Tanda Tangan Direktur',
    'status.selesai'               => 'Selesai - SKVI Terbit',

    /* Jenis Labels */
    'jenis.baru'     => 'Baru',
    'jenis.tambah'   => 'Tambah Lingkup',
    'jenis.kurang'   => 'Kurangi Lingkup',

    /* Dashboard */
    'db.page_title'      => 'Dashboard',
    'db.halo'            => 'Halo',
    'db.subtitle_uml'    => 'Pantau status permohonan SKVI yang kamu ajukan.',
    'db.subtitle_nonuml' => 'Berikut permohonan SKVI yang perlu ditindaklanjuti dan seluruh permohonan yang berjalan di sistem.',
    'db.btn_ajukan'      => '✨ Ajukan Permohonan Baru',
    'db.stat_total'      => 'Total Permohonan',
    'db.stat_proses'     => 'Sedang Diproses',
    'db.stat_terbit'     => 'SKVI Terbit',
    'db.stat_perlu'      => 'Perlu Tindakan Anda',
    'db.stat_total_sys'  => 'Total Permohonan (Sistem)',
    'db.card_saya'       => 'Permohonan Saya',
    'db.card_perlu'      => 'Perlu Tindakan Anda',
    'db.cardSemua'       => 'Semua Permohonan di Sistem',
    'db.th_kode'         => 'Kode',
    'db.th_uttp'         => 'UTTP',
    'db.th_jenis'        => 'Jenis',
    'db.th_diajukan'     => 'Diajukan',
    'db.th_status'       => 'Status',
    'db.th_uml'          => 'UML',
    'db.empty_saya'      => 'Belum ada permohonan. Klik "Ajukan Permohonan Baru" untuk memulai.',
    'db.empty_perlu'     => 'Tidak ada permohonan yang menunggu tindakan kamu saat ini. 🎉',
    'db.empty_semua'     => 'Belum ada permohonan di sistem.',
    'db.btn_lihat'       => 'Lihat →',
    'db.btn_proses'      => 'Proses →',
    'db.baru'            => 'baru',

    /* Footer */
    'footer.desc'        => 'Sistem Informasi Monitoring Layanan UTTP &amp; Standar Ukuran.<br>Dikembangkan oleh Kementerian Perdagangan Republik Indonesia.',
    'footer.copyright'   => '&copy; %year% Kementerian Perdagangan RI. Hak Cipta Dilindungi.',
    'footer.kontak'      => 'Kontak',
    'footer.privasi'     => 'Kebijakan Privasi',
    'footer.ketentuan'   => 'Syarat &amp; Ketentuan',
    'footer.copyright_short' => '&copy; %year% Kementerian Perdagangan RI',
],

'en' => [
    'nav.tentang'        => 'About',
    'nav.alur_kerja'     => 'Workflow',
    'nav.faq'            => 'FAQ',
    'nav.masuk'          => 'Sign In',
    'nav.keluar'         => 'Sign Out',
    'nav.notifikasi'     => 'Notifications',
    'nav.langsung_konten'=> 'Skip to main content',
    'lang.pilih'         => 'Select Language',
    'lang.id'            => 'Indonesia',
    'lang.en'            => 'English',

    /* Hero */
    'hero.title'         => 'Information System for <br><span class="highlight">UTTP Service Monitoring</span> &amp; Measurement Standards',
    'hero.subtitle'      => 'An integrated digital platform for managing the verification, assessment, and issuance of Installation Verification Certificates (SKVI) for Technical Requirement Verification/Trials Measurement Units (UTTP).',
    'hero.btn_masuk'     => 'Sign In',
    'hero.btn_alur'      => 'View Workflow &darr;',

    /* About */
    'about.label'        => 'About',
    'about.title'        => 'What is STIMULUS?',
    'about.desc1'        => '<strong>STIMULUS</strong> (<em>Information System for Monitoring UTTP Services and Measurement Standards</em>) is a web application designed to digitalize the entire workflow of UTTP Technical Requirement Verification/Trials Unit verification requests.',
    'about.desc2'        => 'The system automates the process from request submission by UML (Legal Metrology Unit) to the issuance of Installation Verification Certificates (SKVI) by the Director, with layered approval mechanisms, real-time notifications, and a complete audit trail.',
    'about.feat1'        => 'Paperless digital submissions &amp; documents',
    'about.feat2'        => 'Structured multi-role approval workflow',
    'about.feat3'        => 'Automatic notifications to every stakeholder',
    'about.feat4'        => 'Audit trail for every status change',
    'about.feat5'        => 'Digital SKVI issuance with electronic signature',
    'about.skvi_title'   => 'Digital SKVI',
    'about.skvi_desc'    => 'Internal Verification Certificate issued digitally, integrated with the entire field verification and assessment process.',

    /* Roles */
    'roles.label'        => 'Workflow',
    'roles.title'        => '5 Roles in the Workflow',
    'roles.subtitle'     => 'Each role has specific responsibilities in ensuring the UTTP verification process runs smoothly.',
    'roles.uml_desc'     => 'Legal Metrology Unit — submits UTTP verification requests',
    'roles.koord_desc'   => 'Administrative verification &amp; SKVI draft review',
    'roles.verify_desc'  => 'Document verification &amp; assessment via Zoom meeting',
    'roles.kt_desc'      => 'Review &amp; initial SKVI draft before signing',
    'roles.dir_desc'     => 'Signs &amp; issues official SKVI',

    /* FAQ */
    'faq.label'          => 'FAQ',
    'faq.title'          => 'Frequently Asked Questions',
    'faq.q1'             => 'What is STIMULUS?',
    'faq.a1'             => 'STIMULUS is an Information System for Monitoring UTTP Services &amp; Measurement Standards, a digital platform from the Ministry of Trade of the Republic of Indonesia for managing the entire SKVI verification and issuance process electronically.',
    'faq.q2'             => 'Who can use this system?',
    'faq.a2'             => 'This system is used by 5 roles: UML (Legal Metrology Unit), Coordinator, Verifier, Team Leader, and Director. Each role has access and features matching their responsibilities.',
    'faq.q3'             => 'How do I submit a request?',
    'faq.a3'             => 'Sign in as UML, then click "New Request" on the dashboard. Fill in the required data, upload supporting documents, and submit. You will receive notifications whenever the request status changes.',
    'faq.q4'             => 'How long does the verification process take?',
    'faq.a4'             => 'The verification process goes through 5 stages: submission, administration, field verification, team leader initial, and SKVI issuance by the Director. Duration depends on document completeness and verifier availability.',
    'faq.q5'             => 'Is there a fee to use this system?',
    'faq.a5'             => 'No. STIMULUS is a digital service from the Ministry of Trade of the Republic of Indonesia that can be used free of charge by all Legal Metrology Units in Indonesia.',

    /* Login Modal */
    'login.title'        => 'Sign In to STIMULUS',
    'login.subtitle'     => 'Information System for Monitoring UTTP Services &amp; Measurement Standards',
    'login.email'        => 'Email',
    'login.email_ph'     => 'name@agency.go.id',
    'login.password'     => 'Password',
    'login.submit'       => 'Sign In',
    'login.demo_title'   => 'Demo accounts for each role',
    'login.footer'       => 'Ministry of Trade, Republic of Indonesia',
    'login.demo_hint'    => 'Password for all demo accounts: <strong>password123</strong>',

    /* UI */
    'ui.batal'           => 'Cancel',
    'ui.ya_keluar'       => 'Yes, Sign Out',
    'ui.konfirmasi_keluar' => 'Confirm Sign Out',
    'ui.konfirmasi_text' => 'Are you sure you want to sign out of the STIMULUS system?',

    /* DB Error */
    'db_error.title'     => 'Database Connection Failed',
    'db_error.desc'      => 'The application cannot connect to the database. Check the configuration in <code>config.php</code>.',
    'db_error.hint'      => 'Make sure you have created the database and user in your hosting panel, then update the <code>DB_HOST</code>, <code>DB_NAME</code>, <code>DB_USER</code>, <code>DB_PASS</code> values in <code>config.php</code>.',

    /* Status Labels */
    'status.diajukan'              => 'Submitted',
    'status.verifikasi_administrasi' => 'Administrative Verification (Coordinator)',
    'status.dikembalikan_uml'      => 'Returned to Applicant (Incomplete Documents)',
    'status.verifikasi_berkas'     => 'Document Verification (Verifier)',
    'status.penilaian'             => 'Zoom Meeting Assessment',
    'status.review_koordinator'    => 'SKVI Draft Review (Coordinator)',
    'status.review_ketua_tim'      => 'SKVI Draft Review (Team Leader)',
    'status.menunggu_ttd_direktur' => 'Awaiting Director Signature',
    'status.selesai'               => 'Completed - SKVI Issued',

    /* Jenis Labels */
    'jenis.baru'     => 'New',
    'jenis.tambah'   => 'Add Scope',
    'jenis.kurang'   => 'Reduce Scope',

    /* Dashboard */
    'db.page_title'      => 'Dashboard',
    'db.halo'            => 'Hello',
    'db.subtitle_uml'    => 'Track the status of your SKVI verification requests.',
    'db.subtitle_nonuml' => 'Here are the SKVI requests that need your action and all requests in the system.',
    'db.btn_ajukan'      => '✨ New Request',
    'db.stat_total'      => 'Total Requests',
    'db.stat_proses'     => 'In Progress',
    'db.stat_terbit'     => 'SKVI Issued',
    'db.stat_perlu'      => 'Needs Your Action',
    'db.stat_total_sys'  => 'Total Requests (System)',
    'db.card_saya'       => 'My Requests',
    'db.card_perlu'      => 'Needs Your Action',
    'db.cardSemua'       => 'All Requests in System',
    'db.th_kode'         => 'Code',
    'db.th_uttp'         => 'UTTP',
    'db.th_jenis'        => 'Type',
    'db.th_diajukan'     => 'Submitted',
    'db.th_status'       => 'Status',
    'db.th_uml'          => 'Applicant',
    'db.empty_saya'      => 'No requests yet. Click "New Request" to get started.',
    'db.empty_perlu'     => 'No requests are waiting for your action right now. 🎉',
    'db.empty_semua'     => 'No requests in the system yet.',
    'db.btn_lihat'       => 'View →',
    'db.btn_proses'      => 'Process →',
    'db.baru'            => 'new',

    /* Footer */
    'footer.desc'        => 'Information System for Monitoring UTTP Services &amp; Measurement Standards.<br>Developed by the Ministry of Trade of the Republic of Indonesia.',
    'footer.copyright'   => '&copy; %year% Ministry of Trade of the Republic of Indonesia. All Rights Reserved.',
    'footer.kontak'      => 'Contact',
    'footer.privasi'     => 'Privacy Policy',
    'footer.ketentuan'   => 'Terms &amp; Conditions',
    'footer.copyright_short' => '&copy; %year% Ministry of Trade, Republic of Indonesia',
],
];
