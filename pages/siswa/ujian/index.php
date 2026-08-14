<?php
// Redirect ujian hari ini / selesai dilakukan di index.php sebelum output HTML (header() di sini gagal dan membuat halaman kosong).
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['id_siswa'])) {
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}

$username = $_SESSION['username'];
$cek = mysqli_query($kon, "SELECT * FROM siswa WHERE username='" . mysqli_real_escape_string($kon, $username) . "' LIMIT 1");
if (!$cek || mysqli_num_rows($cek) < 1) {
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your head content -->
</head>
<body>
<hr>
<div class="card">
    <div class="card-body">
        <h3><i class="fas fa-clipboard-list"></i> Daftar Ujian Saya</h3><hr>

        <?php 
        if (isset($_SESSION["mulai_ujian"])):
            $nama_mapel = $_SESSION["nama_mapel"];
            $id_ujian = $_SESSION["id_ujian"];
            echo "<div class='alert alert-warning'><i class='fas fa-clock'></i> Ujian <strong>".$nama_mapel."</strong> sedang berlangsung. Jangan khawatir, jawaban yang sudah Anda isi sudah tersimpan otomatis. <a href='index.php?page=review&id=".$id_ujian."' class='btn btn-warning btn-sm ml-2' role='button'><i class='fas fa-play'></i> Lanjutkan Ujian</a> </div>";
        endif; 
        ?>

        <div class="row mt-4">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Pengawas</label>
                    <select id="guru" class="select2" multiple="multiple" style="width: 100%" name="guru[]">
                        <?php
                            $id_siswa = mysqli_real_escape_string($kon, (string) $_SESSION["id_siswa"]);
                            $hasil = mysqli_query($kon, "SELECT id_kelas FROM siswa WHERE id_siswa='$id_siswa'");
                            $row = mysqli_fetch_array($hasil);
                            $id_kelas = $row['id_kelas'];
                            $cek_col_ujian = mysqli_query($kon, "SHOW COLUMNS FROM ujian LIKE 'status_aktif'");
                            $aktif_ujian_sql = ($cek_col_ujian && mysqli_num_rows($cek_col_ujian) > 0) ? " AND u.status_aktif='1'" : '';
        
                            $sql = "SELECT u.id_guru, g.nama_guru FROM ujian u
                                    INNER JOIN kelas k ON u.id_kelas = k.id_kelas
                                    INNER JOIN guru g ON g.id_guru = u.id_guru
                                    INNER JOIN siswa s ON s.id_kelas = k.id_kelas
                                    INNER JOIN peserta p ON p.id_ujian = u.id_ujian AND p.id_siswa = s.id_siswa
                                    WHERE u.id_kelas='$id_kelas' AND s.id_siswa='$id_siswa'$aktif_ujian_sql
                                    GROUP BY u.id_guru";
                            $hasil = mysqli_query($kon, $sql);
                            while ($data = mysqli_fetch_array($hasil)) {
                                echo "<option value='".$data['id_guru']."'>".$data['nama_guru']."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Mata Ujian</label>
                    <select id="mapel" class="select2" multiple="multiple" style="width: 100%" name="mapel[]"></select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group mt-4">
                    <input type="button" class="btn btn-primary" id="tampilkan" value="Tampilkan">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div id="tampil_tabel"></div>
            </div>
        </div>
    </div> 
</div>

<script>
    $(document).ready(function () {
        get_mapel();
        tabel_ujian();
        $(".select2").select2({});
    });

    $("#guru").change(function() {
        get_mapel();
    });

    $('#tampilkan').on('click', function() {
        tabel_ujian();
    });

    function get_mapel() {
        var guru = $("#guru").val();
        $.ajax({
            url: 'pages/siswa/ujian/get-mapel.php',
            method: "POST",
            data: {guru: guru},
            async: false,
            dataType: 'json',
            success: function(data) {
                var html = '';
                for (var i = 0; i < data.length; i++) {
                    html += '<option value=' + data[i].id_mapel + '>' + data[i].nama_mapel + '</option>';
                }
                $('#mapel').html(html);
            }
        });
    }

    function tabel_ujian() {
        var mapel = $("#mapel").val();
        var guru = $("#guru").val();
        $.ajax({
            url: 'pages/siswa/ujian/tabel-ujian.php',
            method: 'post',
            dataType: "html",
            async: false,
            data: {mapel: mapel, guru: guru},
            success: function(data) {
                $('#tampil_tabel').html(data);
            }
        });
    }
</script>
</body>
</html>
