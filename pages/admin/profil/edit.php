<?php
    session_start();
    if (isset($_POST['edit_admin'])) {
        
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

            $id_admin=input($_POST["id_admin"]);
            $nama=input($_POST["nama"]);
            $jk=input($_POST["jk"]);
            $email=input($_POST["email"]);
            $no_telp=input($_POST["no_telp"]);
            $alamat=input($_POST["alamat"]);
            $username=input($_POST["username"]);

            
            //Mengambil password
            $ambil_password=mysqli_query($kon,"select password from admin where id_admin='$id_admin' limit 1");
            $data = mysqli_fetch_array($ambil_password);

            if ($data['password']==$_POST["password"]){
                $password=input($_POST["password"]);
            }else {
                $password=md5(input($_POST["password"]));
            }

            $foto_saat_ini=$_POST['foto_saat_ini'];
            $foto_baru = $_FILES['foto_baru']['name'];
            $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
            $x = explode('.', $foto_baru);
            $ekstensi = strtolower(end($x));
            $ukuran	= $_FILES['foto_baru']['size'];
            $file_tmp = $_FILES['foto_baru']['tmp_name'];


            if (!empty($foto_baru)){
                if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
                    //Mengupload foto baru
                    move_uploaded_file($file_tmp, 'foto/'.$foto_baru);
    
                    //Menghapus foto lama, foto yang dihapus selain foto default
                    if ($foto_saat_ini!='foto_default.png'){
                        unlink("foto/".$foto_saat_ini);
                    }
                    
                    $sql="update admin set
                    nama_admin='$nama',
                    jk='$jk',
                    email='$email',
                    no_telp='$no_telp',
                    alamat='$alamat',
                    foto='$foto_baru',
                    username='$username',
                    password='$password'
                    where id_admin=$id_admin";
                }
            }else {
                $sql="update admin set
                nama_admin='$nama',
                jk='$jk',
                email='$email',
                no_telp='$no_telp',
                alamat='$alamat',
                username='$username',
                password='$password'
                where id_admin=$id_admin";
            }


            //Menyimpan ke tabel admin
            $simpan_admin=mysqli_query($kon,$sql);

            if ($simpan_admin) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=profil&edit=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=profil&edit=berhasil");
            }
        }
    }
?>