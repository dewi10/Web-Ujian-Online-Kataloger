<?php
    //Koneksi database
    include '../../../config/database.php';
    //Memulai siswa
    mysqli_query($kon,"START TRANSACTION");

    $id_siswa=$_GET['id_siswa'];
    
    //Menghapus data dalam tabel siswa
    $hapus_siswa=mysqli_query($kon,"delete from siswa where id_siswa='$id_siswa'");

    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($hapus_siswa) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../../index.php?page=siswa&hapus=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../../index.php?page=siswa&hapus=gagal");

    }

?>