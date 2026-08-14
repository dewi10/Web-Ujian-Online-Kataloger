<?php
// Include database
include '../../../config/database.php';

// Ensure session_start is at the very beginning
session_start();

$id_guru = isset($_SESSION['id_guru']) ? $_SESSION['id_guru'] : null;
$status_tab = isset($_POST['status_tab']) ? (int)$_POST['status_tab'] : 1;

// Check if id_guru is set to avoid accessing a null value
if (!$id_guru) {
    die("Access denied: id_guru is not set in session.");
}

date_default_timezone_set('Asia/Jakarta'); 
$kelas = "";
$mapel = "";

// Pastikan kolom status_aktif tersedia
$cek_kolom = mysqli_query($kon, "SHOW COLUMNS FROM ujian LIKE 'status_aktif'");
if ($cek_kolom && mysqli_num_rows($cek_kolom) == 0) {
    mysqli_query($kon, "ALTER TABLE ujian ADD COLUMN status_aktif TINYINT(1) NOT NULL DEFAULT 1");
}

if (!empty($_POST['kelas']) && is_array($_POST['kelas'])) {
    $kelas_arr = array();
    foreach ($_POST['kelas'] as $value) {
        $value = trim((string)$value);
        if ($value !== '') {
            $kelas_arr[] = "'" . mysqli_real_escape_string($kon, $value) . "'";
        }
    }
    if (!empty($kelas_arr)) {
        $kelas = implode(',', $kelas_arr);
    }
}

if (!empty($_POST['mapel']) && is_array($_POST['mapel'])) {
    $mapel_arr = array();
    foreach ($_POST['mapel'] as $value) {
        $value = trim((string)$value);
        if ($value !== '') {
            $mapel_arr[] = "'" . mysqli_real_escape_string($kon, $value) . "'";
        }
    }
    if (!empty($mapel_arr)) {
        $mapel = implode(',', $mapel_arr);
    }
}

if ($kelas !== '' && $mapel === '') {
    $sql = "SELECT * FROM ujian u
            INNER JOIN kelas k ON u.id_kelas=k.id_kelas
            INNER JOIN mapel m ON m.id_mapel=u.id_mapel
            WHERE u.id_guru='$id_guru' AND u.status_aktif='$status_tab' AND k.kode_kelas IN ($kelas)
            ORDER BY u.tanggal DESC, u.jam DESC";
} elseif ($kelas === '' && $mapel !== '') {
    $sql = "SELECT * FROM ujian u
            INNER JOIN kelas k ON u.id_kelas=k.id_kelas
            INNER JOIN mapel m ON m.id_mapel=u.id_mapel
            WHERE u.id_guru='$id_guru' AND u.status_aktif='$status_tab' AND u.id_mapel IN ($mapel)
            ORDER BY u.tanggal DESC, u.jam DESC";
} elseif ($kelas !== '' && $mapel !== '') {
    $sql = "SELECT * FROM ujian u
            INNER JOIN kelas k ON u.id_kelas=k.id_kelas
            INNER JOIN mapel m ON m.id_mapel=u.id_mapel
            WHERE u.id_guru='$id_guru' AND u.status_aktif='$status_tab' AND k.kode_kelas IN ($kelas) AND u.id_mapel IN ($mapel)
            ORDER BY u.tanggal DESC, u.jam DESC";
} else {
    $sql = "SELECT * FROM ujian u
            INNER JOIN kelas k ON u.id_kelas=k.id_kelas
            INNER JOIN mapel m ON m.id_mapel=u.id_mapel
            WHERE u.id_guru='$id_guru' AND u.status_aktif='$status_tab'
            ORDER BY u.tanggal DESC, u.jam DESC";
}

$hasil = mysqli_query($kon, $sql);
$no = 0;
$jumlah_soal = 0;

// HTML output starts here
?>

