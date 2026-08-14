<?php
    header('Content-Type: application/json');
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
        $kode_soal=input($_POST["kode_soal"]);
        $soal=input($_POST["soal"]);
        
        // Validasi soal tidak boleh kosong
        if (empty($soal)){
            mysqli_query($kon,"ROLLBACK");
            echo json_encode(array('status' => 'error', 'message' => 'Soal tidak boleh kosong'));
            exit;
        }

        $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
        $gambar = $_FILES['gambar']['name'];
        $x = explode('.', $gambar);
        $ekstensi = strtolower(end($x));
        $ukuran	= $_FILES['gambar']['size'];
        $file_tmp = $_FILES['gambar']['tmp_name'];

        $gambar_db='';
        if (!empty($gambar)){
            if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
                //Mengupload gambar
                move_uploaded_file($file_tmp, 'gambar/'.$gambar);
                $gambar_db=$gambar;
            } else {
                mysqli_query($kon,"ROLLBACK");
                echo json_encode(array('status' => 'error', 'message' => 'Format gambar tidak valid'));
                exit;
            }
        }

        $sql="insert into soal (kode_soal,soal,id_ujian,gambar,tipe) values
        ('$kode_soal','$soal','$id_ujian','$gambar_db','2')";
    
        //Menyimpan ke tabel soal
        $simpan_soal=mysqli_query($kon,$sql);

        if ($simpan_soal){
            $id_soal_baru = mysqli_insert_id($kon);
            mysqli_query($kon,"COMMIT");
            echo json_encode(array(
                'status' => 'ok',
                'id_soal' => $id_soal_baru,
                'soal' => $soal,
                'gambar' => $gambar_db,
                'id_ujian' => $id_ujian
            ));
        } else {
            mysqli_query($kon,"ROLLBACK");
            echo json_encode(array('status' => 'error', 'message' => mysqli_error($kon)));
        }

    }
    
?>