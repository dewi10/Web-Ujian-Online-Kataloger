<?php

    if (isset($_POST['edit_aplikasi'])) {
        
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
            //Memulai transaksi
            mysqli_query($kon,"START TRANSACTION");

            $id_aplikasi=input($_POST["id_aplikasi"]);
            $nama=input($_POST["nama"]);
            $email=input($_POST["email"]);
            $no_telp=input($_POST["no_telp"]);
            $alamat=input($_POST["alamat"]);
            $website=input($_POST["website"]);

            $logo_saat_ini=$_POST['logo_saat_ini'];
            $logo_baru = $_FILES['logo_baru']['name'];
            $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
            $x = explode('.', $logo_baru);
            $ekstensi = strtolower(end($x));
            $ukuran	= $_FILES['logo_baru']['size'];
            $file_tmp = $_FILES['logo_baru']['tmp_name'];


            if (!empty($logo_baru)){
                if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
                    //Mengupload logo baru
                    move_uploaded_file($file_tmp, 'logo/'.$logo_baru);
    
                    //Menghapus logo lama
                    unlink("logo/".$logo_saat_ini);
                    
                    $sql="update aplikasi set
                    nama_aplikasi='$nama',
                    email='$email',
                    no_telp='$no_telp',
                    alamat='$alamat',
                    website='$website',
                    logo='$logo_baru'
                    where id=$id_aplikasi";
                }
            }else {
                $sql="update aplikasi set
                nama_aplikasi='$nama',
                email='$email',
                no_telp='$no_telp',
                alamat='$alamat',
                website='$website'
                where id=$id_aplikasi";
            }


            //Menyimpan ke tabel aplikasi
            $simpan_aplikasi=mysqli_query($kon,$sql);

            if ($simpan_aplikasi) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=aplikasi&edit=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=aplikasi&edit=berhasil");
            }
        }
    }
?>