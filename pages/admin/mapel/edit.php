<?php

    if (isset($_POST['edit_mapel'])) {
        
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

            $id_mapel=input($_POST["id_mapel"]);
            $nama_mapel=input($_POST["nama_mapel"]);
      
            $sql="update mapel set
            nama_mapel='$nama_mapel'
            where id_mapel=$id_mapel";
        
            //Menyimpan ke tabel mapel
            $simpan_mapel=mysqli_query($kon,$sql);

            if ($simpan_mapel) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=mapel&edit=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=mapel&edit=gagal");
            }
        }
    }
?>

<?php 
    include '../../../config/database.php';
    $id_mapel=$_POST["id_mapel"];
    $sql="select * from mapel where id_mapel=$id_mapel limit 1";
    $hasil=mysqli_query($kon,$sql);
    $data = mysqli_fetch_array($hasil); 
?>

<form action="pages/admin/mapel/edit.php" method="post">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <input type="hidden" name="id_mapel" value="<?php echo $data['id_mapel'];?>" class="form-control" >
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Nama Mata Ujian:</label>
                <input type="text" name="nama_mapel" value="<?php echo $data['nama_mapel'];?>" class="form-control" placeholder="Masukan Nama mapel" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <button type="submit" name="edit_mapel" id="Submit" class="btn btn-warning">Update</button>
        </div>
    </div>
</form>