<?php 
    include '../../../config/database.php';
    $id_jawaban = mysqli_real_escape_string($kon, (string) $_POST["id_jawaban"]);
    $id_soal = mysqli_real_escape_string($kon, (string) $_POST["id_soal"]);
    $id_siswa = mysqli_real_escape_string($kon, (string) $_POST["id_siswa"]);
    $id_ujian = mysqli_real_escape_string($kon, (string) ($_POST["id_ujian"] ?? ''));
    if ($id_ujian === '') {
        exit;
    }
    $sql = "UPDATE hasil SET id_jawaban='$id_jawaban' WHERE id_soal='$id_soal' AND id_siswa='$id_siswa' AND id_ujian='$id_ujian'";
    mysqli_query($kon, $sql);

?>

