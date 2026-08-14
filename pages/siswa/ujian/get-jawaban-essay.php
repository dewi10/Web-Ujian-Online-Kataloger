<?php
    session_start(); 
    include '../../../config/database.php';
    $id_soal = mysqli_real_escape_string($kon, (string) $_POST['id_soal']);
    $id_siswa = mysqli_real_escape_string($kon, (string) $_POST['id_siswa']);
    $id_ujian = mysqli_real_escape_string($kon, (string) ($_POST['id_ujian'] ?? ''));
    if ($id_ujian === '') {
        exit;
    }
    $query = mysqli_query($kon, "SELECT * FROM hasil WHERE id_soal='$id_soal' AND id_siswa='$id_siswa' AND id_ujian='$id_ujian' LIMIT 1");
    if (!$query || mysqli_num_rows($query) === 0) {
        exit;
    }
    $data = mysqli_fetch_array($query);
    if (!$data) {
        exit;
    }
    $t = trim((string) ($data['essay'] ?? ''));
    if ($t !== '') {
        echo $data['essay'];
        exit;
    }
    echo $data['jawaban_essay'] ?? '';
?>

