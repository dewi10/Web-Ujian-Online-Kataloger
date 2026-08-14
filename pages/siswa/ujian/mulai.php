<?php
    require_once __DIR__ . '/../../../config/database.php';
    // Set waktu indonesia bagian barat
    date_default_timezone_set('Asia/Jakarta'); 
    // Validasi hanya siswa yang boleh mengakses halaman ini
    $username = $_SESSION['username'];
    $cek = mysqli_query ($kon,"select * from siswa where username='".$username."' limit 1");
    $jum = mysqli_num_rows($cek);

    if ($jum < 1) {
        echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
        exit;
    }
?>

<?php 

    $id_ujian = addslashes(trim($_GET['id']));
    $id_siswa = $_SESSION["id_siswa"];

    $query_peserta = mysqli_query($kon, "select * from peserta where id_siswa='".$id_siswa."' and id_ujian='".$id_ujian."' limit 1");
    $cek_peserta = mysqli_num_rows($query_peserta);

    if ($cek_peserta <= 0) {
        echo "<center><h5>Data tidak ditemukan</h5></center>";
        echo "<p class='text-center text-muted'>Anda belum terdaftar sebagai peserta ujian ini. Minta pengawas mencentang nama Anda di menu Atur Peserta.</p>";
        exit;
    }

    $sql = "select u.*, m.nama_mapel from ujian u
            inner join mapel m on m.id_mapel=u.id_mapel
            where u.id_ujian='$id_ujian' limit 1";
    $hasil = mysqli_query($kon, $sql);
    $row = ($hasil && mysqli_num_rows($hasil) > 0) ? mysqli_fetch_array($hasil) : null;

    if (!$row) {
        echo "<center><h5>Data tidak ditemukan</h5></center>";
        exit;
    }

    $q_bank = mysqli_query($kon, "select count(*) as c from soal where id_ujian='$id_ujian'");
    $row_bank = mysqli_fetch_assoc($q_bank);
    $jumlah_bank = (int) ($row_bank['c'] ?? 0);

    if ($jumlah_bank <= 0) {
        echo "<br><div class='alert alert-danger'><i class='fas fa-ban'></i> Soal Belum diinput</div>";
        exit;
    }

    $query_hasil = mysqli_query($kon, "select count(*) as c from hasil where id_siswa='".$id_siswa."' and id_ujian='".$id_ujian."'");
    $row_hasil = mysqli_fetch_assoc($query_hasil);
    $cek_hasil = (int) ($row_hasil['c'] ?? 0);

    // Belum klik Kerjakan / link Lanjutkan tanpa set-default — inisialisasi 50 soal acak
    if ($cek_hasil <= 0) {
        header('Location: pages/siswa/hasil/set-default.php?id=' . urlencode((string) $id_ujian));
        exit;
    }

    $nama_mapel = $row['nama_mapel'];

    $saat_ini = date('Y-m-d H:i:s');
    $mulai = $row["tanggal"] . " " . $row["jam"];
    $selesai = date("Y-m-d H:i:s", strtotime("+" . $row['waktu'] . " minutes", strtotime($mulai)));

    if ($saat_ini < $mulai) {
        echo "<br><div class='text-center'><div class='alert alert-info'><i class='fas fa-hourglass-half'></i> Ujian belum dimulai</div></div>";
        exit;
    } else if ($saat_ini > $selesai) {
        echo "<br><div class='text-center'><div class='alert alert-warning'><strong><i class='fas fa-ban'></i> Ujian telah selesai</strong></div></div>";
        if (isset($_SESSION["mulai_ujian"])) {
            unset($_SESSION['mulai_ujian']); 
        }
        exit;
    }

    // Cek apakah siswa sudah menyelesaikan ujian SPESIFIK ini atau belum
    
    // Cek apakah ada riwayat untuk ujian spesifik ini (bukan berdasarkan tanggal)
    $hasil_riwayat = mysqli_query($kon, "SELECT id_riwayat FROM riwayat WHERE id_ujian='$id_ujian' AND id_siswa='$id_siswa' LIMIT 1");
    $ujian_sudah_selesai = mysqli_num_rows($hasil_riwayat) > 0;

    // Jika ujian spesifik ini sudah diselesaikan (ada riwayat), blokir akses
    if ($ujian_sudah_selesai) {
        echo "<br><div class='text-center'><div class='alert alert-success'><i class='fas fa-check-circle'></i> Anda telah menyelesaikan ujian ini</div></div>";
        if (isset($_SESSION["mulai_ujian"])) {
            unset($_SESSION['mulai_ujian']); 
        }
        exit;
    }

    // Bangun urutan soal acak per login/session (konsistent selama sesi aktif)
    if (!isset($_SESSION['urutan_soal']) || !is_array($_SESSION['urutan_soal'])) {
        $_SESSION['urutan_soal'] = array();
    }

    // Cek soal yang sudah dijawab
    $soal_terjawab = array();
    $query_terjawab = mysqli_query($kon, "SELECT id_soal FROM hasil WHERE id_siswa='$id_siswa' AND id_ujian='$id_ujian' AND (IFNULL(id_jawaban,0) != 0 OR LENGTH(TRIM(IFNULL(essay,''))) > 0)");
    while ($r = mysqli_fetch_assoc($query_terjawab)) {
        $soal_terjawab[] = (int)$r['id_soal'];
    }

    // Soal aktif peserta (subset acak dari bank, disimpan di tabel hasil)
    $query_soal_ids = mysqli_query($kon, "SELECT id_soal FROM hasil WHERE id_siswa='$id_siswa' AND id_ujian='$id_ujian'");
    $soal_ids = array();
    while ($r = mysqli_fetch_assoc($query_soal_ids)) {
        $soal_ids[] = (int)$r['id_soal'];
    }

    // Urutan: soal sudah dijawab tetap di depan (urut id_soal), sisanya di-acak (seed tetap per sesi)
    sort($soal_ids);
    $soal_terjawab_sorted = $soal_terjawab;
    sort($soal_terjawab_sorted);
    $urutan_key = md5(implode(',', $soal_ids) . '|' . implode(',', $soal_terjawab_sorted));

    $perlu_bangun_ulang = true;
    if (isset($_SESSION['urutan_soal'][$id_ujian], $_SESSION['urutan_soal_key'][$id_ujian])
        && $_SESSION['urutan_soal_key'][$id_ujian] === $urutan_key
        && is_array($_SESSION['urutan_soal'][$id_ujian])) {
        $perlu_bangun_ulang = false;
    }

    if ($perlu_bangun_ulang) {
        $soal_sudah_dijawab = array_values(array_intersect($soal_ids, $soal_terjawab));
        sort($soal_sudah_dijawab);
        $soal_belum_dijawab = array_values(array_diff($soal_ids, $soal_sudah_dijawab));

        $seed = crc32(session_id() . '|' . $id_siswa . '|' . $id_ujian);
        mt_srand($seed);
        shuffle($soal_belum_dijawab);
        mt_srand();

        $_SESSION['urutan_soal'][$id_ujian] = array_merge($soal_sudah_dijawab, $soal_belum_dijawab);
        $_SESSION['urutan_soal_key'][$id_ujian] = $urutan_key;
    }
    
    // Set nomor soal saat ini untuk continue (soal pertama yang belum dijawab)
    $current_soal_index = 0;
    foreach ($_SESSION['urutan_soal'][$id_ujian] as $index => $soal_id) {
        if (!in_array($soal_id, $soal_terjawab)) {
            $current_soal_index = $index;
            break;
        }
    }
    $_SESSION['current_soal_index'][$id_ujian] = $current_soal_index;

    if (isset($_GET['pesan']) && $_GET['pesan'] === 'ujian-aktif') {
        echo '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Ujian Anda masih berlangsung. Silakan lanjutkan mengerjakan soal. Untuk keluar dari akun, gunakan <a href="logout.php?force=1">Keluar paksa</a> (ujian tetap bisa dilanjutkan setelah login kembali selama waktu masih ada).</div>';
    }
