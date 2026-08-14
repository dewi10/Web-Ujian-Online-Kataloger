<?php

    //Fungsi untuk mencegah inputan karakter yang tidak sesuai
    function input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    //Cek apakah ada kiriman form dari method post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        include '../../../config/database.php';
        mysqli_query($kon,"START TRANSACTION");
 
        $id_ujian=input($_POST["id_ujian"]);
        $id_soal=input($_POST["id_soal"]);
        $soal=input($_POST["soal"]);
        $kunci=isset($_POST["kunci"]) ? (int)$_POST["kunci"] : 0;

        $query = mysqli_query($kon, "SELECT max(id_soal) as id_terbesar FROM soal");
        $ambil= mysqli_fetch_array($query);
        $id_soal = $ambil['id_terbesar'];
        $id_soal++;
        $huruf = "S";
        $kode_soal = $huruf . sprintf("%03s", $id_soal);

        $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
        $gambar = $_FILES['gambar']['name'];
        $x = explode('.', $gambar);
        $ekstensi = strtolower(end($x));
        $ukuran	= $_FILES['gambar']['size'];
        $file_tmp = $_FILES['gambar']['tmp_name'];

        $query = mysqli_query($kon, "SELECT tipe_soal  FROM ujian where id_ujian='$id_ujian'");
        $get= mysqli_fetch_array($query);
        $tipe=$get['tipe_soal'];

        $gambar_db='';
        if (!empty($gambar)){
            if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
                //Mengupload gambar
                move_uploaded_file($file_tmp, 'gambar/'.$gambar);
                $gambar_db=$gambar;
            } else {
                mysqli_query($kon,"ROLLBACK");
                echo "error: Format gambar tidak valid";
                exit;
            }
        }

        $sql="insert into soal (kode_soal,soal,id_ujian,gambar,tipe) values
        ('$kode_soal','$soal','$id_ujian','$gambar_db','$tipe')";
    
        //Menyimpan ke tabel soal
        $simpan_soal=mysqli_query($kon,$sql);

        if (!$simpan_soal){
            mysqli_query($kon,"ROLLBACK");
            echo "error: ".mysqli_error($kon);
            exit;
        }

        //Mengambil id soal dari kode soal
        $hasil=mysqli_query($kon,"select id_soal from soal where kode_soal='$kode_soal' limit 1");
        $row = mysqli_fetch_array($hasil);
        $id_soal=$row['id_soal'];
      
        $pilihan=$_POST['pilihan'];

        $simpan_pilihan=true;
        for ($i=0; $i < count($pilihan) ; $i++){
            $jawaban = (($i+1)==$kunci) ? 1 : 0;
            $sql="insert into jawaban (pilihan,jawaban,id_soal) values ('$pilihan[$i]','$jawaban','$id_soal')";
            $simpan_pilihan=mysqli_query($kon,$sql);
            if (!$simpan_pilihan){
                break;
            }
        }

        if ($simpan_soal && $simpan_pilihan){
            mysqli_query($kon,"COMMIT");
            echo "ok";
        } else {
            mysqli_query($kon,"ROLLBACK");
            echo "error: ".mysqli_error($kon);
        }

    }
    
?>