<?php
    //Koneksi database
    include '../../../config/database.php';
    //Memulai guru
    mysqli_query($kon,"START TRANSACTION");

    $id_guru=$_GET['id_guru'];
    
    //Menghapus data dalam tabel guru
    $hapus_guru=mysqli_query($kon,"delete from guru where id_guru='$id_guru'");

    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($hapus_guru) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../../index.php?page=guru&hapus=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../../index.php?page=guru&hapus=gagal");

    }

?>