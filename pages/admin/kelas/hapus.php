<?php
    //Koneksi database
    include '../../../config/database.php';
    //Memulai kelas
    mysqli_query($kon,"START TRANSACTION");

    $id_kelas=$_GET['id_kelas'];
    
    //Menghapus data dalam tabel kelas
    $hapus_kelas=mysqli_query($kon,"delete from kelas where id_kelas='$id_kelas'");

    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($hapus_kelas) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../../index.php?page=kelas&hapus=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../../index.php?page=kelas&hapus=gagal");

    }

?>