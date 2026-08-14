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

      
        $kode_guru=input($_POST["kode_guru"]);
        $username=input($_POST["username"]);
  

        //Mengambil password
        $ambil_password=mysqli_query($kon,"select password from guru where kode_guru='$kode_guru' limit 1");
        $data = mysqli_fetch_array($ambil_password);

        if ($data['password']==$_POST["password"]){
            $password=input($_POST["password"]);
        }else {
            $password=md5(input($_POST["password"]));
        }
        
        $sql="update guru set
       username='$username',
       password='$password'
       where kode_guru='$kode_guru'";

        //Menyimpan ke tabel guru
        $simpan=mysqli_query($kon,$sql);

        //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
        if ($simpan) {
            mysqli_query($kon,"COMMIT");
            header("Location:../../../index.php?page=guru&akun=berhasil");
        }
        else {
            mysqli_query($kon,"ROLLBACK");
            header("Location:../../../index.php?page=guru&akun=gagal");
        }
        
    }

    //-------------------------------------------------------------------------------------------

    $kode_guru=$_POST["kode_guru"];
    include '../../../config/database.php';
    $query = mysqli_query($kon, "SELECT * FROM guru where kode_guru='$kode_guru'");
    $data = mysqli_fetch_array($query); 
    $username=$data['username'];
    $password=$data['password'];

    
    if ($username==null){
        echo"<div class='alert alert-warning'>Username dan password belum diatur.</div>";
    }

?>
<form action="pages/admin/guru/setting-akun.php" method="post">

<input name="kode_guru" value="<?php echo $kode_guru; ?>" type="hidden" class="form-control">

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

