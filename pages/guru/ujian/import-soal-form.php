<?php
session_start();
include '../../../config/database.php';

$id_guru = $_SESSION['id_guru'] ?? null;
if (!$id_guru) {
    echo '<div class="alert alert-danger">Akses ditolak.</div>';
    exit;
}

$id_ujian = isset($_GET['id_ujian']) ? (int) $_GET['id_ujian'] : 0;
if ($id_ujian <= 0) {
    echo '<div class="alert alert-danger">Ujian tidak valid.</div>';
    exit;
}

$id_guru_esc = mysqli_real_escape_string($kon, (string) $id_guru);
$id_ujian_esc = mysqli_real_escape_string($kon, (string) $id_ujian);
$q = mysqli_query($kon, "SELECT id_ujian, kode_ujian, tipe_soal FROM ujian WHERE id_ujian='$id_ujian_esc' AND id_guru='$id_guru_esc' LIMIT 1");
$row = mysqli_fetch_assoc($q);
if (!$row) {
    echo '<div class="alert alert-danger">Ujian tidak ditemukan.</div>';
    exit;
}
if ((int) $row['tipe_soal'] !== 1) {
    echo '<div class="alert alert-warning">Import ini hanya untuk ujian <strong>Pilihan Ganda</strong>.</div>';
    exit;
}
?>
<form id="form_import_soal_pg" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id_ujian" value="<?php echo (int) $row['id_ujian']; ?>">
    <div class="form-group">
        <label>File soal</label>
        <input type="file" name="bank_soal" class="form-control-file" accept=".txt,.doc,.docx" required>
        <small class="form-text text-muted">
            <strong>.docx</strong> atau <strong>.txt</strong> (disarankan). Untuk .docx: PHP <code>zip</code> (ZipArchive) atau <code>unzip</code>. File <strong>.doc</strong> (Word lama): server akan memakai <strong>LibreOffice</strong> (<code>soffice</code>/<code>libreoffice</code>), lalu <strong>Pandoc</strong>, lalu <code>antiword</code>/<code>catdoc</code> — pasang salah satu (umumnya <code>sudo apt install libreoffice-writer</code>).
        </small>
    </div>
    <div class="form-group">
        <label>Jika sudah pernah mengimpor</label>
        <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" name="import_mode" id="imp_tambah" value="tambah" checked>
            <label class="custom-control-label" for="imp_tambah">Tambah di akhir</label>
        </div>
        <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" name="import_mode" id="imp_skip" value="skip_sama">
            <label class="custom-control-label" for="imp_skip">Lewati soal yang <strong>sama persis</strong> (teks soal, lima pilihan, dan kunci)</label>
        </div>
        <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" name="import_mode" id="imp_timpa" value="timpa">
            <label class="custom-control-label" for="imp_timpa">Ganti semua soal PG ujian ini</label>
        </div>
        <small class="form-text text-muted">Mode <strong>ganti semua</strong> menghapus soal PG beserta jawaban di bank, dan riwayat jawaban siswa (<code>hasil</code>) untuk soal-soal itu. Nilai/riwayat ujian tetap; siswa perlu mengerjakan ulang soal yang baru.</small>
    </div>
    <div class="alert alert-light border small">
            Format tiap soal:<br>
        <code>1. Teks soal…</code> atau <code>1) …</code> (nomor daftar Word otomatis dideteksi di .docx)<br>
        Pilihan <code>a.</code>–<code>e.</code> dan <code>Jawaban</code> <strong>opsional</strong> — yang kosong tidak disimpan; tanpa kunci, penilaian otomatis menganggap semua salah.<br>
        <code>Jawaban : a</code>, <code>Jawaban: a</code>, atau <code>Jawaban a. teks</code> (tanpa titik dua, seperti Word). Di akhir file: <code>Jawaban 1: a</code> …<br>
        <span class="text-info">Jika tidak semua soal terimpor, setelah klik Import akan muncul <strong>rincian</strong> berisi contoh blok yang ditolak dan alasannya.</span>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-file-upload"></i> Import</button>
    <span id="import_soal_status" class="ml-2 text-muted"></span>
