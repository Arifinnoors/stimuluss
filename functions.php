<?php
/**
 * Kumpulan fungsi bantu: label status, mesin alur kerja (workflow),
 * dan pencatatan log aktivitas (audit trail).
 *
 * Alur status mengikuti "Proses Bisnis Rancangan Stimulus":
 * diajukan -> verifikasi_administrasi -> verifikasi_berkas -> penilaian
 * -> review_koordinator -> review_ketua_tim -> menunggu_ttd_direktur -> selesai
 * (dengan opsi "dikembalikan_uml" jika berkas belum lengkap)
 */

// Label yang ditampilkan ke pengguna untuk tiap kode status
function status_label(string $status): string
{
    $key = 'status.' . $status;
    if (function_exists('t')) {
        $translated = t($key);
        // t() returns the key itself if not found — fall back to Indonesian
        if ($translated !== $key) return $translated;
    }
    // Fallback hardcoded
    $labels = [
        'diajukan'                => 'Diajukan',
        'verifikasi_administrasi' => 'Verifikasi Administrasi (Koordinator)',
        'dikembalikan_uml'        => 'Dikembalikan ke UML (Perlu Kelengkapan)',
        'verifikasi_berkas'       => 'Verifikasi Berkas (Verifikator)',
        'penilaian'               => 'Penilaian Zoom Meeting',
        'review_koordinator'      => 'Review Draft SKVI (Koordinator)',
        'review_ketua_tim'        => 'Review Draft SKVI (Ketua Tim)',
        'menunggu_ttd_direktur'   => 'Menunggu Tanda Tangan Direktur',
        'selesai'                 => 'Selesai - SKVI Terbit',
    ];
    return $labels[$status] ?? $status;
}

// Warna badge status (dipakai di CSS class badge-*)
function status_badge_class(string $status): string
{
    $map = [
        'diajukan'                => 'badge-info',
        'verifikasi_administrasi' => 'badge-info',
        'dikembalikan_uml'        => 'badge-warning',
        'verifikasi_berkas'       => 'badge-info',
        'penilaian'               => 'badge-info',
        'review_koordinator'      => 'badge-info',
        'review_ketua_tim'        => 'badge-info',
        'menunggu_ttd_direktur'   => 'badge-info',
        'selesai'                 => 'badge-success',
    ];
    return $map[$status] ?? 'badge-info';
}

// Role mana yang bertanggung jawab memproses status saat ini
function status_pemilik(string $status): string
{
    $map = [
        'diajukan'                => 'koordinator',
        'verifikasi_administrasi' => 'koordinator',
        'dikembalikan_uml'        => 'uml',
        'verifikasi_berkas'       => 'verifikator',
        'penilaian'               => 'verifikator',
        'review_koordinator'      => 'koordinator',
        'review_ketua_tim'        => 'ketua_tim',
        'menunggu_ttd_direktur'   => 'direktur',
        'selesai'                 => 'uml',
    ];
    return $map[$status] ?? '';
}

// Jenis permohonan -> label
function jenis_label(string $jenis): string
{
    $key = 'jenis.' . $jenis;
    if (function_exists('t')) {
        $translated = t($key);
        if ($translated !== $key) return $translated;
    }
    $map = ['baru' => 'Baru', 'tambah' => 'Tambah Lingkup', 'kurang' => 'Kurangi Lingkup'];
    return $map[$jenis] ?? $jenis;
}

// Label role -> teks tampilan
function role_label(string $role): string
{
    $map = [
        'uml'         => ['id' => 'UML (Pemohon)',          'en' => 'UML (Applicant)'],
        'koordinator' => ['id' => 'Koordinator',            'en' => 'Coordinator'],
        'verifikator' => ['id' => 'Verifikator',            'en' => 'Verifier'],
        'ketua_tim'   => ['id' => 'Ketua Tim',              'en' => 'Team Leader'],
        'direktur'    => ['id' => 'Direktur',               'en' => 'Director'],
    ];
    $lang = function_exists('get_language') ? get_language() : 'id';
    return $map[$role][$lang] ?? $role;
}

// Generate kode permohonan unik, format: SKVI-2026-0001
function generate_kode_permohonan(PDO $pdo): string
{
    $tahun = date('Y');
    $stmt  = $pdo->prepare("SELECT COUNT(*) AS jumlah FROM permohonan WHERE kode_permohonan LIKE ?");
    $stmt->execute(["SKVI-$tahun-%"]);
    $jumlah = (int) $stmt->fetch()['jumlah'];
    $urut   = str_pad((string) ($jumlah + 1), 4, '0', STR_PAD_LEFT);
    return "SKVI-$tahun-$urut";
}

