/**
 * SUML Form — Dynamic form logic for permohonan_baru.php
 * Handles: dropdown cascading, card rendering, Type A/B, file uploads
 * Includes inline CSS injection as fallback for hosting compatibility
 */

(function() {
    'use strict';

    var cardCounter = 0;

    // ── Init on DOM ready ──
    document.addEventListener('DOMContentLoaded', function() {
        var kategoriSelect = document.getElementById('sumlKategori');
        var namaSelect = document.getElementById('sumlNama');
        var tambahBtn = document.getElementById('sumlTambahBtn');

        if (!kategoriSelect || !namaSelect || !tambahBtn) return;

        // Kategori change → populate Nama SUML
        kategoriSelect.addEventListener('change', function() {
            var kategori = this.value;
            namaSelect.innerHTML = '<option value="">-- Pilih Nama SUML --</option>';
            if (!kategori || !window.SUML_DATA[kategori]) return;

            window.SUML_DATA[kategori].forEach(function(item, idx) {
                var opt = document.createElement('option');
                opt.value = idx;
                opt.textContent = item.nama;
                namaSelect.appendChild(opt);
            });
            namaSelect.disabled = false;
        });

        // Tambah button → create card
        tambahBtn.addEventListener('click', function() {
            var kategori = kategoriSelect.value;
            var namaIdx = namaSelect.value;

            if (!kategori || namaIdx === '') {
                alert('Pilih Kategori dan Nama SUML terlebih dahulu.');
                return;
            }

            var item = window.SUML_DATA[kategori][parseInt(namaIdx)];
            if (!item) return;

            addSumlCard(kategori, item);
        });
    });

    // ── Add SUML Card ──
    function addSumlCard(kategori, item) {
        cardCounter++;
        var cardId = 'suml-card-' + cardCounter;
        var prefix = 'suml[' + cardCounter + ']';

        var container = document.getElementById('sumlCardsContainer');
        if (!container) return;

        container.className = 'suml-cards-container has-cards';

        var card = document.createElement('div');
        card.className = 'suml-item-card';
        card.id = cardId;
        card.setAttribute('data-kategori', kategori);
        card.setAttribute('data-nama', item.nama);
        card.setAttribute('data-tipe', item.tipe);

        var html = '';

        // Card header
        html += '<div class="suml-card-header">';
        html += '<h4 class="suml-card-title">' + escHtml(item.nama) + '</h4>';
        html += '<span class="suml-card-badge">' + escHtml(kategori) + ' · Tipe ' + escHtml(item.tipe) + '</span>';
        html += '<button type="button" class="suml-card-remove" onclick="removeSumlCard(\'' + cardId + '\')" title="Hapus">&times;</button>';
        html += '</div>';

        html += '<div class="suml-card-body">';

        // ── Hidden fields ──
        html += '<input type="hidden" name="' + prefix + '[kategori]" value="' + escAttr(kategori) + '">';
        html += '<input type="hidden" name="' + prefix + '[nama_suml]" value="' + escAttr(item.nama) + '">';
        html += '<input type="hidden" name="' + prefix + '[tipe]" value="' + escAttr(item.tipe) + '">';

        // ── Jumlah Unit ──
        html += '<div class="suml-row">';
        html += '<div class="suml-field">';
        html += '<label class="suml-label">Jumlah (Unit) <span class="req">*</span></label>';
        html += '<input type="number" name="' + prefix + '[jumlah_unit]" min="1" required class="suml-input">';
        html += '</div>';

        // ── Kapasitas / Nominal ──
        if (item.isLainnya) {
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Nama SUML Lainnya <span class="req">*</span></label>';
            html += '<input type="text" name="' + prefix + '[lainnya_nama]" placeholder="Sebutkan nama SUML..." class="suml-input">';
            html += '</div>';
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Satuan</label>';
            html += '<input type="text" name="' + prefix + '[kapasitas_nominal]" placeholder="Satuan ukur" class="suml-input">';
            html += '</div>';
        } else if (item.nominal_opt) {
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Kapasitas / Nominal <span class="req">*</span></label>';
            html += '<select name="' + prefix + '[kapasitas_nominal]" class="suml-select" required>';
            html += '<option value="">Pilih</option>';
            item.nominal_opt.forEach(function(n) {
                html += '<option value="' + escAttr(n) + '">' + escHtml(n) + '</option>';
            });
            html += '</select>';
            html += '</div>';
        } else if (item.satuan_nominal) {
            var nominalVal = item.nominal_val ? item.nominal_val + ' ' + item.satuan_nominal : item.satuan_nominal;
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Kapasitas / Nominal</label>';
            html += '<input type="text" name="' + prefix + '[kapasitas_nominal]" value="' + escAttr(nominalVal) + '" readonly class="suml-input suml-readonly">';
            html += '</div>';
        }

        html += '</div>'; // end suml-row

        // ── Kelas ──
        if (item.kelas_opt) {
            html += '<div class="suml-row">';
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Kelas <span class="req">*</span></label>';
            html += '<select name="' + prefix + '[kelas]" class="suml-select" required>';
            html += '<option value="">Pilih Kelas</option>';
            item.kelas_opt.forEach(function(k) {
                html += '<option value="' + escAttr(k) + '">' + escHtml(k) + '</option>';
            });
            html += '</select>';
            html += '</div>';
            html += '</div>';
        }

        // ── Daya Baca ──
        var dbLabel = item.daya_baca_label || 'Daya Baca';
        if (item.daya_baca_opt) {
            html += '<div class="suml-row">';
            html += '<div class="suml-field">';
            html += '<label class="suml-label">' + escHtml(dbLabel) + ' <span class="req">*</span></label>';
            html += '<select name="' + prefix + '[daya_baca]" class="suml-select" required>';
            html += '<option value="">Pilih</option>';
            item.daya_baca_opt.forEach(function(d) {
                html += '<option value="' + escAttr(d) + '">' + escHtml(d) + '</option>';
            });
            html += '</select>';
            html += '</div>';
            html += '</div>';
        } else if (item.satuan_nominal || item.tipe === 'A') {
            html += '<div class="suml-row">';
            html += '<div class="suml-field">';
            html += '<label class="suml-label">' + escHtml(dbLabel) + ' <span class="req">*</span></label>';
            html += '<input type="text" name="' + prefix + '[daya_baca]" placeholder="' + escAttr(dbLabel) + '" class="suml-input" required>';
            html += '</div>';
            html += '</div>';
        }

        // ── Type B: Komponen ──
        if (item.tipe === 'B' && item.terdiri_dari && item.terdiri_dari.length > 0) {
            html += '<div class="suml-komponen-section">';
            html += '<label class="suml-label suml-label-section">Komponen (Terdiri dari)</label>';
            html += '<div class="suml-komponen-list">';

            item.terdiri_dari.forEach(function(komponen, kIdx) {
                var kPrefix = prefix + '[komponen][' + kIdx + ']';
                html += '<div class="suml-komponen-row">';
                html += '<input type="hidden" name="' + kPrefix + '[nama]" value="' + escAttr(komponen) + '">';
                html += '<span class="komponen-nama">' + escHtml(komponen) + '</span>';
                html += '<input type="number" name="' + kPrefix + '[jumlah]" min="0" placeholder="Jumlah" class="suml-input suml-input-sm">';
                html += '<input type="text" name="' + kPrefix + '[daya_baca]" placeholder="Daya Baca" class="suml-input suml-input-sm">';
                html += '</div>';
            });

            html += '</div>';

            if (item.extra_tripod) {
                html += '<div class="suml-row" style="margin-top:8px;">';
                html += '<div class="suml-field">';
                html += '<label class="suml-label">Tripod <span class="req">*</span></label>';
                html += '<input type="number" name="' + prefix + '[extra_tripod]" min="0" placeholder="Jumlah Tripod" class="suml-input">';
                html += '</div>';
                html += '</div>';
            }

            html += '</div>';
        }

        // ── Special fields ──
        if (item.fields && item.fields.length > 0) {
            html += '<div class="suml-special-section">';
            html += '<label class="suml-label suml-label-section">Informasi Tambahan</label>';
            item.fields.forEach(function(f) {
                var fKey = f.toLowerCase().replace(/[^a-z0-9]/g, '_');
                html += '<div class="suml-row">';
                html += '<div class="suml-field">';
                html += '<label class="suml-label">' + escHtml(f) + '</label>';
                html += '<input type="text" name="' + prefix + '[custom][' + escAttr(fKey) + ']" placeholder="' + escAttr(f) + '" class="suml-input">';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
        }

        // ── Double kapasitas ──
        if (item.double_kapasitas) {
            html += '<div class="suml-special-section">';
            html += '<label class="suml-label suml-label-section">Kapasitas Ganda</label>';
            html += '<div class="suml-row">';
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Kapasitas Suhu (°C) <span class="req">*</span></label>';
            html += '<input type="text" name="' + prefix + '[custom][kapasitas_suhu]" placeholder="Contoh: -10 s/d 50" class="suml-input">';
            html += '</div>';
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Kapasitas Kelembapan (% RH) <span class="req">*</span></label>';
            html += '<input type="text" name="' + prefix + '[custom][kapasitas_kelembapan]" placeholder="Contoh: 0 s/d 100" class="suml-input">';
            html += '</div>';
            html += '</div>';
            html += '<div class="suml-row">';
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Daya Baca Suhu (°C) <span class="req">*</span></label>';
            html += '<input type="text" name="' + prefix + '[custom][daya_baca_suhu]" class="suml-input">';
            html += '</div>';
            html += '<div class="suml-field">';
            html += '<label class="suml-label">Daya Baca Kelembapan (% RH) <span class="req">*</span></label>';
            html += '<input type="text" name="' + prefix + '[custom][daya_baca_kelembapan]" class="suml-input">';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        // ── Common fields ──
        html += '<div class="suml-divider"></div>';
        html += '<div class="suml-row">';
        html += '<div class="suml-field">';
        html += '<label class="suml-label">Kepemilikan <span class="req">*</span></label>';
        html += '<select name="' + prefix + '[kepemilikan]" class="suml-select" required>';
        html += '<option value="">Pilih</option>';
        html += '<option value="sendiri">Sendiri</option>';
        html += '<option value="kso">KSO</option>';
        html += '</select>';
        html += '</div>';
        html += '<div class="suml-field">';
        html += '<label class="suml-label">Jenis Verifikasi <span class="req">*</span></label>';
        html += '<select name="' + prefix + '[jenis_verifikasi]" class="suml-select" required>';
        html += '<option value="">Pilih</option>';
        html += '<option value="eksternal">Eksternal</option>';
        html += '<option value="internal">Internal</option>';
        html += '</select>';
        html += '</div>';
        html += '</div>';

        html += '<div class="suml-row">';
        html += '<div class="suml-field">';
        html += '<label class="suml-label">Verifikasi Terakhir <span class="req">*</span></label>';
        html += '<input type="date" name="' + prefix + '[verif_terakhir]" class="suml-input" required>';
        html += '</div>';
        html += '<div class="suml-field">';
        html += '<label class="suml-label">Verifikasi Mendatang <span class="req">*</span></label>';
        html += '<input type="date" name="' + prefix + '[verif_mendatang]" class="suml-input" required>';
        html += '</div>';
        html += '</div>';

        // ── Lampiran SKHP ──
        html += '<div class="suml-row">';
        html += '<div class="suml-field suml-field-full">';
        html += '<label class="suml-label">Lampiran SKHP <span class="req">*</span></label>';
        html += '<div class="suml-file-upload">';
        html += '<input type="file" name="' + prefix + '[lampiran_skhp]" accept=".pdf,.jpg,.jpeg,.png" class="suml-file-input" required>';
        html += '<span class="suml-file-text">Pilih File (PDF/JPG/PNG, maks 10MB)</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        html += '</div>'; // end card-body

        card.innerHTML = html;
        container.appendChild(card);

        card.scrollIntoView({ behavior: 'smooth', block: 'center' });

        setupFileUpload(card);

        document.getElementById('sumlNama').value = '';
    }

    // ── Remove SUML Card ──
    window.removeSumlCard = function(cardId) {
        var card = document.getElementById(cardId);
        if (!card) return;
        if (!confirm('Hapus item SUML ini?')) return;
        card.remove();

        var container = document.getElementById('sumlCardsContainer');
        if (container && container.querySelectorAll('.suml-item-card').length === 0) {
            container.className = 'suml-cards-container';
        }
    };

    // ── File upload visual feedback ──
    function setupFileUpload(card) {
        card.querySelectorAll('.suml-file-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var text = this.closest('.suml-file-upload').querySelector('.suml-file-text');
                if (this.files.length > 0) {
                    text.textContent = this.files[0].name;
                    text.classList.add('has-file');
                } else {
                    text.textContent = 'Pilih File (PDF/JPG/PNG, maks 10MB)';
                    text.classList.remove('has-file');
                }
            });
        });
    }

    // ── HTML escapers ──
    function escHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    function escAttr(str) {
        return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

})();