?>
<hr>
<div class="card">
    <div class="card-body">

        <div id='timer'></div>
        <div class="progress mb-2" id="tampil_progress" style="height: 1.5rem; background-color: #e9ecef;">
        </div>
        <div class="card">
            <div class="card-header"><strong>Soal nomor <span id="nomor"> </span> </strong> </div>
            <div class="card-body">

                <div id="tampil_soal">
                
                </div>
            </div>
        </div>
        <br>
        <div id="nomor_soal"> </div>

        <br>
        <div class="row">
            <div class="col-md-6">
                <small><span class="badge badge-primary">&nbsp;&nbsp;</span> Biru : Soal yang telah dikerjakan</small><br>
                <small><span class="badge badge-light border">&nbsp;&nbsp;</span> Putih : Soal yang belum dikerjakan</small><br>
            </div>
            <div class="col-md-6 text-right">
                <button id="btn_soal_berikutnya" class="btn btn-info btn-sm mr-2"><i class="fas fa-arrow-right"></i> Soal Berikutnya</button>
                <a href="pages/siswa/ujian/simpan-hasil.php?id=<?php echo $id_ujian; ?>" id="tombol_selesai" class="btn btn-success btn-sm" ><i class="fas fa-check"></i> Selesai</a>
            </div>
        </div>
    </div> 