// Catat satu baris riwayat/audit trail
function catat_log(PDO $pdo, int $permohonanId, int $userId, ?string $statusSebelum, string $statusSesudah, string $keterangan): void
{
    $stmt = $pdo->prepare(
        "INSERT INTO log_aktivitas (permohonan_id, user_id, status_sebelum, status_sesudah, keterangan)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([$permohonanId, $userId, $statusSebelum, $statusSesudah, $keterangan]);
}

// Format tanggal Indonesia sederhana, contoh: 10 Agu 2026, 14:30
function format_tanggal(?string $datetime): string
{
    if (!$datetime) return '-';
    $bulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
              '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
    $ts = strtotime($datetime);
    return date('d', $ts) . ' ' . $bulan[date('m', $ts)] . ' ' . date('Y H:i', $ts);
}

// ---------------------------------------------------------
// NOTIFIKASI
// ---------------------------------------------------------

// Simpan satu notifikasi untuk satu user
function buat_notifikasi(PDO $pdo, int $userId, int $permohonanId, string $pesan): void
{
    $stmt = $pdo->prepare('INSERT INTO notifikasi (user_id, permohonan_id, pesan) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $permohonanId, $pesan]);
}

/**
 * Dipanggil setiap kali status permohonan berpindah (termasuk saat pertama diajukan).
 * Otomatis mengirim notifikasi ke SEMUA user yang berperan sebagai pemilik status baru
 * (memakai status_pemilik() yang sama dengan yang menentukan siapa boleh bertindak),
 * supaya logikanya konsisten dengan alur approval yang sudah berjalan.
 */
function notifikasi_transisi(PDO $pdo, int $permohonanId, string $statusBaru, string $kodePermohonan): void
{
    $pemilik = status_pemilik($statusBaru);
    if ($pemilik === '') {
        return;
    }

    if ($pemilik === 'uml') {
        // Untuk UML, hanya pemohon yang bersangkutan yang diberi tahu
        $stmt = $pdo->prepare('SELECT id, email FROM users WHERE id = (SELECT uml_id FROM permohonan WHERE id = ?)');
        $stmt->execute([$permohonanId]);
        $targets = $stmt->fetchAll();
    } else {
        // Untuk role staf, semua akun dengan role tsb diberi tahu
        $stmt = $pdo->prepare('SELECT id, email FROM users WHERE role = ?');
        $stmt->execute([$pemilik]);
        $targets = $stmt->fetchAll();
    }

    $pesan = ($statusBaru === 'selesai')
        ? "SKVI untuk permohonan $kodePermohonan sudah terbit dan siap diunduh."
        : "Permohonan $kodePermohonan menunggu tindakan Anda: " . status_label($statusBaru) . '.';

    foreach ($targets as $target) {
        buat_notifikasi($pdo, (int) $target['id'], $permohonanId, $pesan);
        if (EMAIL_NOTIF_ENABLED) {
            kirim_email_notifikasi($target['email'], '[' . APP_NAME . '] ' . $pesan, $pesan);
        }
    }
}

// Jumlah notifikasi yang belum dibaca milik satu user (dipakai untuk badge di navbar)
function hitung_notifikasi_belum_dibaca(PDO $pdo, int $userId): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND dibaca = 0');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

// Ambil daftar notifikasi milik satu user, terbaru lebih dulu
function ambil_notifikasi(PDO $pdo, int $userId, int $limit = 30): array
{
    $stmt = $pdo->prepare(
        'SELECT n.*, p.kode_permohonan
         FROM notifikasi n JOIN permohonan p ON p.id = n.permohonan_id
         WHERE n.user_id = ? ORDER BY n.created_at DESC LIMIT ' . (int) $limit
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Tandai seluruh notifikasi milik satu user sebagai sudah dibaca
function tandai_semua_dibaca(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare('UPDATE notifikasi SET dibaca = 1 WHERE user_id = ? AND dibaca = 0');
    $stmt->execute([$userId]);
}

/**
 * Kirim email lewat SMTP polos (tanpa library tambahan, supaya tidak perlu Composer
 * di setup XAMPP). Dipanggil hanya kalau EMAIL_NOTIF_ENABLED = true di config.php.
 * Kalau gagal, fungsi ini akan diam-diam kembalikan false (tidak menghentikan alur
 * utama aplikasi) — supaya kalau SMTP belum di-setup dengan benar, permohonan tetap
 * bisa diproses seperti biasa.
 */
function kirim_email_notifikasi(string $emailTujuan, string $subjek, string $pesan): bool
{
    if (!EMAIL_NOTIF_ENABLED) {
        return false;
    }
    try {
        $ctx = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $sock = @stream_socket_client('tcp://' . SMTP_HOST . ':' . SMTP_PORT, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);
        if (!$sock) return false;

        $baca = fn() => fgets($sock, 515);
        $kirim = function (string $cmd) use ($sock) { fwrite($sock, $cmd . "\r\n"); };

        $baca();
        $kirim('EHLO localhost'); $baca();
        $kirim('STARTTLS'); $baca();
        stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $kirim('EHLO localhost'); $baca();
        $kirim('AUTH LOGIN'); $baca();
        $kirim(base64_encode(SMTP_USER)); $baca();
        $kirim(base64_encode(SMTP_PASS)); $baca();
        $kirim('MAIL FROM:<' . SMTP_USER . '>'); $baca();
        $kirim('RCPT TO:<' . $emailTujuan . '>'); $baca();
        $kirim('DATA'); $baca();
        $kirim("Subject: $subjek\r\nFrom: " . APP_NAME . ' <' . SMTP_USER . ">\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n$pesan\r\n.");
        $baca();
        $kirim('QUIT');
        fclose($sock);
        return true;
    } catch (\Throwable $e) {
        return false;
    }
}

// Upload file bantuan, mengembalikan path relatif yang disimpan ke DB, atau null jika tidak ada file
function handle_upload(string $inputName, string $subfolder): ?string
{
    if (empty($_FILES[$inputName]['name']) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
    $ext     = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }
    $dirTujuan = __DIR__ . '/../uploads/' . $subfolder;
    // Buat direktori jika belum ada
    if (!is_dir($dirTujuan)) {
        mkdir($dirTujuan, 0755, true);
    }
    // Cek apakah direktori bisa ditulis
    if (!is_writable($dirTujuan)) {
        return null;
    }
    $namaBaru = $subfolder . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
    $tujuan   = $dirTujuan . '/' . $namaBaru;
    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $tujuan)) {
        return 'uploads/' . $subfolder . '/' . $namaBaru;
    }
    return null;
}
