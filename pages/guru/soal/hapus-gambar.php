<?php
    //Koneksi database
    include '../../../config/database.php';
    //Memulai soal
    mysqli_query($kon,"START TRANSACTION");

    $id_soal=$_GET['id_soal'];
    $gambar=$_GET['gambar'];
    $id=$_GET['id'];

    $sql="update soal set gambar='' where id_soal='$id_soal' ";
    $hapus_gambar=mysqli_query($kon,$sql);

    //Menghapus foto
    unlink("gambar/".$gambar);
        

    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($hapus_gambar) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../../index.php?page=input-soal&id=$id&hapus_gambar=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../../index.php?page=input-soal&id=$id&hapus_gambar=gagal");

    }

?>