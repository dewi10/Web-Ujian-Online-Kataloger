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
        <h3><i class="fas fa-cog"></i> Aplikasi</h3>
    </div>
    <div class="card-body">
    <?php
        include 'config/database.php';

        $query ="select * from aplikasi limit 1"; 
        $hasil=mysqli_query($kon,$query);
        $row = mysqli_fetch_array($hasil);
    ?>
        <div class="table-responsive">
            <table class="table table-border" id="tabel_ujian">
                <tbody>
                    <tr>
                        <td>Nama</td>  <td>: <?php echo $row['nama_aplikasi'];  ?></td>
                    </tr>
                    <tr>
                        <td>No Telp</td><td>:  <?php echo $row['no_telp'];  ?> </td>
                    </tr>
                    <tr>
                        <td>Email</td>  <td>:  <?php echo $row['email'];  ?> </td>
                    </tr>
                    <tr>
                        <td>Alamat</td>  <td>:  <?php echo $row['alamat'];  ?> </td>
                    </tr>
                    <tr>
                        <td>Website</td>  <td>:  <?php echo $row['website'];  ?> </td>
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
        <h4 class="modal-title">Edit Aplikasi</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form action="pages/admin/aplikasi/edit.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="id_aplikasi" value="<?php echo $row['id'];?>" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Nama:</label>
                        <input type="text" name="nama" value="<?php echo $row['nama_aplikasi'];?>" class="form-control" placeholder="Masukan Nama Aplikasi" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo $row['email'];?>" class="form-control" placeholder="Masukan Email" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>No Telp:</label>
                        <input type="text" name="no_telp" value="<?php echo $row['no_telp'];?>" class="form-control" placeholder="Masukan No Telp" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Alamat:</label>
                        <textarea class="form-control" name="alamat" rows="3" id="alamat"><?php echo $row['alamat'];?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Website:</label>
                        <input type="text" name="website" value="<?php echo $row['website'];?>" class="form-control" placeholder="Masukan URL Website" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div id="msg"></div>
                        <label>logo:</label>
                        <input type="file" name="logo_baru" class="file" >
                            <div class="input-group my-3">
                                <input type="text" class="form-control" disabled placeholder="Upload logo" id="file">
                                <div class="input-group-append">
                                        <button type="button" id="pilih_logo" class="browse btn btn-dark">Pilih</button>
                                </div>
                            </div>
                            <input type="text" name="logo_saat_ini" value="<?php echo $row['logo'];?>" class="form-control" />
                        <img src="pages/admin/aplikasi/logo/<?php echo $row['logo'];?>" id="preview" class="img-thumbnail">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <button type="submit" name="edit_aplikasi" id="Submit" class="btn btn-warning">Update</button>
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
            $(document).on("click", "#pilih_logo", function() {
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


