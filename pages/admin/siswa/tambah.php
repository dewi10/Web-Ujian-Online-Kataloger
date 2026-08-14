<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

$kelas_data = array();

if (isset($_POST['tambah_siswa'])) {
    
    //Fungsi untuk mencegah inputan karakter yang tidak sesuai
    function input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    //Cek apakah ada kiriman form dari method post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //Memulai transaksi
        mysqli_query($kon,"START TRANSACTION");

        $nama=input($_POST["nama"]);
        $nis=input($_POST["nis"]);
        $jk=isset($_POST["jk"]) ? input($_POST["jk"]) : '';
        $email=isset($_POST["email"]) && !empty($_POST["email"]) ? input($_POST["email"]) : 'noemail@email.com';
        $no_telp=isset($_POST["no_telp"]) && !empty($_POST["no_telp"]) ? input($_POST["no_telp"]) : '0';
        $alamat=isset($_POST["alamat"]) && !empty($_POST["alamat"]) ? input($_POST["alamat"]) : '-';
        $kelas=input($_POST["kelas"]);
        $username=$nis;
        $password=md5($nis);

        $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
        $foto = $_FILES['foto']['name'];
        $x = explode('.', $foto);
        $ekstensi = strtolower(end($x));
        $ukuran	= $_FILES['foto']['size'];
        $file_tmp = $_FILES['foto']['tmp_name'];

        $foto_db = "foto_default.png"; // default

        if (!empty($foto)){
            if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
                //Mengupload gambar
                move_uploaded_file($file_tmp, 'foto/'.$foto);
                $foto_db = $foto;
            }
        }

        $sql="insert into siswa (id_siswa,kode_siswa,nama_siswa,id_kelas,nis,jk,email,no_telp,alamat,foto,username,password) values
            ('$nis','$nis','$nama','$kelas','$nis','$jk','$email','$no_telp','$alamat','$foto_db','$username','$password')";

        //Menyimpan ke tabel siswa
        $simpan_siswa=mysqli_query($kon,$sql);

        if ($simpan_siswa) {
            mysqli_query($kon,"COMMIT");
            $_SESSION['flash_tambah_siswa'] = [
                'status' => 'success',
                'message' => 'Data siswa berhasil ditambahkan!'
            ];
            header("Location:../../../index.php?page=siswa");
            exit;
        }
        else {
            mysqli_query($kon,"ROLLBACK");
            $error = mysqli_error($kon);
            $_SESSION['flash_tambah_siswa'] = [
                'status' => 'danger',
                'message' => 'Gagal menambah data: '.$error
            ];
            header("Location:../../../index.php?page=siswa");
            exit;
        }
    }
}
$hasil_kelas = mysqli_query($kon, "SELECT id_kelas, kode_kelas, nama_kelas FROM kelas ORDER BY kode_kelas, nama_kelas");
while ($row_kelas = mysqli_fetch_assoc($hasil_kelas)) {
    $kelas_data[] = $row_kelas;
}
?>

<form action="pages/admin/siswa/tambah.php" method="post" enctype="multipart/form-data">
    <div class="row">
        <!-- Kolom Kiri -->
        <div class="col-sm-6">
            <div class="form-group">
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukan Nama Lengkap" required>
            </div>
            <div class="form-group">
                <label>Nomor Induk Pegawai (NIP):</label>
                <input type="text" name="nis" class="form-control" id="nis" placeholder="Masukan Nomor Induk Pegawai" required>
                <div id="info_nis"></div>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" placeholder="Masukan Email">
            </div>
            <div class="form-group">
                <label>No Telp:</label>
                <input type="text" name="no_telp" class="form-control" placeholder="Masukan No Telp">
            </div>
            <div class="form-group">
                <label>Jenis Kelamin:</label>
                <div class="d-flex align-items-center" style="gap: 20px; margin-top: 4px;">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="jk" value="1" id="jk_l" required>
                        <label class="form-check-label" for="jk_l">Laki-laki</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="jk" value="2" id="jk_p" required>
                        <label class="form-check-label" for="jk_p">Perempuan</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Jabatan:</label>
                <select class="form-control" name="jabatan" id="jabatan" required>
                    <option value="">Pilih Jabatan</option>
                    <option value="Terampil">Terampil</option>
                    <option value="Ahli">Ahli</option>
                </select>
            </div>
            <div class="form-group d-none" id="wrap_jenjang">
                <label>Jenjang:</label>
                <select class="form-control" name="kelas" id="jenjang" required>
                    <option value="">Pilih Jabatan terlebih dahulu</option>
                </select>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-sm-6">
            <div class="form-group">
                <label>Alamat:</label>
                <textarea class="form-control" name="alamat" rows="5" id="alamat" placeholder="Masukan Alamat"></textarea>
            </div>
            <div class="form-group">
                <div id="msg"></div>
                <label>Foto:</label>
                <input type="file" name="foto" class="file">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" disabled placeholder="Upload Foto" id="file">
                    <div class="input-group-append">
                        <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih</button>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <img src="img/img80.png" id="preview" class="img-thumbnail" style="max-height:160px; object-fit:cover;">
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <button type="submit" name="tambah_siswa" id="submit" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
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
    const kelasData = <?php echo json_encode($kelas_data, JSON_UNESCAPED_UNICODE); ?>;

    function tampilkanJenjang(jabatan) {
        var jenjangDropdown = $('#jenjang');
        var wrapJenjang = $('#wrap_jenjang');

        jenjangDropdown.empty();

        if (!jabatan) {
            wrapJenjang.addClass('d-none');
            jenjangDropdown.append('<option value="">Pilih Jabatan terlebih dahulu</option>');
            return;
        }

        var dataJenjang = kelasData.filter(function(item) {
            return item.kode_kelas === jabatan;
        });

        jenjangDropdown.append('<option value="">Pilih Jenjang</option>');
        dataJenjang.forEach(function(item) {
            jenjangDropdown.append('<option value="' + item.id_kelas + '">' + item.nama_kelas + '</option>');
        });

        wrapJenjang.removeClass('d-none');
    }

    $(document).ready(function(){
        $('#jabatan').on('change', function() {
            tampilkanJenjang($(this).val());
        });

        $('form').on('reset', function() {
            setTimeout(function() {
                $('#wrap_jenjang').addClass('d-none');
                $('#jenjang').html('<option value="">Pilih Jabatan terlebih dahulu</option>');
            }, 0);
        });

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
    });
</script>
