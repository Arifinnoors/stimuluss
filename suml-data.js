/**
 * SUML Data — Data Standar Ukuran Metrologi Legal
 * Sumber: SIPUMETAL (metrologi.kemendag.go.id)
 * 7 Kategori, ~70+ item SUML
 */

window.SUML_DATA = {
    Panjang: [
        { nama: "Meter Kerja", tipe: "A", satuan_nominal: "m" },
        { nama: "Komparator Van Becker", tipe: "A", satuan_nominal: "m" },
        { nama: "Bourje", tipe: "B", satuan_nominal: "mm", terdiri_dari: ["0,5 dL", "1 dL", "2 dL", "0,5 L", "1 L", "2 L", "5 L", "10 L", "20 L"] },
        { nama: "Jangka Sorong", tipe: "A", satuan_nominal: "mm" },
        { nama: "Depth Tape", tipe: "A", satuan_nominal: "m" },
        { nama: "Ban Ukur", tipe: "A", satuan_nominal: "m" },
        { nama: "Tongkat Duga", tipe: "A", satuan_nominal: "m" },
        { nama: "Salib Ukur", tipe: "A", satuan_nominal: "cm" },
        { nama: "Lainnya", tipe: "A", satuan_nominal: "", isLainnya: true }
    ],
    Volume: [
        { nama: "Bejana Ukur 5.000 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Bejana Ukur 2.000 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Bejana Ukur 1.000 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Bejana Ukur 500 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Bejana Ukur 200 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Bejana Ukur 100 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Bejana Ukur 50 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Bejana Limpah", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"], nominal_opt: ["50 L", "100 L", "200 L"] },
        { nama: "Bejana Ukur 20 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"], extra_landasan: true },
        { nama: "Bejana Ukur 10 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Bejana Ukur 5 L", tipe: "A", satuan_nominal: "L", kelas_opt: ["I", "II", "III"] },
        { nama: "Master Meter", tipe: "A", satuan_nominal: "L/menit; m3/h", daya_baca_label: "Ketelitian", daya_baca_opt: ["0,2%", "0,1%"] },
        { nama: "Lainnya", tipe: "A", satuan_nominal: "", isLainnya: true }
    ],
    Massa: [
        { nama: "Timbangan Elektronik Kap. ≥ 30 kg", tipe: "A", satuan_nominal: "kg", kelas_opt: ["I", "II"] },
        { nama: "Timbangan Elektronik Kap. ≥ 6 kg", tipe: "A", satuan_nominal: "kg" },
        { nama: "Timbangan Elektronik Kap. ≥ 200 g", tipe: "A", satuan_nominal: "g" },
        { nama: "Neraca A", tipe: "A", satuan_nominal: "kg", nominal_val: "75" },
        { nama: "Neraca B", tipe: "A", satuan_nominal: "kg", nominal_val: "10" },
        { nama: "Neraca C", tipe: "A", satuan_nominal: "kg", nominal_val: "1" },
        { nama: "Neraca D", tipe: "A", satuan_nominal: "g", nominal_val: "50" },
        { nama: "Neraca E", tipe: "A", satuan_nominal: "g", nominal_val: "1" },
        { nama: "Anak Timbangan Kelas F2 (1 mg - 1 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["1 mg", "2 mg", "2* mg", "5 mg", "10 mg", "20 mg", "20* mg", "50 mg", "100 mg", "200 mg", "200* mg", "500 mg", "1 g", "2 g", "2* g", "5 g", "10 g", "20 g", "20* g", "50 g", "100 g", "200 g", "200* g", "500 g", "1 kg"] },
        { nama: "Anak Timbangan Kelas F2 (2 kg - 20 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["2 kg", "2* kg", "5 kg", "10 kg", "20 kg", "20* kg"] },
        { nama: "Anak Timbangan Kelas M1 (1 mg - 20 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["1 mg", "2 mg", "2* mg", "5 mg", "10 mg", "20 mg", "20* mg", "50 mg", "100 mg", "200 mg", "200* mg", "500 mg", "1 g", "2 g", "2* g", "5 g", "10 g", "20 g", "20* g", "50 g", "100 g", "200 g", "200* g", "500 g", "1 kg", "2 kg", "2* kg", "5 kg", "10 kg", "20 kg", "20* kg"] },
        { nama: "Anak Timbangan Kelas M2 (100 mg - 20 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["100 mg", "200 mg", "200* mg", "500 mg", "1 kg", "2 kg", "2* kg", "5 kg", "10 kg", "20 kg", "20* kg"] },
        { nama: "Anak Timbangan Remidi Kelas M2 (5 g - 1 kg)", tipe: "B", satuan_nominal: "", terdiri_dari: ["5 g", "10 g", "20 g", "20* g", "50 g", "100 g", "200 g", "200* g", "500 g", "1 kg"] },
        { nama: "Anak Timbangan Kelas M2 Bidur (20 kg)", tipe: "A", satuan_nominal: "kg" },
        { nama: "Anak Timbangan Kelas M1 Dacin (110 kg)", tipe: "B", satuan_nominal: "", extra_tripod: true, terdiri_dari: ["5 kg + Pengait 5 kg", "10 kg", "20* kg", "20 kg", "25 kg", "25* kg"] },
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
        { nama: "Flow Computer", tipe: "A", satuan_nominal: "", fields: ["Merek/Buatan", "Model/Tipe", "No. Seri"] },
        { nama: "Hydrometer/Densytometer", tipe: "A", satuan_nominal: "kg/m3 ; g/cm3", daya_baca_label: "Range" },
        { nama: "Thermometer Ruang", tipe: "A", satuan_nominal: "oC" },
        { nama: "Pressure Gauge/Manometer", tipe: "A", satuan_nominal: "kg/cm2", daya_baca_label: "Range" },
        { nama: "Rotameter", tipe: "A", satuan_nominal: "L/menit", daya_baca_label: "Laju Alir" },
        { nama: "Dehumidifier", tipe: "A", satuan_nominal: "W", fields: ["Compressor", "Air Flow Rate (m3/menit)", "Dehidrasi (L/Jam)"], upload_foto: true },
        { nama: "AC", tipe: "A", satuan_nominal: "kCal/h", fields: ["Cooling Capacity (kCal)"], upload_foto: true },
        { nama: "Lainnya", tipe: "A", satuan_nominal: "", isLainnya: true }
    ]
};
