<?php
// Beranda siswa tidak dipakai: arahkan ke hasil (jika pernah menyelesaikan ujian) atau daftar ujian.
if (!isset($_SESSION['username']) || !isset($_SESSION['id_siswa'])) {
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}

include __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/siswa_ujian_aktif.php';

$username = $_SESSION['username'];
$cek = mysqli_query($kon, "SELECT * FROM siswa WHERE username='" . mysqli_real_escape_string($kon, $username) . "' LIMIT 1");
if (!$cek || mysqli_num_rows($cek) < 1) {
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}

$id_siswa = mysqli_real_escape_string($kon, (string) $_SESSION['id_siswa']);
$aktif_ujian = siswa_id_ujian_berlangsung($kon, (string) $_SESSION['id_siswa']);
if ($aktif_ujian > 0) {
    if (!headers_sent()) {
        header('Location: pages/siswa/hasil/set-default.php?id=' . (int) $aktif_ujian);
    } else {
        echo '<script>window.location.replace(' . json_encode('pages/siswa/hasil/set-default.php?id=' . (int) $aktif_ujian) . ');</script>';
    }
    exit;
}
$q_selesai = mysqli_query($kon, "SELECT 1 FROM riwayat WHERE id_siswa='$id_siswa' LIMIT 1");
$ada_hasil = ($q_selesai && mysqli_num_rows($q_selesai) > 0);
$target = $ada_hasil ? 'index.php?page=hasil-ujian-siswa' : 'index.php?page=ujian-siswa';
if (!headers_sent()) {
    header("Location: " . $target);
} else {
    echo '<script>window.location.replace(' . json_encode($target) . ');</script>';
}
exit;
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
            echo "<div class='alert alert-warning'>Ujian <strong>".$nama_mapel."</strong> sedang berlangsung <a href='ujian-online/index.php?page=mulai-ujian&id=".$id_ujian."' class='btn btn-warning btn-sm' role='button'>lanjut & selesaikan</a> </div>";
        endif; 
        ?>

        <div class="row mt-4">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Pengawas</label>
                    <select id="guru" class="select2" multiple="multiple" style="width: 100%" name="guru[]">
                        <?php
                            $id_siswa = $_SESSION["id_siswa"];
                            $hasil = mysqli_query($kon, "SELECT id_kelas FROM siswa WHERE id_siswa='$id_siswa'");
                            $row = mysqli_fetch_array($hasil);
                            $id_kelas = $row['id_kelas'];
        
                            $sql = "SELECT u.id_guru, g.nama_guru FROM ujian u
                                    INNER JOIN kelas k ON u.id_kelas = k.id_kelas
                                    INNER JOIN guru g ON g.id_guru = u.id_guru
                                    INNER JOIN siswa s ON s.id_kelas = k.id_kelas
                                    INNER JOIN peserta p ON p.id_ujian = u.id_ujian
                                    WHERE u.id_kelas='$id_kelas' AND s.id_siswa='$id_siswa'
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
            url: 'ujian-online/get-mapel.php',
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
            url: 'ujian-online/tabel-ujian.php',
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
