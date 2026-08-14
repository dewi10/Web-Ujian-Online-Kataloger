<?php
    //validasi hanya admin yang boleh mengakses halaman ini
    $username = $_SESSION['username'];
    $cek = mysqli_query ($kon,"select * from admin where username='".$username."' limit 1");
    $jum = mysqli_num_rows($cek);

    if ($jum<1){
        echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
        exit;
    }
?>
<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-user"></i> Profil</h3>
    </div>
    <div class="card-body">
    <?php
        include 'config/database.php';
        $query ="select * from admin where id_admin='".$_SESSION["id_admin"]."' limit 1"; 
        $hasil=mysqli_query($kon,$query);
        $row = mysqli_fetch_array($hasil);

        if (isset($_GET['edit'])) {
            if ($_GET['edit']=='berhasil'){
                echo"<div class='alert alert-success'><strong>Berhasil!</strong> Profil telah diupdate!</div>";
            }else if ($_GET['edit']=='gagal'){
                echo"<div class='alert alert-danger'><strong>Gagal!</strong> Profil gagal diupdate!</div>";
            }    
        }
    ?>


        <div class="table-responsive">
            <table class="table table-border" id="tabel_ujian">
                <tbody>
                <tr>
                    <td width="20%">Nama</td>  
                    <td>: <?php echo $row['nama_admin'];?></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td> <td>: <?php echo $row['jk'] == 1 ? 'Laki-laki' : 'Perempuan';?></td>
                </tr>
                <tr>
                    <td>Alamat</td>  <td>:  <?php echo $row['alamat'];  ?> </td>
                </tr>
                <tr>
                    <td>No Telp</td><td>:  <?php echo $row['no_telp'];  ?> </td>
                </tr>
                <tr>
                    <td>Email</td>  <td>:  <?php echo $row['email'];  ?> </td>
                </tr>

                </tbody>
            </table>
        </div>
        <br>

        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Edit</button>
 
    </div> 
</div>


<!-- The Modal -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Profil</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

        <?php 
            include 'config/database.php';
            $id_admin=$_SESSION["id_admin"];
            $sql="select * from admin where id_admin=$id_admin limit 1";
            $hasil=mysqli_query($kon,$sql);
            $data = mysqli_fetch_array($hasil); 
        ?>


        <form action="pages/admin/profil/edit.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="id_admin" value="<?php echo $data['id_admin'];?>" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Nama Lengkap:</label>
                        <input type="text" name="nama" value="<?php echo $data['nama_admin'];?>" class="form-control" placeholder="Masukan Nama Lengkap" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo $data['email'];?>" class="form-control" placeholder="Masukan Email" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>No Telp:</label>
                        <input type="text" name="no_telp" value="<?php echo $data['no_telp'];?>" class="form-control" placeholder="Masukan No Telp" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
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
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Alamat:</label>
                        <textarea class="form-control" name="alamat" rows="4" id="alamat"><?php echo $data['alamat'];?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
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
                        <img src="pages/admin/profil/foto/<?php echo $data['foto'];?>" id="preview" class="img-thumbnail">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo $data['username'];?>" class="form-control" placeholder="Masukan Username" required>
                        <div id="info_username"> </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" value="<?php echo $data['password'];?>" class="form-control" placeholder="Masukan Password" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <button type="submit" name="edit_admin" id="submit" class="btn btn-warning">Update</button>
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

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>





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

