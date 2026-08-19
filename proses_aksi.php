<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_login();
require_db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$permohonanId = (int) ($_POST['permohonan_id'] ?? 0);
$aksi          = $_POST['aksi'] ?? '';
$role          = current_role();
$userId        = current_user_id();

$stmt = $pdo->prepare('SELECT * FROM permohonan WHERE id = ?');
$stmt->execute([$permohonanId]);
$p = $stmt->fetch();

if (!$p) {
    die('Permohonan tidak ditemukan.');
}

$statusLama = $p['status'];
$redirect   = 'permohonan_detail.php?id=' . $permohonanId;

// Pastikan hanya UML pemilik permohonan yang bisa bertindak atas permohonannya sendiri
if ($role === 'uml' && (int) $p['uml_id'] !== $userId) {
    http_response_code(403);
    die('Permohonan ini bukan milik akun kamu.');
}
// Untuk role staf, pastikan status saat ini memang menjadi tanggung jawab role tsb
if ($role !== 'uml' && status_pemilik($statusLama) !== $role) {
    http_response_code(403);
    die('Permohonan ini sedang bukan di tahap yang menjadi tanggung jawab role kamu.');
}

/**
 * Definisi aksi yang valid: aksi => [role_diizinkan, status_diperlukan, status_baru]
 */
$aksiMap = [
    'lanjut_verifikasi_berkas'        => ['koordinator', ['diajukan', 'verifikasi_administrasi'], 'verifikasi_berkas'],
    'kembalikan_uml_dari_koordinator' => ['koordinator', ['diajukan', 'verifikasi_administrasi'], 'dikembalikan_uml'],
    'kirim_ulang_uml'                 => ['uml',         ['dikembalikan_uml'],                    'verifikasi_administrasi'],
    'verifikasi_berkas_selesai'       => ['verifikator', ['verifikasi_berkas'],                    'penilaian'],
    'kembalikan_uml_dari_verifikator' => ['verifikator', ['verifikasi_berkas'],                    'dikembalikan_uml'],
    'kirim_draft_ke_koordinator'      => ['verifikator', ['penilaian'],                             'review_koordinator'],
    'teruskan_ke_ketua_tim'           => ['koordinator', ['review_koordinator'],                    'review_ketua_tim'],
    'kembalikan_uml_dari_review'      => ['koordinator', ['review_koordinator'],                    'dikembalikan_uml'],
    'teruskan_ke_direktur'            => ['ketua_tim',   ['review_ketua_tim'],                      'menunggu_ttd_direktur'],
    'tanda_tangani'                   => ['direktur',    ['menunggu_ttd_direktur'],                 'selesai'],
];

if (!isset($aksiMap[$aksi])) {
    die('Aksi tidak dikenali.');
}

[$roleDiizinkan, $statusDiperlukan, $statusBaru] = $aksiMap[$aksi];

if ($role !== $roleDiizinkan || !in_array($statusLama, $statusDiperlukan, true)) {
    http_response_code(403);
    die('Aksi ini tidak berlaku untuk status/role saat ini.');
}

