<?php
    //Koneksi database
    include '../../../config/database.php';

    $siswa=$_POST['siswa'];

    for ($i=0; $i < count($siswa) ; $i++){
        //Menghapus data dalam tabel siswa
        mysqli_query($kon,"delete from siswa where id_siswa='$siswa[$i]'");

    }

        
?>