</div>

<input type="hidden" name="id_ujian" id="id_ujian" value="<?php echo $id_ujian;?>" />

<?php
    // Simpan nama mata pelajaran ke dalam session
    $_SESSION["nama_mapel"] = $nama_mapel;
    $_SESSION["id_ujian"] = $id_ujian;
    // Include file timer untuk membuat perhitungan waktu ujian
    include 'pages/siswa/ujian/timer.php'; 
?>

<!-- Add this within the script tag -->
<script>
$(document).ready(function() {
    var id_ujian = $("#id_ujian").val();
    // Menampilkan soal (lanjut dari yang belum dijawab)
    $.ajax({
        url: 'pages/siswa/ujian/ambil-soal.php',
        method: 'post',
        data: {id_ujian: id_ujian, continue_exam: true},
        success: function(data) {
            $('#tampil_soal').html(data);
        }
    });
    // Menampilkan progress
    $.ajax({
        url: 'pages/siswa/ujian/progress.php',
        method: 'post',
        data: {id_ujian: id_ujian},
        success: function(data) {
            $('#tampil_progress').html(data);
        }
    });
    // Mengambil nomor soal
    $('#nomor_soal').load('pages/siswa/ujian/nomor-soal.php?id=<?php echo $id_ujian; ?>');

    // Konfirmasi ketika user mengklik tombol selesai
    $('#tombol_selesai').on('click', function(e) {
        e.preventDefault();
        var konfirmasi = confirm("Anda masih memiliki waktu. Yakin ingin mengakhiri ujian?");
        if (konfirmasi) {
            window.location.href = $(this).attr('href');
        }
    });
    
    // Handler untuk tombol soal berikutnya (skip ke soal yang belum dijawab)
    $('#btn_soal_berikutnya').on('click', function() {
        var id_ujian = $("#id_ujian").val();
        $.ajax({
            url: 'pages/siswa/ujian/get-next-unanswered.php',
            method: 'post',
            data: {id_ujian: id_ujian},
            success: function(data) {
                var result = JSON.parse(data);
                if (result.success) {
                    // Load soal berikutnya
                    $.ajax({
                        url: 'pages/siswa/ujian/ambil-soal.php',
                        method: 'post',
                        data: {id_ujian: id_ujian, id_soal: result.id_soal},
                        success: function(soal_data) {
                            $('#tampil_soal').html(soal_data);
                            document.getElementById("nomor").innerHTML = result.nomor;
                        }
                    });
                } else {
                    alert('Semua soal sudah dikerjakan!');
                }
            }
        });
    });

    // Delegasi: nomor soal di-load ulang via AJAX, binding langsung di nomor-soal.php tidak andal
    $(document).on('click', '.pilih_nomor_soal .page-link, .pilih_nomor_soal', function(e) {
        e.preventDefault();
        var $li = $(this).closest('.pilih_nomor_soal');
        if (!$li.length) { $li = $(this); }
        var id_soal = $li.attr('id_soal');
        var kode_soal = $li.attr('kode_soal');
        var id_ujian = $li.attr('id_ujian');
        var nomor = $li.attr('nomor');
        $.ajax({
            url: 'pages/siswa/ujian/ambil-soal.php',
            method: 'post',
            data: { kode_soal: kode_soal, id_soal: id_soal, id_ujian: id_ujian },
            success: function(data) {
                $('#tampil_soal').html(data);
                document.getElementById('nomor').innerHTML = nomor;
            }
        });
    });
});
</script>
