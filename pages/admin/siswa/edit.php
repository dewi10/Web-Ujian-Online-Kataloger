<?php
session_start();
    if (isset($_POST['edit_siswa'])) {
        
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

            $id_siswa=input($_POST["id_siswa"]);
            $nama=input($_POST["nama"]);
            $nis=input($_POST["nis"]);
            $jk=input($_POST["jk"]);
            $email=input($_POST["email"]);
            $no_telp=input($_POST["no_telp"]);
            $alamat=input($_POST["alamat"]);
            $kelas=input($_POST["kelas"]);
            $username=$nis;
            $password=md5($nis);
  
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
                    
                    $sql="update siswa set
                    id_siswa='$nis',
                    kode_siswa='$nis',
                    nama_siswa='$nama',
                    nis='$nis',
                    username='$username',
                    password='$password',
                    id_kelas='$kelas',
                    jk='$jk',
                    email='$email',
                    no_telp='$no_telp',
                    alamat='$alamat',
                    foto='$foto_baru'
                    where id_siswa=$id_siswa";
                }
            }else {
                $sql="update siswa set
                id_siswa='$nis',
                kode_siswa='$nis',
                nama_siswa='$nama',
                nis='$nis',
                username='$username',
                password='$password',
                id_kelas='$kelas',
                jk='$jk',
                email='$email',
                no_telp='$no_telp',
                alamat='$alamat'
                where id_siswa=$id_siswa";
            }


            //Menyimpan ke tabel siswa
            $simpan_siswa=mysqli_query($kon,$sql);

            //Update nis pada tabel hasil
            $sql="update hasil set
            id_siswa='$nis'
            where id_siswa=$id_siswa";
            $hasil_siswa=mysqli_query($kon,$sql);

            //Update nis pada tabel nilai
            $sql="update nilai set
            id_siswa='$nis'
            where id_siswa=$id_siswa";
            $nilai_siswa=mysqli_query($kon,$sql);


            //Update nis pada tabel peserta
            $sql="update peserta set
            id_siswa='$nis'
            where id_siswa=$id_siswa";
            $peserta_siswa=mysqli_query($kon,$sql);

            //Update nis pada tabel riwayat
            $sql="update riwayat set
            id_siswa='$nis'
            where id_siswa=$id_siswa";
            $riwayat_siswa=mysqli_query($kon,$sql);


            if ($simpan_siswa and  $hasil_siswa and  $nilai_siswa and  $peserta_siswa and $riwayat_siswa) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=siswa&edit=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=siswa&edit=berhasil");
            }
        }
    }
?>

<?php 
    include '../../../config/database.php';
    $id_siswa=$_POST["id_siswa"];
    $sql="select * from siswa where id_siswa=$id_siswa limit 1";
    $hasil=mysqli_query($kon,$sql);
    $data = mysqli_fetch_array($hasil); 
?>


<form action="pages/admin/siswa/edit.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id_siswa" value="<?php echo $data['id_siswa'];?>" class="form-control">
    <input type="hidden" name="foto_saat_ini" value="<?php echo $data['foto'];?>" class="form-control" />

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" value="<?php echo $data['nama_siswa'];?>" class="form-control" placeholder="Masukan Nama Lengkap" required>
            </div>
            <div class="form-group">
                <label>Nomor Induk Pegawai (NIP):</label>
                <input type="text" name="nis" id="nis" value="<?php echo $data['nis'];?>" class="form-control" placeholder="Masukan Nomor Induk Pegawai" required>
                <div id="info_nis"></div>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $data['email'];?>" class="form-control" placeholder="Masukan Email">
            </div>
            <div class="form-group">
                <label>No Telp:</label>
                <input type="text" name="no_telp" value="<?php echo $data['no_telp'];?>" class="form-control" placeholder="Masukan No Telp">
            </div>
            <div class="form-group">
                <label>Jenis Kelamin:</label>
                <div class="d-flex align-items-center" style="gap: 20px; margin-top: 4px;">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" <?php if (isset($data['jk']) && $data['jk']==1) echo "checked"; ?> name="jk" value="1" id="jk_l" required>
                        <label class="form-check-label" for="jk_l">Laki-laki</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" <?php if (isset($data['jk']) && $data['jk']==2) echo "checked"; ?> name="jk" value="2" id="jk_p" required>
                        <label class="form-check-label" for="jk_p">Perempuan</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Jabatan:</label>
                <select class="form-control" name="kelas">
                <?php
                    include '../../../config/database.php';
                    $sql="select * from kelas";
                    $hasil=mysqli_query($kon,$sql);
                    while ($row = mysqli_fetch_array($hasil)) {
                ?>
                    <option value="<?php echo $row['id_kelas'];?>" <?php  if ($row['id_kelas']==$data['id_kelas']) echo "selected";?> ><?php echo $row['nama_kelas'];?></option>
                <?php
                    }
                ?>
                </select>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Alamat:</label>
                <textarea class="form-control" name="alamat" rows="5" id="alamat"><?php echo $data['alamat'];?></textarea>
            </div>
            <div class="form-group">
                <div id="msg"></div>
                <label>Foto:</label>
                <input type="file" name="foto_baru" class="file">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" disabled placeholder="Upload Foto" id="file">
                    <div class="input-group-append">
                        <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih</button>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <img src="pages/admin/siswa/foto/<?php echo $data['foto'];?>" id="preview" class="img-thumbnail" style="max-height:160px; object-fit:cover;">
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <button type="submit" name="edit_siswa" id="submit" class="btn btn-warning">Update</button>
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
    //Cek ketersediaan nis
    $("#nis").bind('keyup', function () {

        var nis = $('#nis').val();

        $.ajax({
            url: 'cek-nis.php',
            method: 'POST',
            data:{nis:nis},
            success:function(data){
                $('#info_nis').show();
                $('#info_nis').html(data);
            }
        }); 

    });

    $("#nis").bind('change', function () {

        var nis = $('#nis').val();

        $.ajax({
            url: 'cek-nis.php',
            method: 'POST',
            data:{nis:nis},
            success:function(data){
                $('#info_nis').show();
                $('#info_nis').html(data);
            }
        }); 
    });
</script>

