<?php
    //Koneksi database
    include '../../../config/database.php';

    $soal=$_POST['soal'];

    for ($i=0; $i < count($soal) ; $i++){
        //Menghapus data dalam tabel soal
        mysqli_query($kon,"delete from soal where id_soal='$soal[$i]'");

        //Menghapus data dalam tabel jawaban
        mysqli_query($kon,"delete from jawaban where id_soal='$soal[$i]'");
    }
    
    //Menghapus foto
    //unlink("gambar/".$gambar);
        
?>