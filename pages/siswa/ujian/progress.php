<?php
    session_start();
    $id_siswa=$_SESSION["id_siswa"];
    require_once __DIR__ . '/../../../config/database.php';
    $id_ujian = mysqli_real_escape_string($kon, (string) $_POST["id_ujian"]);

    $id_siswa_esc = mysqli_real_escape_string($kon, (string) $id_siswa);

    $q_total = mysqli_query($kon, "SELECT COUNT(*) AS c FROM hasil WHERE id_ujian='$id_ujian' AND id_siswa='$id_siswa_esc'");
    $row_total = mysqli_fetch_assoc($q_total);
    $jumlah_soal = (int) ($row_total['c'] ?? 0);

    // IFNULL: PG dengan id_jawaban NULL tidak terhitung; essay dari kolom `essay` atau `jawaban_essay`
    $sql_terjawab = "SELECT COUNT(*) AS c FROM hasil WHERE id_ujian='$id_ujian' AND id_siswa='$id_siswa_esc'
        AND (IFNULL(id_jawaban, 0) != 0 OR LENGTH(TRIM(IFNULL(essay, ''))) > 0)";
    $q_ter = mysqli_query($kon, $sql_terjawab);
    $row_ter = mysqli_fetch_assoc($q_ter);
    $jumlah_telah_dijawab = (int) ($row_ter['c'] ?? 0);

    $progress = 0;
    if ($jumlah_soal > 0) {
        $progress = (int) round(($jumlah_telah_dijawab / $jumlah_soal) * 100);
    }
?>

<div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
     role="progressbar"
     style="width: <?php echo max(0, min(100, $progress)); ?>%; min-width: 2.5em;"
     aria-valuenow="<?php echo $progress; ?>"
     aria-valuemin="0"
     aria-valuemax="100">
    <?php echo (int) $jumlah_telah_dijawab; ?>/<?php echo (int) $jumlah_soal; ?> soal (<?php echo $progress; ?>%)
</div>