<div class="table-responsive">
    <table class="table table-bordered" id="tabel_ujian">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Judul</th>
                <th>Mapel</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Peserta</th>
                <th>Soal</th>
                <th>Status</th>
                <th width="280px" style="white-space:nowrap;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($data = mysqli_fetch_array($hasil)) {
                $no++;

                $query1 = mysqli_query($kon, "SELECT id_soal FROM soal s INNER JOIN ujian u ON u.id_ujian=s.id_ujian WHERE u.id_ujian='" . $data['id_ujian'] . "' AND s.tipe='" . $data['tipe_soal'] . "'");
                $jumlah_soal = mysqli_num_rows($query1);

                $query2 = mysqli_query($kon, "SELECT * FROM peserta WHERE id_ujian='" . $data['id_ujian'] . "' AND id_kelas='" . $data['id_kelas'] . "'");
                $jumlah_peserta = mysqli_num_rows($query2);
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $data['kode_ujian']; ?></td>
                <td>
                    <?php echo $data['judul']; ?>
                    <p><small class="text-muted">Tipe Soal <?php echo $data['tipe_soal'] == 1 ? 'Pilihan Ganda' : 'Essay'; ?></small></p>
                </td>
                <td><?php echo $data['nama_mapel']; ?></td>
                <td><?php echo $data['nama_kelas']; ?></td>
                <td><?php echo tanggal(date('Y-m-d', strtotime($data["tanggal"]))); ?> 
                    <p><small class="text-muted"><?php echo date('H:i', strtotime($data["jam"])); ?> - <?php echo date("H:i", strtotime("+" . $data['waktu'] . " minutes", strtotime($data["jam"]))); ?> WIB</small></p>
                </td>
                <td><?php echo $jumlah_peserta; ?></td>
                <td><?php echo $jumlah_soal; ?></td>
                <td>
                    <?php
                    if ((int)$data['status_aktif'] === 1) {
                        echo "<span class='badge badge-pill badge-primary mb-1'>Aktif</span><br>";
                    } else {
                        echo "<span class='badge badge-pill badge-secondary mb-1'>Nonaktif</span><br>";
                    }
                    ?>
                    <?php
                    $saat_ini = date('Y-m-d H:i:s');
                    $mulai = $data["tanggal"] . " " . $data["jam"];
                    $selesai = date("Y-m-d H:i:s", strtotime("+" . $data['waktu'] . " minutes", strtotime($mulai)));

                    if ($saat_ini < $mulai) {
                        echo "<span class='badge badge-pill badge-primary'>Belum Mulai</span>";
                    } elseif ($saat_ini > $selesai) {
                        echo "<span class='badge badge-pill badge-success'>Telah Selesai</span>";
                    } else {
                        echo "<span class='badge badge-pill badge-warning'>Sedang Berlangsung</span><br>";
                        $start_date = new DateTime($data["jam"]);
                        $since_start = $start_date->diff(new DateTime(date('H:i:s')));

                        $minutes = $since_start->days * 24 * 60;
                        $minutes += $since_start->h * 60;
                        $minutes += $since_start->i;
                        echo "<small class='text-primary'> " . $minutes . " menit lalu</small>";
                    }
                    ?> 
                </td>
                <td class="text-center" style="white-space:nowrap;">
                    <button type="button" class="btn_peserta btn btn-info btn-circle btn-sm" id_ujian="<?php echo $data['id_ujian']; ?>" id_kelas="<?php echo $data['id_kelas']; ?>" kode_ujian="<?php echo $data['kode_ujian']; ?>" data-toggle="tooltip" title="Atur Peserta" ><i class="fa fa-user"></i></button>
                    <a href="index.php?page=input-soal&id=<?php echo $data['id_ujian']; ?>" class="btn btn-success btn-circle btn-sm"  data-toggle="tooltip" title="Input Soal"><i class="fas fa-list-alt"></i></a>
                    <?php if ((int) $data['tipe_soal'] === 1): ?>
                    <button type="button" class="btn_import_soal btn btn-primary btn-circle btn-sm" id_ujian="<?php echo (int) $data['id_ujian']; ?>" kode_ujian="<?php echo htmlspecialchars($data['kode_ujian']); ?>" data-toggle="tooltip" title="Import Soal (.doc/.docx/.txt)"><i class="fas fa-file-import"></i></button>
                    <?php endif; ?>
                    <button class="btn_edit btn btn-warning btn-circle btn-sm" id_ujian="<?php echo $data['id_ujian']; ?>" kode_ujian="<?php echo $data['kode_ujian']; ?>" data-toggle="tooltip" title="Edit Ujian" ><i class="fa fa-edit"></i></button>
                    <?php if ((int)$data['status_aktif'] === 1): ?>
                        <button class="btn_nonaktif btn btn-secondary btn-circle btn-sm" data-toggle="tooltip" title="Nonaktifkan" id_ujian="<?php echo $data['id_ujian']; ?>" status="0"><i class="fas fa-toggle-off"></i></button>
                    <?php else: ?>
                        <button class="btn_nonaktif btn btn-primary btn-circle btn-sm" data-toggle="tooltip" title="Aktifkan" id_ujian="<?php echo $data['id_ujian']; ?>" status="1"><i class="fas fa-toggle-on"></i></button>
                    <?php endif; ?>
                    <a href="pages/guru/ujian/hapus.php?id=<?php echo $data['id_ujian']; ?>" class="btn_hapus btn btn-danger btn-circle btn-sm"  data-toggle="tooltip" title="Hapus Ujian"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php 
