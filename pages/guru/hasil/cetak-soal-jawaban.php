<?php
session_start();
include '../../../config/database.php';

$username = $_SESSION['username'];
$cek_guru = mysqli_query($kon, "select * from guru where username='".addslashes($username)."' limit 1");
if (mysqli_num_rows($cek_guru) < 1) {
    die("<center><h5>Tidak memiliki hak akses</h5></center>");
}

$id_ujian = addslashes(trim($_GET['id_ujian']));
$id_siswa = addslashes(trim($_GET['id_siswa']));

$query_app = mysqli_query($kon, "select * from aplikasi limit 1");
$row_app   = mysqli_fetch_array($query_app);

$sql = "select s.*, k.nama_kelas, u.judul, u.tipe_soal, u.tanggal, u.nilai_kelulusan,
               u.jam, u.waktu, u.kode_ujian, m.nama_mapel, g.nama_guru
        from siswa s
        inner join kelas k on k.id_kelas = s.id_kelas
        inner join ujian u on u.id_kelas = k.id_kelas
        inner join mapel m on m.id_mapel = u.id_mapel
        inner join guru g on g.id_guru = u.id_guru
        where u.id_ujian = '$id_ujian' and s.id_siswa = '$id_siswa'
        limit 1";
$hasil = mysqli_query($kon, $sql);
if (mysqli_num_rows($hasil) <= 0) die("<center><h5>Data tidak ditemukan</h5></center>");
$data = mysqli_fetch_array($hasil);

