<?php

    include '../../../config/database.php';

    $id_ujian=$_POST['id_ujian'];
    $id_kelas=$_POST['id_kelas'];

    $hasil=mysqli_query($kon,"select * from peserta where id_kelas='".$id_kelas."' and id_ujian='".$id_ujian."'");

    $jumlah_row = mysqli_num_rows($hasil);

    if ($jumlah_row==0){
        //Jika belum pernah di input sebelumnya
        $status=$_POST['stat'];
        $id_siswa=$_POST['id_siswa'];
        for ($i=0; $i < count($id_siswa) ; $i++){

            if ($status[$i]=='0'){
                continue;
            }
             $sql1="insert into peserta (id_siswa,id_kelas,id_ujian) values
             ('$id_siswa[$i]','$id_kelas','$id_ujian')";
             mysqli_query($kon,$sql1);
         }
    }else {
        //Jika sudah ada hapus data sebelumnya
        mysqli_query($kon,"delete from peserta where id_kelas='$id_kelas' and id_ujian='$id_ujian'");

        $status=$_POST['stat'];
        $id_siswa=$_POST['id_siswa'];
        for ($i=0; $i < count($id_siswa) ; $i++){

            if ($status[$i]=='0'){
                continue;
            }
            
             $sql1="insert into peserta (id_siswa,id_kelas,id_ujian) values
             ('$id_siswa[$i]','$id_kelas','$id_ujian')";
             mysqli_query($kon,$sql1);
         }
    }



?>