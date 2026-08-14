<?php
// Set waktu Indonesia bagian barat
date_default_timezone_set('Asia/Jakarta');

// Mulai sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'config/database.php'; // Pastikan file ini sesuai dengan konfigurasi Anda
require_once __DIR__ . '/../../../config/ujian_soal.php';

// Validasi hanya siswa yang boleh mengakses halaman ini
if (!isset($_SESSION['username']) || !isset($_SESSION['id_siswa'])) {
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}

$username = $_SESSION['username'];
$id_siswa = $_SESSION["id_siswa"];

$cek = mysqli_query($kon, "SELECT * FROM siswa WHERE username='$username' LIMIT 1");
$jum = mysqli_num_rows($cek);

if ($jum < 1) {
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}

// Ambil id_ujian dari tabel peserta berdasarkan id_siswa dengan filter tanggal
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Cek apakah ada ujian >= hari ini (hanya yang terdaftar sebagai peserta)
$hasil_siswa = mysqli_query($kon, "SELECT id_kelas FROM siswa WHERE id_siswa='$id_siswa'");
$row_siswa = mysqli_fetch_array($hasil_siswa);
$id_kelas = $row_siswa['id_kelas'];

$cek_ujian_baru = mysqli_query($kon, "SELECT count(*) as jumlah FROM ujian u
    INNER JOIN kelas k ON u.id_kelas=k.id_kelas
    INNER JOIN siswa s ON s.id_kelas=k.id_kelas
    INNER JOIN peserta p ON p.id_ujian=u.id_ujian AND p.id_siswa=s.id_siswa
    WHERE k.id_kelas='$id_kelas' AND s.id_siswa='$id_siswa' AND u.tanggal >= '$today'");
$row_cek = mysqli_fetch_array($cek_ujian_baru);
$ada_ujian_baru = $row_cek['jumlah'] > 0;

// Jika ada ujian baru (hari ini atau ke depan), hanya tampilkan ujian >= hari ini
// Jika tidak ada ujian baru, tampilkan ujian kemarin (maksimal 1 hari ke belakang)
$filter_tanggal = $ada_ujian_baru ? ">= '$today'" : ">= '$yesterday'";

$query_peserta = mysqli_query($kon, "SELECT p.id_ujian FROM peserta p 
    INNER JOIN ujian u ON u.id_ujian = p.id_ujian 
    INNER JOIN siswa s ON s.id_siswa = p.id_siswa AND s.id_kelas = u.id_kelas
    WHERE p.id_siswa='$id_siswa' AND u.tanggal $filter_tanggal
    ORDER BY u.id_ujian ASC");
$id_ujian_list = [];
while ($row_peserta = mysqli_fetch_assoc($query_peserta)) {
    $id_ujian_list[] = $row_peserta['id_ujian'];
}

// Validasi apakah ujian masih boleh diakses
if (isset($_GET['id'])) {
    $id_ujian_akses = $_GET['id'];
    
    // Cek apakah ujian ini masuk dalam list yang diizinkan
    if (!in_array($id_ujian_akses, $id_ujian_list)) {
        echo "<script>
            alert('Ujian ini sudah tidak dapat diakses. Silakan pilih ujian yang tersedia.');
            window.location.href='index.php?page=ujian';
        </script>";
        exit;
    }
}

// Validasi URL Parameter
if (isset($_GET['auth'])) {
    if ($_GET['auth'] == 'soal-tidak-tersedia') {
        echo "<br><div class='alert alert-danger'>Soal belum diinput</div>";
    }
}

?>

<script type="text/javascript">
    function showTime() {
        var today = new Date();
        var curr_hour = today.getHours();
        var curr_minute = today.getMinutes();
        var curr_second = today.getSeconds();
        curr_hour = checkTime(curr_hour);
        curr_minute = checkTime(curr_minute);
        curr_second = checkTime(curr_second);
        document.getElementById('time').innerHTML = curr_hour + ":" + curr_minute + ":" + curr_second + " WIB";
    }

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }
    setInterval(showTime, 500);
</script>

<br>
<div class="card border-0">
    <div class="dashboard-header">
        <div class="row">
            <div class="col-sm-6">
                <h3><i class="fas fa-info-circle"></i> Informasi Ujian</h3>
            </div>
            <div class="col-sm-6">
                <h3 align="right"><div id="time"></div></h3>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php
        foreach ($id_ujian_list as $id_ujian) {
            $hasil = mysqli_query($kon, "SELECT * FROM ujian u
                INNER JOIN kelas k ON u.id_kelas=k.id_kelas
                INNER JOIN siswa s ON s.id_kelas=k.id_kelas
                INNER JOIN mapel m ON m.id_mapel=u.id_mapel
                INNER JOIN guru g ON g.id_guru=u.id_guru
                WHERE u.id_ujian='$id_ujian' AND s.id_siswa='$id_siswa' LIMIT 1");

            $cek = mysqli_num_rows($hasil);

            if ($cek <= 0) {
                echo "<center><h5>Data tidak ditemukan</h5></center>";
                exit;
            }

            $row = mysqli_fetch_array($hasil);

            $query1 = mysqli_query($kon, "SELECT * FROM peserta WHERE id_siswa='$id_siswa' AND id_ujian='$id_ujian'");
            $cek_peserta = mysqli_num_rows($query1);

            if ($cek_peserta <= 0) {
                echo "<div class='alert alert-warning'><i class='fas fa-user-slash'></i> Mohon maaf, Anda tidak terdaftar sebagai peserta pada ujian ini. Hubungi pihak penyelenggara untuk informasi lebih lanjut!</div>";
                $peserta = false;
            } else {
                $peserta = true;
            }
        ?>
        
        <div class="table-responsive">
            <table class="table table-border" id="tabel_ujian">
                <tbody>
                <tr>
                    <td>Judul</td> <td>:  <?php echo $row['judul']; ?> - <?php echo $row['kode_ujian']; ?>  </td>
                </tr>
                <tr>
                    <td>Tipe Soal</td> <td>: <?php echo $row['tipe_soal'] == 1 ? 'Pilihan Ganda' : 'Essay'; ?> </td>
                </tr>
                <tr>
                    <td>Tanggal</td><td>: <?php echo tanggal(date('Y-m-d', strtotime($row["tanggal"]))); ?> </td>
                </tr>
                <tr>
                    <td>Jam</td> <td>: <?php echo date('H:i', strtotime($row["jam"])); ?> - <?php echo date("H:i", strtotime("+" . $row['waktu'] . " minutes", strtotime($row["jam"]))); ?> WIB</td>
                </tr>
                <tr>
                    <td>Waktu</td> <td>: <?php echo $row['waktu']; ?> Menit</td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
            $hasil = mysqli_query($kon, "SELECT id_soal FROM soal WHERE id_ujian='$id_ujian'");
            $jumlah_bank = mysqli_num_rows($hasil);
            $jumlah_soal = ujian_jumlah_soal_tampil($jumlah_bank);
        ?>
        <div class="card">
            <div class="card-header"><i class="far fa-question-circle"></i> Petunjuk Pengerjaan</div>
            <div class="card-body">
                <ul>
                    <li>Soal berupa <?php echo $row['tipe_soal'] == 1 ? 'pilihan ganda' : 'essay'; ?> berjumlah <?php echo $jumlah_soal; ?> soal</li>
                    <li>Kerjakan terlebih dahulu soal yang dianggap mudah</li>
                    <li>Perhatikan waktu yang tersedia agar dapat menyelesaikan semua soal dengan baik</li>
                    <li>Peserta wajib mengerjakan soal hingga waktu yang ditentukan selesai atau dapat mengakhiri ujian dengan cara klik tombol <strong>SELESAI</strong></li>
                    <li>Peserta yang keluar selama ujian berlangsung tanpa mengklik tombol <strong>SELESAI</strong> akan dianggap tidak mengikuti ujian</li>
                    <li>Berdoa terlebih dahulu sebelum mengerjakan soal</li>
                </ul>
            </div>
        </div>
        <br>
        <?php
            $saat_ini = date('Y-m-d H:i:s');
            $mulai = $row["tanggal"] . " " . $row["jam"];
            $selesai = date("Y-m-d H:i:s", strtotime("+" . $row['waktu'] . " minutes", strtotime($mulai)));

            // Check if the current time is within the exam period or if the exam is of type 2
            if (($saat_ini < $mulai && $row['tipe_soal'] != 2) || ($saat_ini > $selesai)) {
                if ($saat_ini < $mulai) {
                    echo "<div class='text-center'><div class='alert alert-info'><i class='fas fa-hourglass-half'></i> Ujian belum dimulai</div></div>";
                } else {
                    echo "<div class='text-center'><div class='alert alert-warning'><strong><i class='fas fa-ban'></i> Ujian telah selesai</strong></div></div>";
                }
            } else {
                // Cek apakah siswa sudah mengikuti jadwal ujian TERBARU
                // Riwayat lama (tanggal sebelum tanggal ujian saat ini) tidak memblokir
                $tanggal_ujian = $row['tanggal'];
                $hasil = mysqli_query($kon, "SELECT MAX(tanggal) AS terakhir FROM riwayat WHERE id_ujian='$id_ujian' AND id_siswa='$id_siswa'");
                $data_riwayat = mysqli_fetch_assoc($hasil);
                $cek = 0;
                if (!empty($data_riwayat['terakhir'])) {
                    if (strtotime($data_riwayat['terakhir']) >= strtotime($tanggal_ujian . ' 00:00:00')) {
                        $cek = 1;
                    }
                }

                // Jika sudah maka siswa tersebut tidak dapat mengikuti ujian tersebut
                if ($cek >= 1) {
                    echo "<div class='text-center'><div class='alert alert-success'><i class='fas fa-check-circle'></i> Anda telah mengikuti ujian ini</div></div>";
                } else {
                    if ($peserta == true) {
                        echo "<div class='text-center'><a href='pages/siswa/hasil/set-default.php?id=" . $id_ujian . "' class='tombol_mulai_ujian btn btn-primary btn-circle' ><i class='fas fa-pen-square'></i> Kerjakan Ujian</a></div>";
                    }
                }
            }
        }
        ?>
    </div>
</div>

<?php
// Membuat format tanggal
function tanggal($tanggal)
{
    $bulan = array(1 => 'Januari',
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
    // fungsi mulai ujian
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.tombol_mulai_ujian').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                const konfirmasi = confirm("Siap mengerjakan ujian ini?");
                if (!konfirmasi) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

<?php
// Redirect to the next exam after completing the current one
if (isset($_GET['id'])) {
    $current_id = $_GET['id'];
    $next_id_index = array_search($current_id, $id_ujian_list) + 1;

    if ($next_id_index < count($id_ujian_list)) {
        $next_id = $id_ujian_list[$next_id_index];
        echo "<script>window.location.href = 'http://localhost/ujian/index.php?page=review&id=$next_id';</script>";
    } else {
        echo "<div class='alert alert-info'>Tidak ada ujian berikutnya.</div>";
    }
}
?>
