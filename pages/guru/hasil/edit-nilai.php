<?php

    if (isset($_POST['edit_nilai'])) {
        
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

            $id_nilai=input($_POST["id_nilai"]);
            $nilai=input($_POST["nilai"]);
            $id_ujian=input($_POST["id_ujian"]);
    

            $sql="update nilai set
            nilai='$nilai'
            where id_nilai='$id_nilai'";
    
            //Menyimpan ke tabel nilai
            $simpan_nilai=mysqli_query($kon,$sql);


            if ($simpan_nilai) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=hasil-ujian&edit-nilai=berhasil&id_ujian=".$id_ujian);
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=hasil-ujian&edit-nilai=gagal&id_ujian=".$id_ujian);
            }
        }
    }
?>

<?php 
    include '../../../config/database.php';
    $id_nilai=$_POST["id_nilai"];
    $sql="select * from nilai where id_nilai='$id_nilai' limit 1";
    $hasil=mysqli_query($kon,$sql);
    $data = mysqli_fetch_array($hasil); 
?>


<form action="pages/guru/hasil/edit-nilai.php" method="post">

    <div class="row">
        <div class="col-sm-7">
            <div class="form-group">
                <input type="hidden" name="id_nilai" value="<?php echo $data['id_nilai'];?>" class="form-control">
                <input type="hidden" name="id_ujian" value="<?php echo $data['id_ujian'];?>" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Nilai:</label>
                <input type="text" name="nilai" value="<?php echo $data['nilai'];?>" class="form-control" placeholder="Masukan Nama Lengkap" required>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-4">
            <button type="submit" name="edit_nilai" id="submit" class="btn btn-warning">Update</button>
        </div>
    </div>
</form>
