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
        <h3><i class="fas fa-book"></i> Data Mata Ujian</h3>
    </div>
    <div class="card-body">
        <script> $('title').text('DATA MATA UJIAN'); </script>
        <?php
            $cek_kolom = mysqli_query($kon, "SHOW COLUMNS FROM mapel LIKE 'status_aktif'");
            if ($cek_kolom && mysqli_num_rows($cek_kolom) == 0) {
                mysqli_query($kon, "ALTER TABLE mapel ADD COLUMN status_aktif TINYINT(1) NOT NULL DEFAULT 1");
            }

            $tab_status = isset($_GET['tab']) ? $_GET['tab'] : 'aktif';
            if ($tab_status !== 'nonaktif') {
                $tab_status = 'aktif';
            }
            $status_filter = ($tab_status === 'nonaktif') ? 0 : 1;
        ?>
      
        <?php
            //Validasi untuk menampilkan pesan pemberitahuan saat user menambah mapel
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

            if (isset($_GET['status'])) {
                if ($_GET['status']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Status data berhasil diubah!</div>";
                }else if ($_GET['status']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Status data gagal diubah!</div>";
                }
            }
        ?>
        <div class="mb-3">
            <button type="button" class="btn btn-danger" id="btn_tambah" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;"><i class="fas fa-plus"></i> Tambah</button>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link <?php echo ($tab_status=='aktif') ? 'active' : ''; ?>" href="index.php?page=mapel&tab=aktif">Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($tab_status=='nonaktif') ? 'active' : ''; ?>" href="index.php?page=mapel&tab=nonaktif">Non Active</a>
            </li>
        </ul>

        <div class="table-responsive">
            <table class="table table-hover" id="tabel_mapel">
                <thead class="thead-light">
                <tr>
                    <th>KATEGORI</th>
                    <th>JENJANG</th>
                    <th>GOLONGAN RUANG</th>
                    <th>STATUS</th>
                    <th width="24%" style="min-width: 280px;">AKSI</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    // include database
                    include 'config/database.php';
                
                    $sql="select * from mapel where status_aktif='$status_filter'";
                    $hasil=mysqli_query($kon,$sql);
                    $no=0;
                    //Menampilkan data dengan perulangan while
                    while ($data = mysqli_fetch_array($hasil)):
                    $no++;
                ?>
                <tr>
                    <td style="font-weight: 600;"><?php echo $data['kode_mapel'];?></td>
                    <td><?php echo $data['nama_mapel'];?></td>
                    <td>-</td>
                    <td>
                        <?php if ((int)$data['status_aktif']===1): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Non Active</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap" style="min-width: 280px;">
                    <button class="btn_edit btn btn-warning btn-sm" id_mapel="<?php echo $data['id_mapel']; ?>" kode_mapel="<?php echo $data['kode_mapel']; ?>" ><i class="fa fa-edit"></i> Edit</button>
                    <?php if ((int)$data['status_aktif']===1): ?>
                    <a href="pages/admin/mapel/toggle-status.php?id_mapel=<?php echo $data['id_mapel']; ?>&status=0&tab=<?php echo $tab_status; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-toggle-off"></i> Nonaktifkan</a>
                    <?php else: ?>
                    <a href="pages/admin/mapel/toggle-status.php?id_mapel=<?php echo $data['id_mapel']; ?>&status=1&tab=<?php echo $tab_status; ?>" class="btn btn-primary btn-sm"><i class="fas fa-toggle-on"></i> Aktifkan</a>
                    <?php endif; ?>
                    <a href="pages/admin/mapel/hapus.php?id_mapel=<?php echo $data['id_mapel']; ?>" class="btn_hapus btn btn-danger btn-sm" ><i class="fas fa-trash"></i> Hapus</a>
                </td>
                </tr>
                <?php endwhile; ?>
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
        $('#tabel_mapel').DataTable( {
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

    // Tambah Mata Ujian
    $('#btn_tambah').on('click',function(){
        $.ajax({
            url: 'pages/admin/mapel/tambah.php',
            method: 'post',
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah Mata Ujian';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // fungsi edit mapel
    $('.btn_edit').on('click',function(){

        var id_mapel = $(this).attr("id_mapel");
        var kode_mapel = $(this).attr("kode_mapel");
        $.ajax({
            url: 'pages/admin/mapel/edit.php',
            method: 'post',
            data: {kode_mapel:kode_mapel,id_mapel:id_mapel},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit Mata Ujian #'+kode_mapel;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

    // fungsi hapus mapel
    $('.btn_hapus').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus data ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });


</script>