// Function to format date
function tanggal($tanggal)
{
    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}
?>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    $('#tabel_ujian').DataTable({
        "searching": true,
        "paging": true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: ['excel', 'print', 'copy']
    });
});

    // Tambah ujian
    $('#btn_tambah').on('click',function(){
        $.ajax({
            url: 'pages/guru/ujian/tambah.php',
            method: 'post',
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah Ujian Baru';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });

// Add event listeners for buttons
$('.btn_peserta').on('click', function() {
    var kode_ujian = $(this).attr("kode_ujian");
    var id_ujian = $(this).attr("id_ujian");
    var id_kelas = $(this).attr("id_kelas");
    $.ajax({
        url: 'pages/guru/ujian/peserta.php',
        method: 'post',
        data: { id_kelas: id_kelas, id_ujian: id_ujian },
        success: function(data) {
            $('#tampil_data').html(data);
            document.getElementById("judul").innerHTML = 'Atur Peserta Ujian #' + kode_ujian;
        }
    });
    $('#modal').modal('show');
});

$('.btn_edit').on('click', function() {
    var id_ujian = $(this).attr("id_ujian");
    var kode_ujian = $(this).attr("kode_ujian");
    $.ajax({
        url: 'pages/guru/ujian/edit.php',
        method: 'post',
        data: { kode_ujian: kode_ujian, id_ujian: id_ujian },
        success: function(data) {
            $('#tampil_data').html(data);
            document.getElementById("judul").innerHTML = 'Edit Ujian #' + kode_ujian;
        }
    });
    $('#modal').modal('show');
});

$('.btn_hapus').on('click', function() {
    return confirm("Yakin ingin menghapus data ujian ini?");
});

$(document).off('click', '.btn_import_soal').on('click', '.btn_import_soal', function() {
    var id_ujian = $(this).attr('id_ujian');
    var kode = $(this).attr('kode_ujian');
    $.ajax({
        url: 'pages/guru/ujian/import-soal-form.php',
        method: 'get',
        data: { id_ujian: id_ujian },
        success: function(html) {
            $('#tampil_data').html(html);
            document.getElementById('judul').innerHTML = 'Import Soal &mdash; ' + kode;
            $('#modal').modal('show');
        },
        error: function() {
            alert('Gagal memuat form import.');
        }
    });
});

$('.btn_nonaktif').on('click', function(){
    var id_ujian = $(this).attr('id_ujian');
    var status = $(this).attr('status');
    var konfirmasi = status == '0'
        ? 'Yakin ingin menonaktifkan ujian ini?'
        : 'Yakin ingin mengaktifkan ujian ini kembali?';

    if (!confirm(konfirmasi)) return;

    $.ajax({
        type: 'POST',
        url: 'pages/guru/ujian/toggle-status.php',
        data: {id_ujian:id_ujian, status_aktif:status},
        success: function(res){
            if ((res || '').toString().trim() === 'ok') {
                if (typeof tabel_ujian === 'function') tabel_ujian();
            } else {
                alert('Gagal mengubah status ujian. ' + res);
            }
        },
        error: function(){
            alert('Gagal mengubah status ujian.');
        }
    });
});
</script>