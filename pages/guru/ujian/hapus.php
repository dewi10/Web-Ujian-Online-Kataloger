<?php
    //Koneksi database
    include '../../../config/database.php';
    //Memulai mapel
    mysqli_query($kon,"START TRANSACTION");

    $id_ujian=addslashes(trim($_GET['id']));

    //Menghapus data dalam tabel ujian, soal dan hasil yang memiliki id_ujian yang saling berhubungan
    
    $hasil=mysqli_query($kon,"select id_soal from soal where id_ujian='$id_ujian'");
    $row = mysqli_fetch_array($hasil);
    $id_soal=$row['id_soal'];
    $hapus_jawaban=mysqli_query($kon,"delete from jawaban where id_soal='$id_soal'");

    
    $hapus_ujian=mysqli_query($kon,"delete from ujian where id_ujian='$id_ujian'");
    $hapus_soal=mysqli_query($kon,"delete from soal where id_ujian='$id_ujian'");
    $hapus_hasil=mysqli_query($kon,"delete from hasil where id_ujian='$id_ujian'");
    $hapus_nilai=mysqli_query($kon,"delete from nilai where id_ujian='$id_ujian'");
    $hapus_riwayat=mysqli_query($kon,"delete from riwayat where id_ujian='$id_ujian'");


    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($hapus_ujian and $hapus_soal and $hapus_hasil and $hapus_jawaban and $hapus_nilai and $hapus_riwayat) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../../index.php?page=ujian&hapus=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../../index.php?page=ujian&hapus=gagal");

    }

?>