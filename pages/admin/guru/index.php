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
        <h3><i class="fas fa-user-tie"></i> Data Pengawas</h3>
    </div>
    <div class="card-body">
        <script> $('title').text('DATA PENGAWAS'); </script>
        <?php
            //Validasi untuk menampilkan pesan pemberitahuan saat user menambah guru
            if (isset($_GET['tambah'])) {
                if ($_GET['tambah']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah ditambah!</div>";
                }else if ($_GET['tambah']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal ditambahkan!</div>";
                }    
            }
            
            if (isset($_GET['edit'])) {
                if ($_GET['edit']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah diupdate!</div>";
                }else if ($_GET['edit']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal diupdate!</div>";
                }    
            }
            if (isset($_GET['hapus'])) {
                if ($_GET['hapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah dihapus!</div>";
                }else if ($_GET['hapus']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal dihapus!</div>";
                }    
            }
            if (isset($_GET['akun'])) {
                if ($_GET['akun']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Username & password telah diatur</div>";
                }else if ($_GET['akun']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Username & password gagal diatur</div>";
                }    
            }
        ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button type="button" class="btn btn-danger" id="btn_tambah" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;"><i class="fas fa-plus"></i> Tambah</button>
                <button type="button" class="btn btn-outline-danger ml-2" id="btn_hapus"><i class="fas fa-trash"></i> Hapus</button>
            </div>
        </div>

        <form id="form_guru">
        <div class="table-responsive">
            <table class="table table-hover" id="tabel_guru">
                <thead class="thead-light">
                <tr>
                <th width="5%" class="text-center">
                    <script>
                    function toggle(pilih) { 
                        
                        checkboxes = document.getElementsByName('guru[]');
                        for(var i=0, n=checkboxes.length;i<n;i++)
                        { 
                            checkboxes[i].checked = pilih.checked;
                        } 
                    } 
                    </script>
            
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkall"  onClick="toggle(this)"  />
                            <label class="custom-control-label" for="checkall"></label>
                        </div>
                    </th>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Telp</th>
                    <th>Email</th>
                    <th width="15%">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    // include database
                    include 'config/database.php';
                
                    $sql="select * from guru";
                    $hasil=mysqli_query($kon,$sql);
                    $no=0;
                    //Menampilkan data dengan perulangan while
                    while ($data = mysqli_fetch_array($hasil)):
                    $no++;
                ?>
                <tr>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $no;?>" value="<?php echo $data['id_guru']; ?>"  name="guru[]" />
                            <label class="custom-control-label" for="customCheck<?php echo $no;?>"></label>
                        </div>
                    </td>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data['nip'];?></td>
                    <td><?php echo $data['nama_guru'];?></td>
                    <td><?php echo $data['no_telp'];?></td>
                    <td><?php echo $data['email'];?></td>
                    <td>
                    <button type="button" class="setting_akun btn btn-primary btn-circle" id_guru="<?php echo $data['id_guru']; ?>"  kode_guru="<?php echo $data['kode_guru']; ?>" ><i class="fas fa-user"></i></button>
                    <button type="button" class="btn_edit btn btn-warning btn-circle" id_guru="<?php echo $data['id_guru']; ?>" kode_guru="<?php echo $data['kode_guru']; ?>" ><i class="fa fa-edit"></i></button>
                    <a href="pages/admin/guru/hapus.php?id_guru=<?php echo $data['id_guru']; ?>" class="btn_hapus btn btn-danger btn-circle" ><i class="fas fa-trash"></i></a>
                </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </form>
    </div> 
</div>

<div class="modal fade" id="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        <div class="modal-header" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); color: white;">
            <h4 class="modal-title" id="judul"></h4>
            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
        </div>

        <div class="modal-body">
            <div id="tampil_data">
                 <!-- Data akan di load menggunakan AJAX -->                   
            </div>  
        </div>
  
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('#tabel_guru').DataTable( {
            "searching": true,
            "paging":   true,
            "ordering": true,
            "info":     true,
            dom: 'Bfrtip',
            buttons: ['excel','print','copy']
        });
    });
</script>

<script>

    // Tambah guru
    $('#btn_tambah').on('click',function(){
        $.ajax({
            url: 'pages/admin/guru/tambah.php',
            method: 'post',
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah Pengawas';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // fungsi edit guru
    $('.btn_edit').on('click',function(){

        var id_guru = $(this).attr("id_guru");
        var kode_guru = $(this).attr("kode_guru");
        $.ajax({
            url: 'pages/admin/guru/edit.php',
            method: 'post',
            data: {kode_guru:kode_guru,id_guru:id_guru},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit Pengawas #'+kode_guru;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

    $('.setting_akun').on('click',function(){
        var id_guru = $(this).attr("id_guru");
        var kode_guru = $(this).attr("kode_guru");
        $.ajax({
            url: 'pages/admin/guru/setting-akun.php',
            method: 'post',
            data: {kode_guru:kode_guru,id_guru:id_guru},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Setting Akun';
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

    // fungsi hapus guru
    $('.btn_hapus').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus data ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });

    $('#btn_hapus').click(function(){

        konfirmasi=confirm("Semua pengawas yang di centang akan dihapus, Apakah anda yakin ingin melakukan tindakan ini? ")
        if (konfirmasi){
            var form = $('#form_guru')[0];
            var data = new FormData(form);

            $.ajax({
                type	: 'POST',
                url: 'pages/admin/guru/hapus-semua.php',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                error: function (request, error) {
                    alert('Gagal menghapus');
                },
                success	: function(data){
                    window.location.href = "index.php?page=guru&multiplehapus=berhasil";
                }
            });
        }else {

        }
    });


</script>