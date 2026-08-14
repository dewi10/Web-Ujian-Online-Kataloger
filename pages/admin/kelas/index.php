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
        <h3><i class="fas fa-th-large"></i> Data Kategori</h3>
    </div>
    <div class="card-body">
    <script> $('title').text('DATA KATEGORI'); </script>
     
        <?php
            //Validasi untuk menampilkan pesan pemberitahuan saat user menambah kelas
            if (isset($_GET['tambah'])) {
                if ($_GET['tambah']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah ditambah!</div>";
                }else if ($_GET['tambah']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal ditambahkan!</div>";
                }else if ($_GET['tambah']=='duplikat'){
                    echo"<div class='alert alert-warning'><strong>Duplikat!</strong> Kombinasi Kategori dan Jenjang sudah ada!</div>";
                }    
            }
            
            if (isset($_GET['edit'])) {
                if ($_GET['edit']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah diupdate!</div>";
                }else if ($_GET['edit']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal diupdate!</div>";
                }else if ($_GET['edit']=='duplikat'){
                    echo"<div class='alert alert-warning'><strong>Duplikat!</strong> Kombinasi Kategori dan Jenjang sudah ada!</div>";
                }    
            }
            if (isset($_GET['hapus'])) {
                if ($_GET['hapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah dihapus!</div>";
                }else if ($_GET['hapus']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal dihapus!</div>";
                }    
            }
        ?>

        <div class='alert alert-info'>Kategori yang memiliki jumlah siswa lebih dari 0 tidak dapat dihapus</div>

        <div class="mb-3">
            <button type="button" class="btn btn-danger" id="btn_tambah" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;"><i class="fas fa-plus"></i> Tambah</button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tabel_kelas">
                <thead class="thead-light">
                <tr>
                    <th>NO</th>
                    <th>KATEGORI</th>
                    <th>JENJANG </th>
                    <th>JUMLAH PESERTA</th>
                    <th width="15%">AKSI</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    // include database
                    include 'config/database.php';
                
                    $sql="select * from kelas order by kode_kelas, nama_kelas";
                    $hasil=mysqli_query($kon,$sql);
                    $no=0;
                    $prev_kode = '';
                    $kategori_data = array();
                    
                    // Kelompokkan data berdasarkan kategori
                    while ($data = mysqli_fetch_array($hasil)) {
                        $kategori_data[$data['kode_kelas']][] = $data;
                    }
                    
                    // Tampilkan data dengan rowspan
                    foreach($kategori_data as $kode_kelas => $items) {
                        $rowspan = count($items);
                        $first = true;
                        
                        foreach($items as $data) {
                            $no++;
                            $query=mysqli_query($kon,"select id_siswa from siswa where id_kelas='".$data['id_kelas']."'");
                            $jumlah_siswa = mysqli_num_rows($query);
                ?>
                <tr>
                    <td style="border: 1px solid #dee2e6;"><?php echo $no; ?></td>
                    <?php if($first): ?>
                    <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; font-weight: bold; border: 1px solid #dee2e6; background-color: #f8f9fa;"><?php echo $kode_kelas; ?></td>
                    <?php $first = false; endif; ?>
                    <td style="border: 1px solid #dee2e6;"><?php echo $data['nama_kelas'];?></td>
                    <td style="border: 1px solid #dee2e6;"><?php echo $jumlah_siswa; ?></td>
                    <td style="border: 1px solid #dee2e6;">
                    <button class="btn_edit btn btn-warning btn-circle" id_kelas="<?php echo $data['id_kelas']; ?>" kode_kelas="<?php echo $data['kode_kelas']; ?>" ><i class="fa fa-edit"></i></button>
                    <a href="pages/admin/kelas/hapus.php?id_kelas=<?php echo $data['id_kelas']; ?>"   <?php if ($jumlah_siswa>=1) echo "onclick='return false'";?> class="btn_hapus btn btn-danger btn-circle" ><i class="fas fa-trash"></i></a>
                </td>
                </tr>
                <?php 
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
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
        $('#tabel_kelas').DataTable( {
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

    // Tambah Kategori
    $('#btn_tambah').on('click',function(){
        $.ajax({
            url: 'pages/admin/kelas/tambah.php',
            method: 'post',
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah Kategori';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // fungsi edit kelas
    $('.btn_edit').on('click',function(){

        var id_kelas = $(this).attr("id_kelas");
        var kode_kelas = $(this).attr("kode_kelas");
        $.ajax({
            url: 'pages/admin/kelas/edit.php',
            method: 'post',
            data: {kode_kelas:kode_kelas,id_kelas:id_kelas},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit kelas #'+kode_kelas;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

    // fungsi hapus kelas
    $('.btn_hapus').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus data ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });


</script>