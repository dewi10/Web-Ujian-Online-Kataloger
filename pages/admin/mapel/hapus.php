<?php
    //Koneksi database
    include '../../../config/database.php';
    //Memulai mapel
    mysqli_query($kon,"START TRANSACTION");

    $id_mapel=$_GET['id_mapel'];
    
    //Menghapus data dalam tabel mapel
    $hapus_mapel=mysqli_query($kon,"delete from mapel where id_mapel='$id_mapel'");

    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($hapus_mapel) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../../index.php?page=mapel&hapus=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../../index.php?page=mapel&hapus=gagal");

    }

?>