$hasil_nilai = mysqli_query($kon, "select * from nilai where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
$data_nilai  = mysqli_fetch_array($hasil_nilai);
$nilai           = isset($data_nilai['nilai']) ? $data_nilai['nilai'] : 0;
$nilai_kelulusan = $data['nilai_kelulusan'];
$status          = ($nilai >= $nilai_kelulusan) ? 'Kompeten' : 'Belum Kompeten';
$is_pg           = ($data['tipe_soal'] == 1);

// Hanya soal yang dikerjakan siswa ini (subset acak 50 soal dari bank)
$sql_soal   = "select distinct s.* from soal s
               inner join hasil h on h.id_soal = s.id_soal and h.id_ujian = s.id_ujian
               where s.id_ujian='$id_ujian' and h.id_siswa='$id_siswa'
               order by s.id_soal asc";
$hasil_soal = mysqli_query($kon, $sql_soal);

$jumlah_soal  = mysqli_num_rows($hasil_soal);
$jumlah_benar = 0;

// Pre-fetch semua hasil siswa untuk efisiensi
$map_hasil = [];
$r_all = mysqli_query($kon, "select * from hasil where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
while ($rh = mysqli_fetch_array($r_all)) {
    $map_hasil[$rh['id_soal']] = $rh;
}

function tgl($t) {
    $b = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus',
          'September','Oktober','November','Desember'];
    $s = explode('-', $t);
    return $s[2].' '.$b[(int)$s[1]].' '.$s[0];
}

$letters = ['A','B','C','D','E','F'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Soal &amp; Jawaban - <?php echo htmlspecialchars($data['nama_siswa']); ?></title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 11pt; color: #222; background: #fff; }

.wrapper { max-width: 780px; margin: 0 auto; padding: 20px 30px; }

/* Header */
.header { display: flex; align-items: center; border-bottom: 3px solid #14286e; padding-bottom: 8px; margin-bottom: 4px; }
.header img { width: 60px; height: 60px; object-fit: contain; margin-right: 14px; }
.header-text { flex: 1; text-align: center; }
.header-text h2 { font-size: 14pt; color: #14286e; text-transform: uppercase; line-height: 1.3; }
.header-text p { font-size: 9pt; color: #555; }
.sub-line { border-bottom: 1px solid #14286e; margin-bottom: 14px; }

/* Title */
.doc-title { text-align: center; font-size: 14pt; font-weight: bold; color: #14286e; margin: 10px 0 4px; text-transform: uppercase; letter-spacing: 1px; }
.doc-subtitle { text-align: center; font-size: 9pt; color: #666; margin-bottom: 14px; }

/* Info table */
.info-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 10pt; }
.info-table td { padding: 3px 5px; vertical-align: top; }
.info-table td:first-child { width: 120px; font-weight: bold; color: #333; }
.info-table td:nth-child(2) { width: 10px; }
.info-table .section-header td { background: #14286e; color: #fff; font-weight: bold; padding: 5px 8px; }

/* Divider */
hr.section { border: none; border-top: 1px solid #ccc; margin: 12px 0; }

/* Question block */
.question-block { margin-bottom: 16px; page-break-inside: avoid; }
.question-num { font-weight: bold; color: #14286e; margin-bottom: 3px; font-size: 10.5pt; }
.question-text { margin-left: 18px; margin-bottom: 6px; line-height: 1.5; }
.question-img { margin-left: 18px; margin-bottom: 6px; }
.question-img img { max-width: 280px; max-height: 180px; }

/* Options (PG) */
.options { margin-left: 28px; }
.option-row { display: flex; align-items: flex-start; padding: 3px 6px; border-radius: 3px; margin-bottom: 2px; line-height: 1.4; }
.option-letter { font-weight: bold; min-width: 22px; }
.option-text { flex: 1; }
.option-correct { background: #e6f4ea; color: #1a7a34; }
.option-correct .option-letter { color: #1a7a34; }
.option-wrong { background: #fdecea; color: #c62828; }
.option-wrong .option-letter { color: #c62828; }
.badge { font-size: 8.5pt; padding: 1px 6px; border-radius: 10px; margin-left: 6px; white-space: nowrap; }
.badge-correct { background: #1a7a34; color: #fff; }
.badge-chosen { background: #c62828; color: #fff; }
.badge-both { background: #1a7a34; color: #fff; }

/* Essay answer */
.essay-box { margin-left: 18px; background: #f5f5f5; border-left: 3px solid #14286e; padding: 7px 10px; font-style: italic; color: #333; line-height: 1.5; border-radius: 2px; }
.essay-label { margin-left: 18px; font-weight: bold; font-size: 9.5pt; color: #14286e; margin-bottom: 3px; }

/* Summary */
.summary-box { border: 2px solid #14286e; border-radius: 4px; padding: 10px 16px; margin: 16px 0; display: flex; gap: 30px; font-size: 10.5pt; }
.summary-item { display: flex; gap: 6px; }
.summary-item .lbl { font-weight: bold; color: #333; }
.summary-item .val { color: #14286e; font-weight: bold; }
.status-kompeten { color: #1a7a34; font-weight: bold; }
.status-belum    { color: #c62828; font-weight: bold; }

/* Print button */
.print-btn { display: inline-block; margin: 0 0 18px; padding: 8px 20px; background: #14286e; color: #fff; border: none; border-radius: 4px; font-size: 10pt; cursor: pointer; }
.print-btn:hover { background: #1e3a9a; }

@media print {
    .no-print { display: none !important; }
    .wrapper { padding: 0; }
    body { font-size: 10pt; }
}
</style>
</head>
<body>
<div class="wrapper">

    <!-- Print Button -->
    <div class="no-print" style="margin-bottom:12px;">
        <button class="print-btn" onclick="window.print()">&#128438; Cetak / Print</button>
    </div>

    <!-- Header -->
    <div class="header">
        <img src="../../../img/logokemhan.png" alt="Logo">
        <div class="header-text">
            <h2>PUSAT KODIFIKASI<br>BADAN LOGISTIK PERTAHANAN KEMHAN</h2>
            <p><?php echo htmlspecialchars($row_app['alamat']); ?> &nbsp;|&nbsp; Kode Pos 12450 &nbsp;|&nbsp; Telp <?php echo htmlspecialchars($row_app['no_telp']); ?></p>
        </div>
    </div>
    <div class="sub-line"></div>

    <div class="doc-title">Lembar Soal &amp; Jawaban Siswa</div>
    <div class="doc-subtitle"><?php echo htmlspecialchars($data['judul']); ?></div>

    <!-- Info siswa & ujian -->
    <table class="info-table">
        <tr><td>Nama</td><td>:</td><td><?php echo htmlspecialchars($data['nama_siswa']); ?></td>
            <td style="width:110px;font-weight:bold;">Kategori</td><td style="width:10px;">:</td>
            <td><?php echo htmlspecialchars($data['nama_kelas']); ?></td></tr>
        <tr><td>NIP</td><td>:</td><td><?php echo htmlspecialchars($data['nis']); ?></td>
            <td style="font-weight:bold;">Mata Ujian</td><td>:</td>
            <td><?php echo htmlspecialchars($data['nama_mapel']); ?></td></tr>
        <tr><td>Tanggal</td><td>:</td><td><?php echo tgl(date('Y-m-d', strtotime($data['tanggal']))); ?></td>
            <td style="font-weight:bold;">Tipe Soal</td><td>:</td>
            <td><?php echo $is_pg ? 'Pilihan Ganda' : 'Essay'; ?></td></tr>
        <tr><td>Pengawas</td><td>:</td><td colspan="4"><?php echo htmlspecialchars($data['nama_guru']); ?></td></tr>
    </table>

    <hr class="section">

    <!-- Summary nilai -->
    <div class="summary-box">
        <?php if ($is_pg): ?>
        <div class="summary-item"><span class="lbl">Jumlah Soal :</span><span class="val"><?php echo $jumlah_soal; ?></span></div>
        <?php endif; ?>
        <div class="summary-item"><span class="lbl">Nilai :</span><span class="val"><?php echo number_format($nilai, 2); ?></span></div>
        <div class="summary-item"><span class="lbl">Nilai Minimum :</span><span class="val"><?php echo $nilai_kelulusan; ?></span></div>
        <div class="summary-item"><span class="lbl">Status :</span>
            <span class="<?php echo ($status=='Kompeten') ? 'status-kompeten' : 'status-belum'; ?>"><?php echo $status; ?></span>
        </div>
    </div>

    <hr class="section">

    <!-- Daftar Soal -->
    <?php
    $no = 0;
    mysqli_data_seek($hasil_soal, 0);
    while ($row_soal = mysqli_fetch_array($hasil_soal)):
        $no++;
        $id_soal   = $row_soal['id_soal'];
        $hasil_row = isset($map_hasil[$id_soal]) ? $map_hasil[$id_soal] : null;
    ?>
    <div class="question-block">
        <div class="question-num">Soal <?php echo $no; ?></div>
        <div class="question-text"><?php echo nl2br(htmlspecialchars($row_soal['soal'])); ?></div>

        <?php if (!empty($row_soal['gambar'])): ?>
        <div class="question-img">
            <img src="../../../uploads/soal/<?php echo htmlspecialchars($row_soal['gambar']); ?>" alt="Gambar Soal">
        </div>
        <?php endif; ?>

        <?php if ($is_pg): ?>
        <?php
            // Ambil semua opsi untuk soal ini
            $r_options = mysqli_query($kon, "select * from jawaban where id_soal='$id_soal' order by id_jawaban asc");
            $chosen_id = $hasil_row ? $hasil_row['id_jawaban'] : 0;
            $idx = 0;
        ?>
        <div class="options">
        <?php while ($opt = mysqli_fetch_array($r_options)):
            $letter    = isset($letters[$idx]) ? $letters[$idx] : chr(65+$idx);
            $is_benar  = ($opt['jawaban'] == 1);
            $is_dipilih= ($opt['id_jawaban'] == $chosen_id);

            if ($is_benar && $is_dipilih) {
                $row_class = 'option-correct';
                $badge = '<span class="badge badge-both">&#10003; Benar &amp; Dipilih</span>';
                $jumlah_benar++;
            } elseif ($is_benar) {
                $row_class = 'option-correct';
                $badge = '<span class="badge badge-correct">&#10003; Kunci Jawaban</span>';
            } elseif ($is_dipilih) {
                $row_class = 'option-wrong';
                $badge = '<span class="badge badge-chosen">&#10007; Dipilih Siswa</span>';
            } else {
                $row_class = '';
                $badge = '';
            }
            $idx++;
        ?>
            <div class="option-row <?php echo $row_class; ?>">
                <span class="option-letter"><?php echo $letter; ?>.</span>
                <span class="option-text"><?php echo htmlspecialchars($opt['pilihan']); ?></span>
                <?php echo $badge; ?>
            </div>
        <?php endwhile; ?>
        </div>

        <?php else: ?>
        <!-- Essay -->
        <div class="essay-label">Jawaban Siswa:</div>
        <div class="essay-box">
            <?php
            if ($hasil_row && !empty($hasil_row['essay'])) {
                echo nl2br(htmlspecialchars($hasil_row['essay']));
            } else {
                echo '<em style="color:#999;">(Tidak dijawab)</em>';
            }
            ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>

    <?php if ($is_pg): ?>
    <hr class="section">
    <div style="text-align:right; font-size:10pt; color:#555;">
        Jawaban benar: <strong><?php echo $jumlah_benar; ?></strong> dari <strong><?php echo $jumlah_soal; ?></strong> soal
    </div>
    <?php endif; ?>

    <!-- Tanda tangan -->
    <div style="text-align:right; margin-top:30px; font-size:10pt; color:#444;">
        Jakarta, <?php echo tgl(date('Y-m-d')); ?><br><br>
        Pengawas,<br><br><br>
        <strong style="color:#14286e;"><?php echo htmlspecialchars($data['nama_guru']); ?></strong><br>
        <span style="color:#888; font-size:9pt;">NIP. ______________________</span>
    </div>

</div>
</body>
</html>
