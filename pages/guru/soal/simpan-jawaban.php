<?php 
    include '../../../config/database.php';
    mysqli_query($kon,"START TRANSACTION");

    $id_soal=$_POST['id_soal'];
    $id_jawaban=$_POST['id_jawaban'];
    $id_ujian=$_POST['id_ujian'];

    $set_null = mysqli_query($kon,"update jawaban set jawaban='0' where id_soal='$id_soal'");

    $update_jawaban= mysqli_query($kon,"update jawaban set jawaban='1' where id_jawaban=$id_jawaban");

    $is_ajax = isset($_POST['ajax']) && $_POST['ajax']=='1';

    if ($set_null and $update_jawaban) {
        mysqli_query($kon,"COMMIT");
        if ($is_ajax) {
            echo json_encode(array('status' => 'success', 'message' => 'Soal berhasil disimpan'));
            exit;
        }
        header("Location:../../../index.php?page=input-soal&id=$id_ujian&tambah=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        if ($is_ajax) {
            echo json_encode(array('status' => 'error', 'message' => mysqli_error($kon)));
            exit;
        }
        header("Location:../../../index.php?page=input-soal&id=$id_ujian&tambah=gagal");
    }

?>