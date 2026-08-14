<?php
session_start();
    if (isset($_POST['edit_guru'])) {
        
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

            $id_guru=input($_POST["id_guru"]);
            $nama=input($_POST["nama"]);
            $nip=input($_POST["nip"]);
            $jk=input($_POST["jk"]);
            $email=input($_POST["email"]);
            $no_telp=input($_POST["no_telp"]);
            $alamat=input($_POST["alamat"]);
  
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
                    
                    $sql="update guru set
                    id_guru='$nip',
                    kode_guru='$nip',
                    nama_guru='$nama',
                    nip='$nip',
                    jk='$jk',
                    email='$email',
                    no_telp='$no_telp',
                    alamat='$alamat',
                    foto='$foto_baru'
                    where id_guru=$id_guru";
                }
            }else {
                $sql="update guru set
                id_guru='$nip',
                kode_guru='$nip',
                nama_guru='$nama',
                nip='$nip',
                jk='$jk',
                email='$email',
                no_telp='$no_telp',
                alamat='$alamat'
                where id_guru=$id_guru";
            }


            //Menyimpan ke tabel guru
            $simpan_guru=mysqli_query($kon,$sql);

            $sql="update ujian set
            id_guru='$nip'
            where id_guru=$id_guru";

            $tabel_ujian=mysqli_query($kon,$sql);


            if ($simpan_guru and $tabel_ujian) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=guru&edit=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=guru&edit=berhasil");
            }
        }
    }
?>

<?php 
    include '../../../config/database.php';
    $id_guru=$_POST["id_guru"];
    $sql="select * from guru where id_guru=$id_guru limit 1";
    $hasil=mysqli_query($kon,$sql);
    $data = mysqli_fetch_array($hasil); 
?>


<form action="pages/admin/guru/edit.php" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-sm-7">
            <div class="form-group">
                <input type="hidden" name="id_guru" value="<?php echo $data['id_guru'];?>" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-7">
            <div class="form-group">
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" value="<?php echo $data['nama_guru'];?>" class="form-control" placeholder="Masukan Nama Lengkap" required>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
            <label>Nomor Induk Pegawai (NIP):</label>
                <input type="text" name="nip" id="nip" value="<?php echo $data['nip'];?>" class="form-control" placeholder="Masukan Nomor Induk Pegawai" required>
                <div id="info_nip"> </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $data['email'];?>" class="form-control" placeholder="Masukan Email" required>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>No Telp:</label>
                <input type="text" name="no_telp" value="<?php echo $data['no_telp'];?>" class="form-control" placeholder="Masukan No Telp" required>
            </div>
        </div>
        <div class="col-sm-5">
        <div class="form-group">
                <label>Jenis Kelamin:</label>
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" <?php if (isset($data['jk']) && $data['jk']==1) echo "checked"; ?> name="jk" value="1" required>Laki-laki
                    </label>
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" <?php if (isset($data['jk']) && $data['jk']==2) echo "checked"; ?> name="jk" value="2" required>Perempuan
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <div class="form-group">
                <label>Alamat:</label>
                <textarea class="form-control" name="alamat" rows="4" id="alamat"><?php echo $data['alamat'];?></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-7">
            <div class="form-group">
                <div id="msg"></div>
                <label>Foto:</label>
                <input type="file" name="foto_baru" class="file" >
                    <div class="input-group my-3">
                        <input type="text" class="form-control" disabled placeholder="Upload Foto" id="file">
                        <div class="input-group-append">
                                <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih</button>
                        </div>
                    </div>
                    <input type="hidden" name="foto_saat_ini" value="<?php echo $data['foto'];?>" class="form-control" />
                <img src="pages/guru/foto/<?php echo $data['foto'];?>" id="preview" class="img-thumbnail">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <button type="submit" name="edit_guru" id="submit" class="btn btn-warning">Update</button>
        </div>
    </div>
</form>

<style>
    .file {
    visibility: hidden;
    position: absolute;
    }
</style>

<script>
    $(document).on("click", "#pilih_foto", function() {
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


<script>
    //Cek ketersediaan nip
    $("#nip").bind('keyup', function () {

        var nip = $('#nip').val();

        $.ajax({
            url: 'cek-nip.php',
            method: 'POST',
            data:{nip:nip},
            success:function(data){
                $('#info_nip').show();
                $('#info_nip').html(data);
            }
        }); 

    });

    $("#nip").bind('change', function () {

        var nip = $('#nip').val();

        $.ajax({
            url: 'cek-nip.php',
            method: 'POST',
            data:{nip:nip},
            success:function(data){
                $('#info_nip').show();
                $('#info_nip').html(data);
            }
        }); 
    });
</script>
