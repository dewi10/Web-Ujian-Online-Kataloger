<?php 
    include '../../../config/database.php';
    $jawaban = mysqli_real_escape_string($kon, (string) ($_POST["jawaban_essay"] ?? ''));
    $id_soal = mysqli_real_escape_string($kon, (string) $_POST["id_soal"]);
    $id_siswa = mysqli_real_escape_string($kon, (string) $_POST["id_siswa"]);
    $id_ujian = mysqli_real_escape_string($kon, (string) ($_POST["id_ujian"] ?? ''));
    if ($id_ujian === '') {
        exit;
    }
    // Kolom `essay` dipakai get-jawaban-essay.php; id_jawaban=1 menandai essay pernah diisi
    $sql = "UPDATE hasil SET essay='$jawaban', id_jawaban='1'
        WHERE id_soal='$id_soal' AND id_siswa='$id_siswa' AND id_ujian='$id_ujian'";
    mysqli_query($kon, $sql);
?>
