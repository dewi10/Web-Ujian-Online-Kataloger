<?php
session_start();
    if (isset($_POST['submit'])) {
        //Include file koneksi, untuk koneksikan ke database
        include '../../../config/database.php';

        //Memulai transaksi
        mysqli_query($kon,"START TRANSACTION");
        
        //Fungsi untuk mencegah inputan karakter yang tidak sesuai
        function input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

      
        $kode_siswa=input($_POST["kode_siswa"]);
        $username=input($_POST["username"]);
  

        //Mengambil password
        $ambil_password=mysqli_query($kon,"select password from siswa where kode_siswa='$kode_siswa' limit 1");
        $data = mysqli_fetch_array($ambil_password);

        if ($data['password']==$_POST["password"]){
            $password=input($_POST["password"]);
        }else {
            $password=md5(input($_POST["password"]));
        }
        
        $sql="update siswa set
       username='$username',
       password='$password'
       where kode_siswa='$kode_siswa'";

        //Menyimpan ke tabel siswa
        $simpan=mysqli_query($kon,$sql);

        //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
        if ($simpan) {
            mysqli_query($kon,"COMMIT");
            header("Location:../../../index.php?page=siswa&akun=berhasil");
        }
        else {
            mysqli_query($kon,"ROLLBACK");
            header("Location:../../../index.php?page=siswa&akun=gagal");
        }
        
    }

    //-------------------------------------------------------------------------------------------

    $kode_siswa=$_POST["kode_siswa"];
    include '../../../config/database.php';
    $query = mysqli_query($kon, "SELECT * FROM siswa where kode_siswa='$kode_siswa'");
    $data = mysqli_fetch_array($query); 
    $username=$data['username'];
    $password=$data['password'];

    
    if ($username==null){
        echo"<div class='alert alert-warning'>Username dan password belum diatur.</div>";
    }

?>
<form action="pages/admin/siswa/setting-akun.php" method="post">

<input name="kode_siswa" value="<?php echo $kode_siswa; ?>" type="hidden" class="form-control">

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Username:</label>
                <input name="username" value="<?php echo $username; ?>"  id="username" type="text" class="form-control" placeholder="Masukan username" required>
                <div id="info_username"> </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Password:</label>
                <input name="password" value="<?php echo $password; ?>" type="password" class="form-control" placeholder="Masukan password" required>
            </div>
        </div>
    </div>
    <br>
    <button type="submit" name="submit" id="submit" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit</button>
</form>


<script>
    //Cek ketersediaan username
    $("#username").bind('keyup', function () {

        var username = $('#username').val();

        $.ajax({
            url: 'cek-username.php',
            method: 'POST',
            data:{username:username},
            success:function(data){
                $('#info_username').show();
                $('#info_username').html(data);
            }
        }); 

    });

    $("#username").bind('change', function () {

        var username = $('#username').val();

        $.ajax({
            url: 'cek-username.php',
            method: 'POST',
            data:{username:username},
            success:function(data){
                $('#info_username').show();
                $('#info_username').html(data);
            }
        }); 
    });
</script>