</form>
<script>
(function () {
    function teksLaporanImpor(L) {
        if (!L) return '';
        var bukan = (L.gagal_bukan_soal_jumlah || 0);
        var per = (L.peringatan_tidak_lengkap_jumlah || 0);
        if (bukan === 0 && per === 0) return '';
        var lines = [];
        lines.push('——— Rincian pemindaian file ———');
        lines.push('Blok terdeteksi: ' + L.blok_terdeteksi + ' | Diimpor: ' + L.lolos_parser);
        if (bukan > 0) {
            lines.push('Tidak dianggap blok soal: ' + bukan + ' (awal baris bukan N. / N)).');
        }
        if (per > 0) {
            lines.push('Peringatan (tetap diimpor): ' + per + ' soal tanpa pilihan/kunci lengkap.');
        }
        var contohVal = L.contoh_peringatan || L.contoh_gagal_validasi;
        if (contohVal && contohVal.length) {
            lines.push('');
            lines.push('Contoh peringatan (nomor = di file Anda):');
            contohVal.slice(0, 10).forEach(function (x, i) {
                lines.push((i + 1) + ') Soal #' + x.nomor_di_file + ': ' + x.alasan);
                if (x.cuplikan_soal) lines.push('   «' + x.cuplikan_soal + '…»');
            });
        }
        if (L.contoh_bukan_soal && L.contoh_bukan_soal.length) {
            lines.push('');
            lines.push('Contoh teks yang tidak dianggap awal soal (gabung paragraf / tanpa "N."):');
            L.contoh_bukan_soal.slice(0, 6).forEach(function (x, i) {
                lines.push((i + 1) + ') ' + x.alasan);
                if (x.cuplikan) lines.push('   «' + x.cuplikan + '…»');
            });
        }
        if (L.petunjuk) {
            lines.push('');
            lines.push(L.petunjuk);
        }
        var t = lines.join('\n');
        if (t.length > 3800) t = t.slice(0, 3800) + '\n…(potong)';
        return '\n\n' + t;
    }
    $('#form_import_soal_pg').off('submit').on('submit', function (e) {
        e.preventDefault();
        var mode = $(this).find('input[name="import_mode"]:checked').val() || 'tambah';
        if (mode === 'timpa') {
            if (!confirm('Semua soal pilihan ganda di ujian ini akan dihapus dan diganti dari file. Jawaban siswa pada soal lama ikut terhapus. Lanjutkan?')) {
                return;
            }
        }
        var $st = $('#import_soal_status');
        $st.text('Mengunggah…');
        var fd = new FormData(this);
        $.ajax({
            url: 'pages/guru/ujian/import-soal-proses.php',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res && res.ok) {
                    $st.text('');
                    var msg = 'Berhasil mengimpor ' + res.imported + ' soal.';
                    if (res.skipped > 0) {
                        msg += ' (' + res.skipped + ' dilewati karena identik dengan yang sudah ada.)';
                    }
                    if (res.deleted_soal > 0) {
                        msg += ' Soal lama dihapus: ' + res.deleted_soal + '.';
                    }
                    msg += teksLaporanImpor(res.laporan);
                    alert(msg);
                    $('#modal').modal('hide');
                    if (typeof tabel_ujian === 'function') tabel_ujian();
                } else {
                    $st.text('');
                    alert((res.message || 'Import gagal.') + teksLaporanImpor(res.laporan));
                }
            },
            error: function (xhr) {
                $st.text('');
                var msg = 'Import gagal.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message + teksLaporanImpor(xhr.responseJSON.laporan);
                } else {
                    try {
                        var j = JSON.parse(xhr.responseText);
                        if (j.message) msg = j.message;
                    } catch (err) {
                        if (xhr.status === 0) {
                            msg = 'Tidak terhubung ke server.';
                        } else if (xhr.status >= 500) {
                            msg = 'Error server (' + xhr.status + '). Periksa log PHP.';
                        } else if (xhr.responseText && xhr.responseText.length < 500) {
                            msg += ' ' + xhr.responseText.replace(/<[^>]+>/g, ' ').trim().slice(0, 200);
                        }
                    }
                }
                alert(msg);
            }
        });
    });
})();
</script>
