<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_role(['uml']);
require_db();

$error = '';
$success = false;

// ── Handle POST ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis     = $_POST['jenis_permohonan'] ?? '';
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if (!in_array($jenis, ['baru', 'tambah', 'kurang'], true)) {
        $error = 'Jenis permohonan wajib dipilih.';
    } elseif (empty($_FILES['file_permohonan']['name']) || $_FILES['file_permohonan']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Berkas permohonan wajib diunggah.';
    } else {
        // Upload berkas permohonan utama
        $filePermohonan = handle_upload('file_permohonan', 'permohonan');

        // Generate kode permohonan
        $kode = generate_kode_permohonan($pdo);

        // Build nama_uttp from SUML items or fallback
        $sumlItems = $_POST['suml'] ?? [];
        $namaUttaParts = [];
        foreach ($sumlItems as $item) {
            $namaUttaParts[] = $item['nama_suml'] ?? '';
        }
        $namaUttp = !empty($namaUttaParts) ? implode(', ', $namaUttaParts) : 'Permohonan ' . ucfirst($jenis);

        // Insert permohonan
        $stmt = $pdo->prepare(
            'INSERT INTO permohonan (kode_permohonan, uml_id, jenis_permohonan, nama_uttp, deskripsi, file_permohonan, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$kode, current_user_id(), $jenis, $namaUttp, $deskripsi, $filePermohonan, 'diajukan']);
        $permohonanId = (int) $pdo->lastInsertId();

        // Insert SUML items
        if (!empty($sumlItems)) {
            $stmtSuml = $pdo->prepare(
                'INSERT INTO permohonan_suml
                 (permohonan_id, kategori, nama_suml, tipe, jumlah_unit, kapasitas_nominal, daya_baca, kelas, nominal, kepemilikan, jenis_verifikasi, verif_terakhir, verif_mendatang, lampiran_skhp, fields_json, lainnya_nama)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmtKomponen = $pdo->prepare(
                'INSERT INTO permohonan_suml_komponen (permohonan_suml_id, nama_komponen, jumlah, daya_baca)
                 VALUES (?, ?, ?, ?)'
            );

            foreach ($sumlItems as $key => $item) {
                $kategori    = $item['kategori'] ?? '';
                $namaSuml    = $item['nama_suml'] ?? '';
                $tipe        = $item['tipe'] ?? 'A';
                $jumlahUnit  = !empty($item['jumlah_unit']) ? (int) $item['jumlah_unit'] : null;
                $kapasitas   = $item['kapasitas_nominal'] ?? null;
                $dayaBaca    = $item['daya_baca'] ?? null;
                $kelas       = $item['kelas'] ?? null;
                $nominal     = $item['nominal'] ?? null;
                $kepemilikan = $item['kepemilikan'] ?? null;
                $jenisVerif  = $item['jenis_verifikasi'] ?? null;
                $verifAkhir  = !empty($item['verif_terakhir']) ? $item['verif_terakhir'] : null;
                $verifMendat = !empty($item['verif_mendatang']) ? $item['verif_mendatang'] : null;
                $lainnyaNama = $item['lainnya_nama'] ?? null;

                // Upload Lampiran SKHP per item
                $lampiranSkhp = null;
                if (!empty($_FILES['suml']['name'][$key]['lampiran_skhp'])
                    && $_FILES['suml']['error'][$key]['lampiran_skhp'] === UPLOAD_ERR_OK) {
                    // Flatten nested $_FILES for handle_upload
                    $flatFile = [
                        'name'      => $_FILES['suml']['name'][$key]['lampiran_skhp'],
                        'type'      => $_FILES['suml']['type'][$key]['lampiran_skhp'],
                        'tmp_name'  => $_FILES['suml']['tmp_name'][$key]['lampiran_skhp'],
                        'error'     => $_FILES['suml']['error'][$key]['lampiran_skhp'],
                        'size'      => $_FILES['suml']['size'][$key]['lampiran_skhp'],
                    ];
                    $originalFiles = $_FILES;
                    $_FILES['_suml_flat'] = $flatFile;
                    $lampiranSkhp = handle_upload('_suml_flat', 'skhp');
                    $_FILES = $originalFiles;
                }

                // Custom fields as JSON
                $customFields = $item['custom'] ?? null;
                $fieldsJson = !empty($customFields) ? json_encode($customFields, JSON_UNESCAPED_UNICODE) : null;

                $stmtSuml->execute([
                    $permohonanId, $kategori, $namaSuml, $tipe,
                    $jumlahUnit, $kapasitas, $dayaBaca, $kelas, $nominal,
                    $kepemilikan, $jenisVerif, $verifAkhir, $verifMendat,
                    $lampiranSkhp, $fieldsJson, $lainnyaNama
                ]);
                $sumlId = (int) $pdo->lastInsertId();

                // Insert komponen (Type B)
                $komponen = $item['komponen'] ?? [];
                foreach ($komponen as $komp) {
                    $kNama  = $komp['nama'] ?? '';
                    $kJumlah = !empty($komp['jumlah']) ? (int) $komp['jumlah'] : null;
                    $kDaya  = $komp['daya_baca'] ?? null;
                    if ($kNama !== '') {
                        $stmtKomponen->execute([$sumlId, $kNama, $kJumlah, $kDaya]);
                    }
                }
            }
        }

        // Log aktivitas
        catat_log($pdo, $permohonanId, current_user_id(), null, 'diajukan',
            'Permohonan SKVI diajukan oleh UML melalui sistem Stimulus.');

        // Notifikasi
        notifikasi_transisi($pdo, $permohonanId, 'diajukan', $kode);

        header('Location: permohonan_detail.php?id=' . $permohonanId . '&sukses=1');
        exit;
    }
}

$pageTitle = t('form.page_title') ?: 'Ajukan Permohonan Baru';
require 'includes/header.php';
?>

<!-- SUML Form Inline CSS — high specificity to override STIMULUS base styles -->
<style id="suml-inline-css">
/* ── Step Indicator ── */
.suml-step-indicator{display:flex !important;align-items:center;justify-content:center;padding:16px 20px;background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(15,26,46,.06);margin-bottom:20px}
.suml-step{display:flex !important;flex-direction:column;align-items:center;flex:1}
.suml-step-num{width:32px;height:32px;border-radius:50%;font-weight:700;font-size:13px;display:flex !important;align-items:center;justify-content:center}
.suml-step-num.active{background:linear-gradient(135deg,#1A8A7D,#2AA198);color:#fff;box-shadow:0 2px 8px rgba(42,161,152,.25)}
.suml-step-num.inactive{background:#F4F6FA;color:#A8A29E;border:2px solid #E2E0DC}
.suml-step-label{font-size:11px;font-weight:600;margin-top:6px}
.suml-step-label.active{color:#1A8A7D}
.suml-step-label.inactive{color:#A8A29E}
.suml-step-line{flex:1;height:2px;margin:0 -8px;margin-bottom:20px}
.suml-step-line.done{background:linear-gradient(90deg,#2AA198,#E0F5F3)}
.suml-step-line.pending{background:#E2E0DC}

/* ── Selector Row ── */
.suml-selector-row{display:flex !important;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:16px}
.suml-selector-row > .suml-field{flex:1 !important;min-width:200px}

/* ── Cards Container ── */
.suml-cards-container{display:none !important;flex-direction:column;gap:16px}
.suml-cards-container.has-cards{display:flex !important}

/* ── Card ── */
.suml-item-card{background:#fff !important;border:1.5px solid #E2E0DC !important;border-radius:14px !important;overflow:hidden;box-shadow:0 2px 12px rgba(15,26,46,.06) !important;animation:sumlCardIn .3s cubic-bezier(.34,1.56,.64,1);margin:0 0 16px !important;padding:0 !important}
@keyframes sumlCardIn{from{opacity:0;transform:translateY(12px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.suml-card-header{display:flex !important;align-items:center;gap:10px;padding:14px 18px !important;background:linear-gradient(135deg,#F0FDFA,#E0F5F3) !important;border-bottom:1.5px solid #E2E0DC}
.suml-card-title{margin:0 !important;font-size:15px !important;font-weight:700 !important;color:#1A8A7D !important;padding:0 !important}
.suml-card-badge{font-size:11px !important;font-weight:600 !important;padding:3px 10px !important;border-radius:20px !important;background:#fff !important;color:#1A8A7D !important;border:1px solid rgba(42,161,152,.2) !important;white-space:nowrap}
.suml-card-remove{margin-left:auto !important;width:28px !important;height:28px !important;border:none !important;border-radius:8px !important;background:rgba(185,28,28,.08) !important;color:#B91C1C !important;font-size:18px !important;font-weight:700 !important;cursor:pointer !important;display:flex !important;align-items:center;justify-content:center;padding:0 !important;line-height:1}

/* ── Card Body ── */
.suml-card-body{padding:18px !important}

/* ── Row & Field ── */
.suml-card-body > .suml-row{display:flex !important;gap:12px;flex-wrap:wrap;margin-bottom:12px}
.suml-card-body > .suml-row > .suml-field{flex:1 !important;min-width:160px;max-width:none}
.suml-field-full{flex:1 1 100% !important;max-width:100%}

/* ── Labels ── */
.suml-card-body .suml-label{display:block !important;font-size:12px !important;font-weight:600 !important;color:#44403C !important;margin-bottom:5px !important;padding:0 !important;border:none !important;background:none !important}
.suml-card-body .suml-label .req{color:#B91C1C !important}
.suml-label-section{font-size:13px !important;font-weight:700 !important;color:#15233D !important;margin-bottom:10px !important;padding-bottom:6px !important;border-bottom:1px solid #E2E0DC !important}

/* ── Inputs — override generic input[type=text], select {width:100%;padding:12px 16px} ── */
.suml-card-body .suml-input,
.suml-card-body .suml-select{width:100% !important;padding:9px 12px !important;border:1.5px solid #E2E0DC !important;border-radius:10px !important;font-size:13px !important;color:#1C1917 !important;background:#fff !important;transition:border-color .2s,box-shadow .2s !important;box-sizing:border-box !important;font-family:inherit !important}
.suml-card-body .suml-input:focus,
.suml-card-body .suml-select:focus{outline:none !important;border-color:#2AA198 !important;box-shadow:0 0 0 3px rgba(42,161,152,.12) !important;transform:none !important}
.suml-card-body .suml-readonly{background:#F4F6FA !important;color:#44403C !important;cursor:default !important}
.suml-input-sm{width:100px !important;min-width:80px !important;padding:6px 8px !important;font-size:12px !important}

/* ── Komponen (Type B) ── */
.suml-komponen-section{margin:12px 0 !important;padding:14px !important;background:#F4F6FA !important;border-radius:10px !important;border:1px solid #E2E0DC !important}
.suml-komponen-list{display:flex !important;flex-direction:column;gap:6px}
.suml-komponen-row{display:flex !important;align-items:center;gap:8px;padding:6px 10px !important;background:#fff !important;border-radius:8px !important;border:1px solid #E2E0DC !important;flex-wrap:wrap}
.komponen-nama{font-size:12px !important;font-weight:600 !important;color:#1C1917 !important;min-width:140px !important;flex-shrink:0}

/* ── Special / Double ── */
.suml-special-section{margin:12px 0 !important;padding:14px !important;background:#FFFBF0 !important;border-radius:10px !important;border:1px solid rgba(212,160,23,.15) !important}
.suml-divider{height:1px !important;background:#E2E0DC !important;margin:14px 0 !important}

/* ── File Upload ── */
.suml-file-upload{position:relative !important;display:flex !important;align-items:center;gap:10px}
.suml-file-input{position:absolute !important;width:100% !important;height:100% !important;opacity:0 !important;cursor:pointer !important;z-index:2}
.suml-file-text{font-size:12px !important;color:#A8A29E !important;padding:10px 14px !important;border:1.5px dashed #E2E0DC !important;border-radius:10px !important;width:100% !important;transition:all .2s;display:block !important;box-sizing:border-box}
.suml-file-text.has-file{color:#1A8A7D !important;border-color:#2AA198 !important;border-style:solid !important;font-weight:600 !important}

/* ── Mobile ── */
@media(max-width:768px){
  .suml-selector-row{flex-direction:column !important}
  .suml-card-body > .suml-row{flex-direction:column !important}
  .suml-card-body > .suml-row > .suml-field{min-width:100% !important}
  .suml-komponen-row{flex-direction:column !important;align-items:stretch !important}
  .komponen-nama{min-width:auto !important}
}
</style>

<a href="dashboard.php" class="breadcrumb-link">← Kembali ke Dashboard</a>

<div class="page-header">
    <h1><?= t('form.page_title') ?: 'Ajukan Permohonan SKVI' ?></h1>
    <p><?= t('form.page_subtitle') ?: 'Lengkapi form berikut. Permohonan akan otomatis masuk ke antrean verifikasi Koordinator.' ?></p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <span class="alert-icon">⚠️</span>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<!-- Step Indicator -->
<div class="suml-step-indicator">
    <div class="suml-step">
        <div class="suml-step-num active">1</div>
        <span class="suml-step-label active">Isi Data</span>
    </div>
    <div class="suml-step-line done"></div>
    <div class="suml-step">
        <div class="suml-step-num active">2</div>
        <span class="suml-step-label active">SUML & Peralatan</span>
    </div>
    <div class="suml-step-line done"></div>
    <div class="suml-step">
        <div class="suml-step-num active">3</div>
        <span class="suml-step-label active">Kirim</span>
    </div>
</div>

<form method="post" enctype="multipart/form-data" id="permohonanForm">

    <!-- ═══ STEP 1: Data Umum ═══ -->
    <div class="card reveal" style="margin-bottom:20px;">
        <h3 style="margin:0 0 16px;font-size:16px;font-weight:700;color:var(--navy-800);">
            📋 Data Permohonan
        </h3>

        <div class="form-group">
            <label class="form-label">Jenis Permohonan <span style="color:var(--red-600)">*</span></label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="jenis_permohonan" value="baru" checked>
                    🆕 Baru
                </label>
                <label>
                    <input type="radio" name="jenis_permohonan" value="tambah">
                    ➕ Tambah Lingkup
                </label>
                <label>
                    <input type="radio" name="jenis_permohonan" value="kurang">
                    ➖ Kurangi Lingkup
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">UML</label>
            <input type="text" class="form-input" value="<?= htmlspecialchars($_SESSION['nama'] ?? '') ?>" readonly
                   style="background:var(--navy-50);">
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi / Keterangan Tambahan</label>
            <textarea name="deskripsi" class="form-input" rows="3"
                      placeholder="Jelaskan lingkup verifikasi yang diajukan..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Berkas Permohonan <span style="color:var(--red-600)">*</span></label>
            <div class="upload-zone" id="uploadZone">
                <div class="upload-icon">📄</div>
                <div class="upload-text">Klik untuk memilih berkas atau drag & drop</div>
                <div class="upload-hint">PDF, JPG, PNG, DOC — Maks 10MB. <strong>Wajib diunggah.</strong></div>
                <input type="file" id="file_permohonan" name="file_permohonan" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
            </div>
        </div>
    </div>

    <!-- ═══ STEP 2: SUML & Peralatan ═══ -->
    <div class="card reveal" style="margin-bottom:20px;">
        <h3 style="margin:0 0 16px;font-size:16px;font-weight:700;color:var(--navy-800);">
            🔧 SUML & Peralatan
        </h3>
        <p style="margin:0 0 16px;font-size:13px;color:var(--ink-600);">
            Pilih kategori dan nama SUML yang akan diajukan verifikasi. Anda dapat menambah multiple item.
        </p>

        <!-- Kategori & Nama SUML selector -->
        <div class="suml-selector-row">
            <div class="suml-field">
                <label class="suml-label">Kategori SUML <span class="req">*</span></label>
                <select id="sumlKategori" class="suml-select">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Panjang">Panjang</option>
                    <option value="Volume">Volume</option>
                    <option value="Massa">Massa</option>
                    <option value="Waktu">Waktu</option>
                    <option value="Listrik">Listrik</option>
                    <option value="Suhu">Suhu</option>
                    <option value="Pendukung">Pendukung</option>
                </select>
            </div>
            <div class="suml-field">
                <label class="suml-label">Nama SUML <span class="req">*</span></label>
                <select id="sumlNama" class="suml-select" disabled>
                    <option value="">-- Pilih Nama SUML --</option>
                </select>
            </div>
            <div class="suml-field" style="flex:0 0 auto;min-width:auto;">
                <label class="suml-label">&nbsp;</label>
                <button type="button" id="sumlTambahBtn" class="btn btn-primary btn-pill" style="white-space:nowrap;">
                    + Tambah
                </button>
            </div>
        </div>

        <!-- Dynamic SUML Cards -->
        <div id="sumlCardsContainer" class="suml-cards-container"></div>
    </div>

    <!-- ═══ Submit ═══ -->
    <div class="card reveal" style="padding:16px 20px;">
        <div class="flex-between">
            <a href="dashboard.php" class="btn btn-secondary">← Batal</a>
            <button type="submit" class="btn btn-primary btn-pill">🚀 Kirim Permohonan</button>
        </div>
    </div>
</form>

<!-- SUML Data (inline — no external file dependency) -->
<script>
window.SUML_DATA = {
    Panjang: [
        { nama: "Meter Kerja", tipe: "A", satuan_nominal: "m" },
        { nama: "Komparator Van Becker", tipe: "A", satuan_nominal: "m" },
        { nama: "Bourje", tipe: "B", satuan_nominal: "mm", terdiri_dari: ["0,5 dL","1 dL","2 dL","0,5 L","1 L","2 L","5 L","10 L","20 L"] },
        { nama: "Jangka Sorong", tipe: "A", satuan_nominal: "mm" },
        { nama: "Depth Tape", tipe: "A", satuan_nominal: "m" },
        { nama: "Ban Ukur", tipe: "A", satuan_nominal: "m" },
        { nama: "Tongkat Duga", tipe: "A", satuan_nominal: "m" },
        { nama: "Salib Ukur", tipe: "A", satuan_nominal: "cm" },
        { nama: "Lainnya", tipe: "A", satuan_nominal: "", isLainnya: true }
    ],
    Volume: [
        { nama: "Bejana Ukur 5.000 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Ukur 2.000 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Ukur 1.000 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Ukur 500 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Ukur 200 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Ukur 100 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Ukur 50 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Limpah", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"], nominal_opt: ["50 L","100 L","200 L"] },
        { nama: "Bejana Ukur 20 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Ukur 10 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Bejana Ukur 5 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I","II","III"] },
        { nama: "Master Meter", tipe: "A", satuan_nominal: "L/menit; m3/h", daya_baca_label: "Ketelitian", daya_baca_opt: ["0,2%","0,1%"] },
        { nama: "Lainnya", tipe: "A", satuan_nominal: "", isLainnya: true }
    ],
    Massa: [
        { nama: "Timbangan Elektronik Kap. ≥ 30 kg", tipe: "A", satuan_nominal: "kg", kelas_opt: ["I","II"] },
        { nama: "Timbangan Elektronik Kap. ≥ 6 kg", tipe: "A", satuan_nominal: "kg" },
        { nama: "Timbangan Elektronik Kap. ≥ 200 g", tipe: "A", satuan_nominal: "g" },
        { nama: "Neraca A", tipe: "A", satuan_nominal: "kg", nominal_val: "75" },
        { nama: "Neraca B", tipe: "A", satuan_nominal: "kg", nominal_val: "10" },
        { nama: "Neraca C", tipe: "A", satuan_nominal: "kg", nominal_val: "1" },
        { nama: "Neraca D", tipe: "A", satuan_nominal: "g", nominal_val: "50" },
        { nama: "Neraca E", tipe: "A", satuan_nominal: "g", nominal_val: "1" },
        { nama: "Anak Timbangan Kelas F2 (1 mg - 1 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["1 mg","2 mg","2* mg","5 mg","10 mg","20 mg","20* mg","50 mg","100 mg","200 mg","200* mg","500 mg","1 g","2 g","2* g","5 g","10 g","20 g","20* g","50 g","100 g","200 g","200* g","500 g","1 kg"] },
        { nama: "Anak Timbangan Kelas F2 (2 kg - 20 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["2 kg","2* kg","5 kg","10 kg","20 kg","20* kg"] },
        { nama: "Anak Timbangan Kelas M1 (1 mg - 20 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["1 mg","2 mg","2* mg","5 mg","10 mg","20 mg","20* mg","50 mg","100 mg","200 mg","200* mg","500 mg","1 g","2 g","2* g","5 g","10 g","20 g","20* g","50 g","100 g","200 g","200* g","500 g","1 kg","2 kg","2* kg","5 kg","10 kg","20 kg","20* kg"] },
        { nama: "Anak Timbangan Kelas M2 (100 mg - 20 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["100 mg","200 mg","200* mg","500 mg","1 kg","2 kg","2* kg","5 kg","10 kg","20 kg","20* kg"] },
        { nama: "Anak Timbangan Remidi Kelas M2 (5 g - 1 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["5 g","10 g","20 g","20* g","50 g","100 g","200 g","200* g","500 g","1 kg"] },
        { nama: "Anak Timbangan Kelas M2 Bidur (20 kg)", tipe: "A", satuan_nominal: "kg" },
        { nama: "Anak Timbangan Kelas M1 Dacin (110 kg)", tipe: "B", satuan_nominal: "", extra_tripod: true, terdiri_dari: ["5 kg + Pengait 5 kg","10 kg","20* kg","20 kg","25 kg","25* kg"] },
        { nama: "Lainnya", tipe: "A", satuan_nominal: "", isLainnya: true }
    ],
    Waktu: [
        { nama: "Stopwatch", tipe: "A", satuan_nominal: "" },
        { nama: "Lainnya", tipe: "A", satuan_nominal: "", isLainnya: true }
    ],
    Listrik: [
        { nama: "Standar Energy Electric Vehicle Supply Equipment", tipe: "A", satuan_nominal: "A" }
    ],
    Suhu: [
        { nama: "Thermometer Digital", tipe: "A", satuan_nominal: "oC" }
    ],
    Pendukung: [
        { nama: "Thermohygrometer", tipe: "A", satuan_nominal: "oC / % RH", double_kapasitas: true },
        { nama: "Thermocouple (Bejana 5000 L)", tipe: "A", satuan_nominal: "oC" },
        { nama: "Thermocouple (Bejana 1000 L)", tipe: "A", satuan_nominal: "oC" },
        { nama: "Thermocouple (Bejana 500 L)", tipe: "A", satuan_nominal: "oC" },
        { nama: "Pressure Transmitter/Pressure Gauge", tipe: "A", satuan_nominal: "kg/cm2", daya_baca_label: "Range" },
        { nama: "Temperature Transmitter/Temperature Gauge", tipe: "A", satuan_nominal: "kg/cm2", daya_baca_label: "Range" },
        { nama: "Flow Computer", tipe: "A", satuan_nominal: "", fields: ["Merek/Buatan","Model/Tipe","No. Seri"] },
        { nama: "Hydrometer/Densytometer", tipe: "A", satuan_nominal: "kg/m3 ; g/cm3", daya_baca_label: "Range" },
        { nama: "Thermometer Ruang", tipe: "A", satuan_nominal: "oC" },
        { nama: "Pressure Gauge/Manometer", tipe: "A", satuan_nominal: "kg/cm2", daya_baca_label: "Range" },
        { nama: "Rotameter", tipe: "A", satuan_nominal: "L/menit", daya_baca_label: "Laju Alir" },
        { nama: "Dehumidifier", tipe: "A", satuan_nominal: "W", fields: ["Compressor","Air Flow Rate (m3/menit)","Dehidrasi (L/Jam)"] },
        { nama: "AC", tipe: "A", satuan_nominal: "kCal/h", fields: ["Cooling Capacity (kCal)"] },
        { nama: "Lainnya", tipe: "A", satuan_nominal: "", isLainnya: true }
    ]
};
</script>
<!-- Backup: inline dropdown cascade (in case suml-form.js fails to load) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var k = document.getElementById('sumlKategori');
    var n = document.getElementById('sumlNama');
    var b = document.getElementById('sumlTambahBtn');
    if (!k || !n) return;
    k.addEventListener('change', function() {
        var v = this.value;
        n.innerHTML = '<option value="">-- Pilih Nama SUML --</option>';
        if (!v || !window.SUML_DATA || !window.SUML_DATA[v]) return;
        window.SUML_DATA[v].forEach(function(item, i) {
            var o = document.createElement('option');
            o.value = i; o.textContent = item.nama;
            n.appendChild(o);
        });
        n.disabled = false;
    });
});
</script>
<script src="assets/js/suml-form.js"></script>

<?php require 'includes/footer.php'; ?>
