<?php

    if (isset($_POST['edit_soal'])) {
        
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
            $soal=$_POST['soal'];
            $id_soal=$_POST['id_soal'];
            $id_ujian=$_POST['id_ujian'];

            $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
            $gambar = $_FILES['gambar']['name'];
            $x = explode('.', $gambar);
            $ekstensi = strtolower(end($x));
            $ukuran	= $_FILES['gambar']['size'];
            $file_tmp = $_FILES['gambar']['tmp_name'];
    
            if (!empty($gambar)){
                if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
                    //Mengupload gambar
                    move_uploaded_file($file_tmp, 'gambar/'.$gambar);
                    //Sql jika menggunakan gambar
                    $sql="update soal set soal='$soal',gambar='$gambar' where id_soal='$id_soal'";
                }
            }else {
                //Sql jika tidak menggunakan gambar
                $sql="update soal set soal='$soal' where id_soal='$id_soal'";
            }

            //Menyimpan ke tabel soal
            $update_soal=mysqli_query($kon,$sql);


            if ($update_soal) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=input-soal&id=$id_ujian&edit=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=input-soal&id=$id_ujian&edit=gagal");
            }
            
        }
    }
?>


<?php 
    include '../../../config/database.php';
    $id_soal=$_POST["id_soal"];
    $sql="select * from soal where id_soal='$id_soal' limit 1";
    $hasil=mysqli_query($kon,$sql);
    $data = mysqli_fetch_array($hasil); 
?>

<form action="pages/guru/soal/edit-essay.php" method="post" enctype="multipart/form-data" class="soal-form-compact">

    <div class="row">
        <div class="col-sm-7">
            <div class="form-group mb-2">
                <label>Masukan Soal:</label>
                <textarea class="form-control" name="soal" rows="4"><?php echo $data['soal']; ?></textarea>
            </div>
        </div>

        <div class="col-sm-5">
            <div class="form-group mb-2">
                <div id="msg"></div>
                <label>Gambar: <small class="text-danger">Abaikan jika tidak menggunakan gambar</small></label>
                <input type="file" name="gambar" class="file">
                <div class="input-group my-2">
                    <input type="text" class="form-control" disabled placeholder="Upload gambar" id="file">
                    <div class="input-group-append">
                        <button type="button" id="pilih_gambar" class="browse btn btn-dark">Pilih</button>
                    </div>
                </div>
                <?php 
                    if ($data['gambar']==''){
                        echo "<img src='img/img80.png' id='preview' class='img-thumbnail' style='max-height:90px;'>";
                    }else {
                        $gambar=$data['gambar'];
                        echo "<img src='pages/guru/soal/gambar/$gambar' id='preview' class='img-thumbnail' style='max-height:90px;'>";
                        echo "<div class='mt-2'><a href='pages/guru/soal/hapus-gambar.php?id_soal=".$id_soal."&gambar=".$gambar."&id=".$_POST['id_ujian']."' class='btn btn-danger btn-sm' role='button'>Hapus gambar</a></div>";
                    }
                ?>
            </div>
        </div>
    </div>
    
    <input type="hidden" id="id_soal" name="id_soal" value="<?php echo $id_soal;?>" class="form-control"/>
    <input type="hidden" id="id_ujian" name="id_ujian" value="<?php echo $_POST['id_ujian'];?>" class="form-control"/>

    <div class="row">
        <div class="col-sm-12 text-right">
            <button type="submit" name="edit_soal" id="Submit" class="btn btn-warning">Update</button>
        </div>
    </div>
</form>


<style>
    .file {
    visibility: hidden;
    position: absolute;
    }
    .soal-form-compact textarea.form-control {
        min-height: 46px;
    }
</style>

<script>
    $(document).on("click", "#pilih_gambar", function() {
    var file = $(this).parents().find(".file");
    file.trigger("click");
    });
    $('input[type="file"]').change(function(e) {
    var fileName = e.target.files[0].name;
    $("#file").val(fileName);

    var reader = new FileReader();
    reader.onload = function(e) {
        // get loaded data and render thumbnail.
        document.getElementById("preview").src = e.target.result;
    };
    // read the image file as a data URL.
    reader.readAsDataURL(this.files[0]);
    });

</script>

