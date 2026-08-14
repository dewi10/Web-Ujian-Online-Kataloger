<?php
//validasi hanya guru yang boleh mengakses halaman ini
$username = $_SESSION['username'];
$cek = mysqli_query ($kon,"select * from guru where username='".$username."' limit 1");
$jum = mysqli_num_rows($cek);

if ($jum<1){
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}

$get = mysqli_fetch_assoc($cek);
?>
<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-clipboard-list"></i> Daftar Ujian</h3>
    </div>
    <div class="card-body">
    <script> $('title').text('DAFTAR UJIAN <?php echo strtoupper($get['nama_guru'])?>'); </script>
        <button type="button" class="btn btn-danger" id="btn_tambah" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Tambah Baru</button>
        <input type="hidden" id="status_tab" value="1">

        <ul class="nav nav-tabs mt-3" id="tab_status_ujian" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-status="1">Soal Aktif</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="0">Soal Nonaktif</a>
            </li>
        </ul>
        
        <div class="row mt-4">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Kategori:</label>
                    <select id="kelas" class="select2" multiple="multiple" style="width: 100%" name="kelas[]">
                        <?php
                            $id_guru = $_SESSION["id_guru"];
                            include 'config/database.php';
                                $sql="SELECT DISTINCT k.kode_kelas
                                FROM ujian u
                                INNER JOIN kelas k ON k.id_kelas=u.id_kelas
                                WHERE u.id_guru='$id_guru' AND k.kode_kelas<>''
                                ORDER BY k.kode_kelas";
                            $hasil=mysqli_query($kon,$sql);

                            // fallback jika guru belum punya data ujian / query kosong
                            if ($hasil && mysqli_num_rows($hasil) === 0) {
                                $sql="SELECT DISTINCT kode_kelas FROM kelas WHERE kode_kelas<>'' ORDER BY kode_kelas";
                                $hasil=mysqli_query($kon,$sql);
                            }

                            while ($hasil && $data = mysqli_fetch_array($hasil)) {
                                ?>
                            <option value="<?php echo htmlspecialchars($data['kode_kelas']);?>"><?php echo htmlspecialchars($data['kode_kelas']);?></option>
                                    <?php
                                }
                            ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Mata Ujian:</label>
                    <select id="mapel" class="select2" multiple="multiple" style="width: 100%" name="mapel[]">
                    <?php
                            $sql="SELECT DISTINCT m.* FROM mapel m 
                            LEFT JOIN ujian u ON m.id_mapel=u.id_mapel AND u.id_guru='".$id_guru."' 
                            WHERE m.status_aktif=1
                            ORDER BY m.nama_mapel";
                        $hasil=mysqli_query($kon,$sql);
                        while ($data = mysqli_fetch_array($hasil)) {
                            ?>
                        <option value="<?php echo $data['id_mapel'];?>"><?php echo $data['nama_mapel'];?> </option>
                        <?php
                        }
                    ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <input type="button" class="btn btn-warning d-block" id="tampilkan" value="Tampilkan" style="width: 150px;">
                </div>
            </div>
        </div>
        <hr>
        <?php
            //Validasi untuk menampilkan pesan pemberitahuan saat user menambah ujian
            if (isset($_GET['tambah'])) {
                if ($_GET['tambah']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah ditambah!</div>";
                }else if ($_GET['tambah']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal ditambahkan!</div>";
                }    
            }

            if (isset($_GET['peserta'])) {
                if ($_GET['peserta']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Pengaturan peserta ujian telah disimpan!</div>";
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
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Status ujian telah diubah.</div>";
                }else if ($_GET['status']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Status ujian gagal diubah.</div>";
                }
            }
        ?>

        <div class="row">
            <div class="col-sm-12">
            <div id="tampil_tabel"></div>
            </div>
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

    $(document).ready( function () {
        tabel_ujian();
        $(".select2").select2({
        });

        // Load mata ujian berdasarkan kategori yang dipilih
        $('#kelas').on('change', function(){
            var kelas_ids = $(this).val();
            loadMapelByKelas(kelas_ids);
        });
    });

    function loadMapelByKelas(kelas_ids){
        $.ajax({
            url: 'pages/guru/ujian/get-mapel.php',
            method: 'post',
            dataType: "json",
            data: {kelas_ids: kelas_ids},
            success:function(data){
                $('#mapel').empty();
                if(data.length > 0){
                    $.each(data, function(index, item){
                        $('#mapel').append('<option value="'+item.id_mapel+'">'+item.nama_mapel+'</option>');
                    });
                }
                $('#mapel').trigger('change');
            }
        });
    }

       
    $('#tampilkan').on('click',function(){
        tabel_ujian();
    }); 


    function tabel_ujian(){
        var mapel=$("#mapel").val();
        var kelas=$("#kelas").val();
        var status_tab=$("#status_tab").val();
        $.ajax({
            url: 'pages/guru/ujian/tabel-ujian.php',
            method: 'post',
            dataType: "html",
            async : false,
            data: {mapel:mapel,kelas:kelas,status_tab:status_tab},
            success:function(data){
                $('#tampil_tabel').html(data);  
            }
        });
    }

    $('#tab_status_ujian .nav-link').on('click', function(e){
        e.preventDefault();
        $('#tab_status_ujian .nav-link').removeClass('active');
        $(this).addClass('active');
        $('#status_tab').val($(this).data('status'));
        tabel_ujian();
    });


</script>
