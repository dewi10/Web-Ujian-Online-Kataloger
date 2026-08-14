<?php
include '../../../config/database.php';
$id_ujian = $_POST["id_ujian"];

$query = "SELECT * FROM ujian u
    INNER JOIN kelas k ON u.id_kelas = k.id_kelas
    INNER JOIN mapel m ON m.id_mapel = u.id_mapel
    INNER JOIN guru g ON g.id_guru = u.id_guru
    WHERE u.id_ujian = '$id_ujian' LIMIT 1";
$hasil = mysqli_query($kon, $query);
$row = mysqli_fetch_array($hasil);

$cek = mysqli_num_rows($hasil);

if ($cek <= 0) {
    echo "<div class='alert alert-warning'>Belum ada data!</div>";
    exit;
}
?>
<div class="table-responsive">
    <table class="table table-border" id="tabel_ujian">
        <tbody>
        <tr>
            <td width="20%">Kategori</td>
            <td>: <?php echo $row['nama_kelas']; ?> </td>
        </tr>
        <tr>
            <td>Judul</td>
            <td>: <?php echo $row['judul']; ?> </td>
        </tr>
        <tr>
            <td>Mata Ujian</td>
            <td>: <?php echo $row['nama_mapel']; ?> </td>
        </tr>
        <tr>
            <td>Pengawas</td>
            <td>: <?php echo $row['nama_guru']; ?> </td>
        </tr>
        <tr>
            <td>Tanggal Ujian</td>
            <td>: <?php echo tanggal(date('Y-m-d', strtotime($row["tanggal"]))); ?> </td>
        </tr>
        <tr>
            <td>Jam</td>
            <td>: <?php echo date('H:i', strtotime($row["jam"])); ?> - <?php echo date("H:i", strtotime("+" . $row['waktu'] . " minutes", strtotime($row["jam"]))); ?> WIB</td>
        </tr>
        </tbody>
    </table>
</div>

<?php
if ($row['tipe_soal'] == 2) {
    // Tabel untuk tipe_soal = 2
    ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Soal / Jawaban</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT s.id_siswa, s.nis, s.nama_siswa, soal.soal, hasil.essay AS jawaban FROM siswa s
                INNER JOIN hasil ON hasil.id_siswa = s.id_siswa
                INNER JOIN soal ON soal.id_soal = hasil.id_soal
                WHERE hasil.id_ujian = '$id_ujian' AND hasil.essay IS NOT NULL";
            $hasil = mysqli_query($kon, $sql);
            $no = 0;
            $printed = [];

            while ($data = mysqli_fetch_array($hasil)) {
                $no++;
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data['nis']; ?></td>
                    <td><?php echo $data['nama_siswa']; ?></td>
                    <td>
                        <strong>Soal:</strong> <?php echo $data['soal']; ?><br>
                        <strong>Jawaban:</strong> <?php echo $data['jawaban']; ?>
                    </td>
                    <td>
                        <?php if (!in_array($data['id_siswa'], $printed)): $printed[] = $data['id_siswa']; ?>
                        <a href="pages/admin/hasil/cetak-siswa.php?id_ujian=<?php echo $id_ujian; ?>&id_siswa=<?php echo $data['id_siswa']; ?>" target="_blank" class="btn btn-info btn-circle" title="Print Hasil Siswa"><i class="fas fa-print"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    // Tabel untuk tipe_soal lainnya
    ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Nilai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM siswa s
                INNER JOIN nilai n ON n.id_siswa = s.id_siswa
                WHERE n.id_ujian = '$id_ujian'";
            $hasil = mysqli_query($kon, $sql);
            $no = 0;
            $rata = 0;
            $total_rata = 0;

            while ($data = mysqli_fetch_array($hasil)) {
                $no++;
                $nilai = $data['nilai'];

                $hasil2 = mysqli_query($kon, "SELECT nilai_kelulusan FROM ujian WHERE id_ujian = '$id_ujian'");
                $data2 = mysqli_fetch_array($hasil2);
                $nilai_kelulusan = $data2['nilai_kelulusan'];

                $rata += $nilai;
                $total_rata = $rata / mysqli_num_rows($hasil);
                $arr_nilai[] = $data['nilai'];

                $status = $nilai >= $nilai_kelulusan ? "<span class='text-success'>Kompeten</span>" : "<span class='text-warning'>Belum Kompeten</span>";
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data['nis']; ?></td>
                    <td><?php echo $data['nama_siswa']; ?></td>
                    <td><?php echo number_format($nilai, 2); ?></td>
                    <td><?php echo $status; ?></td>
                    <td>
                        <a href="pages/admin/hasil/cetak-siswa.php?id_ujian=<?php echo $id_ujian; ?>&id_siswa=<?php echo $data['id_siswa']; ?>" target="_blank" class="btn btn-info btn-circle" title="Print Hasil Siswa"><i class="fas fa-print"></i></a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="col-sm-4">
        <div class="table-responsive">
            <table class="table table-border">
                <tbody>
                <tr>
                    <td>Nilai Tertinggi</td>
                    <td>: <?php if (isset($arr_nilai)) echo max($arr_nilai); ?> </td>
                </tr>
                <tr>
                    <td>Nilai Terendah</td>
                    <td>: <?php if (isset($arr_nilai)) echo min($arr_nilai); ?></td>
                </tr>
                <tr>
                    <td>Nilai Rata-rata</td>
                    <td>: <?php echo $total_rata; ?> </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <?php
}
?>

<a href="pages/guru/hasil/export-pdf.php?id=<?php echo $id_ujian; ?>" target="_blank" class="btn_hapus btn btn-danger btn-circle">
    <i class="fas fa-file-pdf"></i> Export PDF
</a>
<a href="pages/guru/hasil/export-excel.php?id=<?php echo $id_ujian; ?>" target="_blank" class="btn_hapus btn btn-success btn-circle">
    <i class="fas fa-file-excel"></i> Export Excel
</a>

<?php
// Membuat format tanggal
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