// -------- Eksekusi tiap jenis aksi --------
switch ($aksi) {

    case 'lanjut_verifikasi_berkas': {
        $catatan = trim($_POST['catatan'] ?? '');
        $pdo->prepare('UPDATE permohonan SET status = ?, catatan_koordinator = ? WHERE id = ?')
            ->execute([$statusBaru, $catatan, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            'Koordinator memverifikasi administrasi dan meneruskan ke Verifikator.' . ($catatan ? " Catatan: $catatan" : ''));
        break;
    }

    case 'kembalikan_uml_dari_koordinator': {
        $catatan = trim($_POST['catatan'] ?? '');
        if ($catatan === '') { die('Catatan alasan pengembalian wajib diisi.'); }
        $pdo->prepare('UPDATE permohonan SET status = ?, catatan_koordinator = ? WHERE id = ?')
            ->execute([$statusBaru, $catatan, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            "Koordinator mengembalikan permohonan ke UML untuk dilengkapi. Alasan: $catatan");
        break;
    }

    case 'kirim_ulang_uml': {
        $fileBaru = handle_upload('file_permohonan', 'permohonan');
        if ($fileBaru) {
            $pdo->prepare('UPDATE permohonan SET status = ?, file_permohonan = ? WHERE id = ?')
                ->execute([$statusBaru, $fileBaru, $permohonanId]);
        } else {
            $pdo->prepare('UPDATE permohonan SET status = ? WHERE id = ?')
                ->execute([$statusBaru, $permohonanId]);
        }
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            'UML melengkapi kekurangan berkas dan mengirim ulang permohonan.');
        break;
    }

    case 'verifikasi_berkas_selesai': {
        $catatan = trim($_POST['catatan'] ?? '');
        $jadwal  = $_POST['jadwal_zoom'] ?? null;
        $jadwal  = $jadwal ? str_replace('T', ' ', $jadwal) . ':00' : null;
        $pdo->prepare('UPDATE permohonan SET status = ?, catatan_verifikator = ?, jadwal_zoom = ? WHERE id = ?')
            ->execute([$statusBaru, $catatan, $jadwal, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            'Verifikator menyatakan berkas lengkap dan menjadwalkan penilaian melalui Zoom Meeting.');
        break;
    }

    case 'kembalikan_uml_dari_verifikator': {
        $catatan = trim($_POST['catatan'] ?? '');
        if ($catatan === '') { die('Catatan alasan pengembalian wajib diisi.'); }
        $pdo->prepare('UPDATE permohonan SET status = ?, catatan_verifikator = ? WHERE id = ?')
            ->execute([$statusBaru, $catatan, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            "Verifikator meminta UML melengkapi berkas. Alasan: $catatan");
        break;
    }

    case 'kirim_draft_ke_koordinator': {
        $hasil = trim($_POST['hasil_penilaian'] ?? '');
        if ($hasil === '') { die('Hasil penilaian wajib diisi.'); }
        $fileDraft = handle_upload('file_draft_skvi', 'draft');
        $pdo->prepare('UPDATE permohonan SET status = ?, hasil_penilaian = ?, file_draft_skvi = COALESCE(?, file_draft_skvi) WHERE id = ?')
            ->execute([$statusBaru, $hasil, $fileDraft, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            'Verifikator mencatat hasil penilaian Zoom Meeting dan mengirim draft SKVI ke Koordinator.');
        break;
    }

    case 'teruskan_ke_ketua_tim': {
        $catatan = trim($_POST['catatan'] ?? '');
        $pdo->prepare('UPDATE permohonan SET status = ? WHERE id = ?')->execute([$statusBaru, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            'Koordinator memverifikasi draft SKVI dan meneruskan ke Ketua Tim.' . ($catatan ? " Catatan: $catatan" : ''));
        break;
    }

    case 'kembalikan_uml_dari_review': {
        $catatan = trim($_POST['catatan'] ?? '');
        if ($catatan === '') { die('Catatan alasan pengembalian wajib diisi.'); }
        $pdo->prepare('UPDATE permohonan SET status = ?, catatan_koordinator = ? WHERE id = ?')
            ->execute([$statusBaru, $catatan, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            "Koordinator menemukan kekurangan pada draft dan mengembalikan ke UML. Alasan: $catatan");
        break;
    }

    case 'teruskan_ke_direktur': {
        $pdo->prepare('UPDATE permohonan SET status = ? WHERE id = ?')->execute([$statusBaru, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            'Ketua Tim memverifikasi (memaraf) draft SKVI dan meneruskan ke Direktur untuk ditandatangani.');
        break;
    }

    case 'tanda_tangani': {
        $nomor = trim($_POST['nomor_skvi'] ?? '');
        if ($nomor === '') { die('Nomor SKVI wajib diisi.'); }

        $fileFinal = handle_upload('file_skvi_final', 'final');
        if (!$fileFinal) {
            // Jika Direktur tidak mengunggah file manual, buat file sertifikat placeholder
            // otomatis supaya alur unduh tetap bisa didemokan end-to-end.
            $dirFinal = __DIR__ . '/uploads/final';
            if (!is_dir($dirFinal)) {
                mkdir($dirFinal, 0755, true);
            }
            if (is_writable($dirFinal)) {
                $namaFile = 'final_' . time() . '_' . bin2hex(random_bytes(3)) . '.txt';
                $isi = "SERTIFIKAT KEMAMPUAN VERIFIKASI INTERNAL (SKVI)\n"
                     . "Nomor: $nomor\n"
                     . "Kode Permohonan: {$p['kode_permohonan']}\n"
                     . "UTTP: {$p['nama_uttp']}\n"
                     . "Ditandatangani secara digital oleh Direktur pada " . date('d-m-Y H:i') . "\n"
                     . "--- Dokumen placeholder demo, gantikan dengan file SKVI asli pada implementasi produksi ---\n";
                file_put_contents($dirFinal . '/' . $namaFile, $isi);
                $fileFinal = 'uploads/final/' . $namaFile;
            }
        }

        $pdo->prepare('UPDATE permohonan SET status = ?, nomor_skvi = ?, file_skvi_final = ? WHERE id = ?')
            ->execute([$statusBaru, $nomor, $fileFinal, $permohonanId]);
        catat_log($pdo, $permohonanId, $userId, $statusLama, $statusBaru,
            "Direktur menandatangani SKVI secara digital dengan nomor $nomor. SKVI siap diunduh UML.");
        break;
    }
}

// Beri tahu role yang jadi pemilik status baru bahwa ada tindakan menunggu
notifikasi_transisi($pdo, $permohonanId, $statusBaru, $p['kode_permohonan']);

header('Location: ' . $redirect . '&sukses=1');
exit;
