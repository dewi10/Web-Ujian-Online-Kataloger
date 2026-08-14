<?php
    session_start();
    require_once __DIR__ . '/../../../config/database.php';
    $id = mysqli_real_escape_string($kon, (string) ($_GET['id'] ?? ''));
    $jumlah_soal=0;
    $no=0;
    $id_siswa=$_SESSION["id_siswa"];

    $urutan_soal = array();
    if (isset($_SESSION['urutan_soal']) && isset($_SESSION['urutan_soal'][$id]) && is_array($_SESSION['urutan_soal'][$id])) {
        $urutan_soal = $_SESSION['urutan_soal'][$id];
    }

    if (count($urutan_soal) === 0) {
        $id_siswa_esc = mysqli_real_escape_string($kon, (string) $id_siswa);
        $hasil_default = mysqli_query($kon, "select id_soal from hasil where id_ujian='$id' and id_siswa='$id_siswa_esc' order by id_soal asc");
        while ($row_default = mysqli_fetch_assoc($hasil_default)) {
            $urutan_soal[] = (int)$row_default['id_soal'];
        }
    }

    $jumlah_soal = count($urutan_soal);
?>
<div class="table-responsive">
    <ul class="pagination">
    <?php  
        foreach ($urutan_soal as $index => $id_soal):
            $no = $index + 1;
            $q_soal = mysqli_query($kon, "select id_soal, kode_soal from soal where id_soal='$id_soal' and id_ujian='$id' limit 1");
            $row = mysqli_fetch_array($q_soal);
            if (!$row) {
                continue;
            }
            
            $id_esc = mysqli_real_escape_string($kon, (string) $id);
            $id_soal_esc = mysqli_real_escape_string($kon, (string) $id_soal);
            $id_siswa_esc = mysqli_real_escape_string($kon, (string) $id_siswa);
            $result = mysqli_query($kon, "SELECT * FROM hasil WHERE id_soal='$id_soal_esc' AND id_siswa='$id_siswa_esc' AND id_ujian='$id_esc' LIMIT 1");
            $get = mysqli_fetch_array($result);
            
            $sudah_dijawab = false;
            if ($get) {
                $idj = isset($get['id_jawaban']) ? (int) $get['id_jawaban'] : 0;
                $je = isset($get['jawaban_essay']) ? trim((string) $get['jawaban_essay']) : '';
                $es = isset($get['essay']) ? trim((string) $get['essay']) : '';
                if ($idj !== 0 || $je !== '' || $es !== '') {
                    $sudah_dijawab = true;
                }
            }
            
    ?>
        <li class="pilih_nomor_soal page-item <?php echo $sudah_dijawab ? 'soal-sudah-dijawab' : ''; ?>" kode_soal="<?php echo htmlspecialchars($row['kode_soal']); ?>" id_soal="<?php echo (int) $row['id_soal']; ?>" id_ujian="<?php echo htmlspecialchars($id); ?>" nomor="<?php echo (int) $no; ?>"><a class="page-link" href="#"><?php echo $no; ?></a></li>
    <?php endforeach; ?>
    </ul>
</div>
<style>
/* Warna biru jelas untuk soal yang sudah dijawab (pagination BS sering samar untuk .active) */
.soal-sudah-dijawab .page-link {
    background-color: #007bff !important;
    color: #fff !important;
    border-color: #007bff !important;
    font-weight: 600;
}
.soal-sudah-dijawab .page-link:hover {
    background-color: #0069d9 !important;
    color: #fff !important;
}
</style>