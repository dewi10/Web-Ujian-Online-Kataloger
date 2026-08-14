<?php
//validasi hanya guru yang boleh mengakses halaman ini
$username = $_SESSION['username'];
$cek = mysqli_query ($kon,"select * from guru where username='".$username."' limit 1");
$jum = mysqli_num_rows($cek);

if ($jum<1){
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}
?>
<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-plus-circle"></i> Input Soal</h3>
    </div>
    <div class="card-body">


        <?php
            //Validasi untuk menampilkan pesan pemberitahuan saat user menambah ujian
            if (isset($_GET['tambah'])) {
                if ($_GET['tambah']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Soal telah ditambah!</div>";
                }else if ($_GET['tambah']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Soal gagal ditambahkan!</div>";
                }    
            }
            
            if (isset($_GET['edit'])) {
                if ($_GET['edit']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Soal telah diupdate!</div>";
                }else if ($_GET['edit']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Soal gagal diupdate!</div>";
                }    
            }
            if (isset($_GET['hapus'])) {
                if ($_GET['hapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Soal telah dihapus!</div>";
                }else if ($_GET['hapus']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Soal gagal dihapus!</div>";
                }    
            }

            if (isset($_GET['multiplehapus'])) {
                if ($_GET['multiplehapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Semua soal yang tercentang telah dihapus</div>";
                }   
            }

            if (isset($_GET['salin'])) {
                if ($_GET['salin']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Soal telah disalin.</div>";
                }   
            }

            if (isset($_GET['import'])) {
                if ($_GET['import']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Soal berhasil diimport.</div>";
                }else if ($_GET['import']=='gagal'){
                    $pesan = isset($_GET['pesan']) ? urldecode($_GET['pesan']) : 'Import soal gagal.';
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> ".$pesan."</div>";
                }
            }

            if (isset($_GET['hapus_gambar'])) {
                if ($_GET['hapus_gambar']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Gambar telah dihapus!</div>";
                }else if ($_GET['hapus_gambar']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Gambar gagal dihapus!</div>";
                }    
            }
        ?>

<?php
    include 'config/database.php';
    $id_ujian=addslashes(trim($_GET['id']));
    $id_guru=$_SESSION['id_guru'];
    $hasil=mysqli_query($kon,"select * from ujian u
    inner join kelas k on u.id_kelas=k.id_kelas
    inner join mapel m on m.id_mapel=u.id_mapel
    where id_ujian='$id_ujian' and id_guru='$id_guru' limit 1");

    $cek=mysqli_num_rows($hasil);

    if ($cek<=0){
        echo "<center><h5>Data tidak ditemukan</h5></center>";
        exit;
    }
    $row = mysqli_fetch_array($hasil);

?>

        <input type="hidden" name="id_ujian" value="<?php echo $id_ujian; ?>" id="id_ujian"/>
        <div class="table-responsive">
            <table class="table table-border" id="tabel_ujian">
                <tbody>
                <tr>
                    <td width="20%">Kategori</td>  <td><?php echo $row['nama_kelas']; ?> </td>
                </tr>
                <tr>
                    <td>Judul</td>  <td><?php echo $row['judul']; ?> </td>
                </tr>
                <tr>
                    <td>Tipe Soal</td> <td><?php echo $row['tipe_soal'] == 1 ? 'Pilihan Ganda' : 'Essay';?></td>
                </tr>
                <tr>
                    <td>Mata Ujian</td>  <td><?php echo $row['nama_mapel']; ?> </td>
                </tr>
                <tr>
                    <td>Tanggal</td>  <td><?php echo tanggal(date('Y-m-d', strtotime($row["tanggal"]))); ?> </td>
                </tr>
                <tr>
                    <td>Jam</td>  <td><?php echo date('H:i', strtotime($row["jam"])); ?> WIB</td>
                </tr>
                <tr>
                    <td>Waktu</td>  <td><?php echo $row['waktu']; ?> Menit</td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php if($row['tipe_soal']=='1'):?>
        <div class="mb-3" id="aksi_soal_buttons" style="display:none;">
            <button type="button" class="btn btn-danger mr-2" id_ujian="<?php echo addslashes(trim($_GET['id']));?>" id="btn_tambah" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Tambah</button>
             <button type="button" class="btn btn-success mr-2" tipe_soal="pg" id_ujian="<?php echo addslashes(trim($_GET['id']));?>" id="btn_salin">Salin Soal</button>
            <button type="button" class="btn btn-danger mr-2" id="btn_hapus">Hapus</button>
        </div>
        <form id="form_soal">
        <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Soal yang ditampilkan pada saat ujian dilaksanakan akan diurutkan berdasarkan nomor soal, sedangkan untuk pilihan jawaban setiap soal akan di random (acak).
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="tabel_soal">
                <thead class="thead-light">
                <tr>
                    <th width="5%" class="text-center">
                    <script>
                    function toggle(pilih) { 
                        
                        checkboxes = document.getElementsByName('soal[]');
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
                    <th width="6%">No</th>
                    <th width="55%">Soal</th>
                    <th>Jawaban</th>
                    <th width="10%">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    // include database
                    include 'config/database.php';
                    $sql="select * from soal where tipe='1' and id_ujian='$id_ujian'";
                    $hasil=mysqli_query($kon,$sql);
                    $no=0;
                    //Menampilkan data dengan perulangan while
                    while ($data = mysqli_fetch_array($hasil)):
                    $no++;

                 

                ?>
                <tr>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $no;?>" value="<?php echo $data['id_soal']; ?>"  name="soal[]" />
                            <label class="custom-control-label" for="customCheck<?php echo $no;?>"></label>
                        </div>
                    </td>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data['soal'];?>
                    <?php if ($data['gambar']!='') echo "<p> <img src='pages/guru/soal/gambar/".$data['gambar']."' width='50%' class='img-thumbnail'></p>"; ?>
                    </td>
                    <td>
                        <ol type="A">
                        <?php
                        $id_soal=$data['id_soal'];
                        $result=mysqli_query($kon,"select * from jawaban where id_soal='$id_soal'");
                        while ($get = mysqli_fetch_array($result)):
                        ?>
                        <li>
                        <?php 
                            if ($get['jawaban']==1){
                                echo "<span class='text-success'>".$get['pilihan']."</span";
                            }else {
                                echo $get['pilihan'];
                            }
                        ?>
                        </li>
                        <?php endwhile; ?>
                        </ol> 
                    </td>
                    <td>
                    <button type="button" class="btn_edit btn btn-warning btn-circle" no="<?php echo $no; ?>" id_soal="<?php echo $data['id_soal']; ?>" id_ujian="<?php echo $id_ujian; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></button>
                    <a href="pages/guru/soal/hapus.php?id_soal=<?php echo $data['id_soal']; ?>&id=<?php echo $id_ujian; ?>&gambar=<?php echo $data['gambar']; ?>&tipe=pg" class="btn_hapus btn btn-danger btn-circle" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </form>
        <?php endif; ?>

        <?php if($row['tipe_soal']=='2'):?>
            <div class="mb-3" id="aksi_soal_buttons" style="display:none;">
                <button type="button" class="btn btn-danger mr-2" id_ujian="<?php echo addslashes(trim($_GET['id']));?>" id="btn_tambah_soal_essay" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Tambah</button>
                <button type="button" class="btn btn-primary mr-2" tipe_soal="essay" id_ujian="<?php echo addslashes(trim($_GET['id']));?>" id="btn_import">Import Soal</button>
                <button type="button" class="btn btn-success mr-2" tipe_soal="essay" id_ujian="<?php echo addslashes(trim($_GET['id']));?>" id="btn_salin">Salin Soal</button>
                <button type="button" class="btn btn-danger mr-2" id="btn_hapus">Hapus</button>
            </div>
            <form id="form_soal">
            <div class="table-responsive">
            <table class="table table-bordered" id="tabel_soal">
                <thead class="thead-light">
                <tr>
                    <th width="5%" class="text-center">
                    <script>
                    function toggle(pilih) { 
                        
                        checkboxes = document.getElementsByName('soal[]');
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
                    <th width="6%">No</th>
                    <th width="55%">Soal</th>
                    <th width="10%">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    // include database
                    include 'config/database.php';
                    $sql="select * from soal where tipe='2' and id_ujian='$id_ujian'";
                    $hasil=mysqli_query($kon,$sql);
                    $no=0;
                    //Menampilkan data dengan perulangan while
                    while ($data = mysqli_fetch_array($hasil)):
                    $no++;
                ?>
                <tr>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $no;?>" value="<?php echo $data['id_soal']; ?>"  name="soal[]" />
                            <label class="custom-control-label" for="customCheck<?php echo $no;?>"></label>
                        </div>
                    </td>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data['soal'];?>
                    <?php if ($data['gambar']!='') echo "<p> <img src='pages/guru/soal/gambar/".$data['gambar']."' width='50%' class='img-thumbnail'></p>"; ?>
                    </td>
                    <td>
                    <button type="button" class="btn_edit_essay btn btn-warning btn-circle" no="<?php echo $no; ?>" id_soal="<?php echo $data['id_soal']; ?>" id_ujian="<?php echo $id_ujian; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></button>
                    <a href="pages/guru/soal/hapus.php?id_soal=<?php echo $data['id_soal']; ?>&id=<?php echo $id_ujian; ?>&gambar=<?php echo $data['gambar']; ?>&tipe=essay" class="btn_hapus_essay btn btn-danger btn-circle" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </form>
        <?php endif; ?> 
    </div> 
</div>
<script>
    $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<?php 
    //Membuat format tanggal
    function tanggal($tanggal)
    {
        $bulan = array (1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
    }

?>

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
  
        <div class="modal-footer"></div>

        </div>
    </div>
</div>

<script>

    $(document).ready( function () {
        $('#tabel_ujian').DataTable();
    } );

    // Salin Soal
    $('#btn_salin').on('click',function(){
        $('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
        var id_ujian = $(this).attr("id_ujian");
        var tipe_soal = $(this).attr("tipe_soal");
        $.ajax({
            url: 'pages/guru/soal/salin.php',
            method: 'post',
            data: {id_ujian:id_ujian,tipe_soal:tipe_soal},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Salin Soal';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });

    // Import Soal
    $('#btn_import').on('click',function(){
        $('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
        var id_ujian = $(this).attr("id_ujian");
        var tipe_soal = $(this).attr("tipe_soal");
        $.ajax({
            url: 'pages/guru/soal/import.php',
            method: 'post',
            data: {id_ujian:id_ujian,tipe_soal:tipe_soal},
            success:function(data){
                $('#tampil_data').html(data);
                document.getElementById("judul").innerHTML='Import Soal';
            }
        });
        $('#modal').modal('show');
    });


    $('#btn_hapus').click(function(){

        konfirmasi=confirm("Semua soal yang di centang akan dihapus, Apakah anda yakin ingin melakukan tindakan ini? ")
        if (konfirmasi){
            var form = $('#form_soal')[0];
            var data = new FormData(form);
            var id_ujian = $("#id_ujian").val();
            $.ajax({
                type	: 'POST',
                url: 'pages/guru/soal/hapus-semua.php',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                error: function (request, error) {
                    alert('Gagal menghapus soal');
                },
                success	: function(data){
                    window.location.href = "index.php?page=input-soal&id="+id_ujian+"&multiplehapus=berhasil";
                }
            });
        }else {

        }
    });

    // Tambah soal PG
    $('#btn_tambah').on('click',function(){
        $('.modal-dialog').removeClass('modal-md modal-lg').addClass('modal-xl');
        var id_ujian = $(this).attr("id_ujian");
        $.ajax({
            url: 'pages/guru/soal/tambah.php',
            method: 'post',
            data: {id_ujian:id_ujian},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah Soal Pilihan Ganda';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // Tambah soal essay
    $('#btn_tambah_soal_essay').on('click',function(){
        $('.modal-dialog').removeClass('modal-md modal-lg').addClass('modal-xl');
        var id_ujian = $(this).attr("id_ujian");
        $.ajax({
            url: 'pages/guru/soal/tambah-essay.php',
            method: 'post',
            data: {id_ujian:id_ujian},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah Soal Essay';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // fungsi untuk edit soal PG dan jawaban
    $('.btn_edit').on('click',function(){
        $('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
        var no = $(this).attr("no");
        var id_soal = $(this).attr("id_soal");
        var id_ujian = $(this).attr("id_ujian");
        $.ajax({
            url: 'pages/guru/soal/edit.php',
            method: 'post',
            data: {id_soal:id_soal,no:no,id_ujian:id_ujian},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit Soal Nomor '+no;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

        // fungsi untuk edit soal essay
        $('.btn_edit_essay').on('click',function(){
        $('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
        var no = $(this).attr("no");
        var id_soal = $(this).attr("id_soal");
        var id_ujian = $(this).attr("id_ujian");
        $.ajax({
            url: 'pages/guru/soal/edit-essay.php',
            method: 'post',
            data: {id_soal:id_soal,no:no,id_ujian:id_ujian},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit Soal Nomor '+no;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });


    // fungsi hapus soal (popup tanpa mengubah URL ke parameter hapus)
    $(document).on('click', '.btn_hapus, .btn_hapus_essay', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        if (!link) {
            return false;
        }
        link += (link.indexOf('?') !== -1 ? '&' : '?') + 'ajax=1';
        var id_ujian = $('#id_ujian').val();
        var konfirmasi = confirm("Yakin ingin menghapus data ini?");
        if (!konfirmasi) {
            return false;
        }

        $.ajax({
            url: link,
            method: 'GET',
            dataType: 'json',
            success: function(res){
                if (res && res.status === 'ok') {
                    alert('Berhasil! Soal telah dihapus!');
                    window.location.href = "index.php?page=input-soal&id=" + id_ujian;
                } else {
                    var pesan = (res && res.pesan) ? res.pesan : 'Soal gagal dihapus!';
                    alert('Gagal! ' + pesan);
                }
            },
            error: function(){
                alert('Gagal! Soal gagal dihapus!');
            }
        });
        return false;
    });


    $(document).ready(function() {
        $('#tabel_soal').DataTable( {
            "searching": true,
            "paging":   true,
            "ordering": false,
            "info":     true,
            "lengthChange": true,
            "initComplete": function () {
                var $length = $('#tabel_soal_wrapper .dataTables_length');
                var $aksi = $('#aksi_soal_buttons');
                if ($length.length && $aksi.length) {
                    $length.empty().append($aksi.children());
                    $length.addClass('mb-2');
                }
            }
        });
    });


</script>