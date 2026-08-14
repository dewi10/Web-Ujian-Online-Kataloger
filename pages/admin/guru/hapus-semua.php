<?php
    //Koneksi database
    include '../../../config/database.php';

    $guru=$_POST['guru'];

    for ($i=0; $i < count($guru) ; $i++){
        //Menghapus data dalam tabel guru
        mysqli_query($kon,"delete from guru where id_guru='$guru[$i]'");

    }

        
?>