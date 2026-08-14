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
            $id_jawaban=$_POST['id_jawaban'];
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

            
            $pilihan=$_POST['pilihan'];
            $id_pilihan=$_POST['id_pilihan'];

            for ($i=0; $i < count($pilihan) ; $i++){

                $sql="update jawaban set pilihan='$pilihan[$i]' where id_jawaban='$id_pilihan[$i]' ";
                $update_pilihan=mysqli_query($kon,$sql);
            }

            if ($id_jawaban!=null){
                $set_null = mysqli_query($kon,"update jawaban set jawaban='0' where id_soal='$id_soal'");
                $update_jawaban= mysqli_query($kon,"update jawaban set jawaban='1' where id_jawaban=$id_jawaban");

                if ($update_soal and $update_pilihan and $set_null and $update_jawaban) {
                    mysqli_query($kon,"COMMIT");
                    header("Location:../../../index.php?page=input-soal&id=$id_ujian&edit=berhasil");
                }
                else {
                    mysqli_query($kon,"ROLLBACK");
                    header("Location:../../../index.php?page=input-soal&id=$id_ujian&edit=gagal");
                }

            } else {
                if ($update_pilihan) {
                    mysqli_query($kon,"COMMIT");
                    header("Location:../../../index.php?page=input-soal&id=$id_ujian&edit=berhasil");
                }
                else {
                    mysqli_query($kon,"ROLLBACK");
                    header("Location:../../../index.php?page=input-soal&id=$id_ujian&edit=gagal");
                }
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

<form action="pages/guru/soal/edit.php" method="post" enctype="multipart/form-data" class="soal-form-compact">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <input type="hidden" name="id_ujian" class="form-control" value="<?php echo $_POST['id_ujian'];?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group mb-2">
                <textarea class="form-control" name="soal" rows="3"><?php echo $data['soal']; ?></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-7">
            <p class="mb-2">Masukan Pilihan Jawaban:</p>
            <?php 
                $alfabet = array("A", "B", "C", "D", "E");
                $sql="select * from jawaban where id_soal='$id_soal'";
                $get=mysqli_query($kon,$sql);
                $no=0;
                $kunci_default='';
                while ($row = mysqli_fetch_array($get)):
                    if ($row['jawaban']==1) {
                        $kunci_default = $row['id_jawaban'];
                    }
            ?>
            <div class="form-group mb-2">
                <div class="d-flex align-items-center">
                    <label class="mb-0 mr-2" style="width:18px;"><?php echo $alfabet[$no];?></label>
                    <textarea class="form-control opsi-jawaban" name="pilihan[]" rows="2"><?php echo $row['pilihan']; ?></textarea>
                    <input type="radio" class="jawaban_benar ml-2" id_jawaban="<?php echo $row['id_jawaban'];?>" <?php if ($row['jawaban']==1) echo "checked"; ?> name="jawaban_benar">
                </div>
            </div>
            <input type="hidden" name="id_pilihan[]" value="<?php echo $row['id_jawaban'];?>" class="form-control"/>
            <?php $no++; endwhile; ?>
        </div>

        <div class="col-sm-5">
            <div class="form-group mb-2">
                <div id="msg"></div>
                <label>Gambar: <small class="text-danger">Abaikan jika tidak menggunakan gambar</small></label>
                <input type="file" name="gambar" class="file" >
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
    <input type="hidden" id="id_jawaban" name="id_jawaban" value="<?php echo $kunci_default; ?>" class="form-control"/>
    <input type="hidden" id="id_ujian" name="id_ujian" value="<?php echo $_POST['id_ujian'];?>" class="form-control"/>

    <div class="row">
        <div class="col-sm-12 text-right">
            <button type="submit" name="edit_soal" id="Submit" class="btn btn-warning">Update</button>
        </div>
    </div>
</form>
<script>
    $('.jawaban_benar').on('click',function(){
        var id_jawaban = $(this).attr("id_jawaban");
        $('#id_jawaban').val(id_jawaban);
    });
</script>

<style>
    .file {
    visibility: hidden;
    position: absolute;
    }
    .soal-form-compact textarea.form-control {
        min-height: 46px;
    }
    .soal-form-compact .opsi-jawaban {
        min-height: 56px;
        resize: vertical;
    }
</style>

<script>
    $(document).on("click", "#pilih_gambar", function() {
    var file = $(this).parents().find(".file");
    file.trigger("click");
    });
    $('input[type="file"]').change(function(e) {
    if (!e.target.files || !e.target.files.length) return;
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

