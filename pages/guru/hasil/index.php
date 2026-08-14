<?php
//validasi hanya guru yang boleh mengakses halaman ini
$username = $_SESSION['username'];
$cek = mysqli_query ($kon,"select * from guru where username='".$username."' limit 1");
$jum = mysqli_num_rows($cek);

if ($jum<1){
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}



if (isset($_GET['edit-nilai'])) {
    if ($_GET['edit-nilai']=='berhasil'){
        echo"<div class='alert alert-success'><strong>Berhasil!</strong> Nilai berhasil diedit!</div>";
    }else if ($_GET['edit-nilai']=='gagal'){
        echo"<div class='alert alert-danger'><strong>Gagal!</strong> Nilai gagal diedit!</div>";
    }    
}

?>
<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-clipboard-check"></i> Hasil Ujian</h3>
    </div>
    <div class="card-body">

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Kategori</label>
                    <select class="form-control" id="kelas">
                        <option value="">Pilih Kategori</option>
                        <?php
                            $id_guru = $_SESSION["id_guru"];
                            include 'config/database.php';
                            $sql="select DISTINCT k.id_kelas, k.kode_kelas, k.nama_kelas from kelas k 
                            inner join ujian u on k.id_kelas=u.id_kelas
                            where u.id_guru='$id_guru' and u.status_aktif='1'
                            order by k.nama_kelas";
                            $hasil=mysqli_query($kon,$sql);
                            while ($data = mysqli_fetch_array($hasil)) {
                                ?>
                            <option value="<?php echo $data['id_kelas'];?>"><?php echo $data['nama_kelas'];?> </option>
                            <?php
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Kode Ujian/Mata Ujian</label>
                    <select class="form-control" id="mapel">

                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <input type="button" class="btn btn-warning btn-block" id="tampilkan" value="Tampilkan">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">    
                <div id="tampil_data"> </div>
            </div>
        </div>

    </div> 
</div>

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



<script>

    $(document).ready( function () {
        <?php if(isset($_GET['id_ujian'])): ?>
        // Auto-load tabel jika ada parameter id_ujian dari edit nilai
        var id_ujian_param = '<?php echo $_GET['id_ujian']; ?>';
        
        // Ambil id_kelas dari id_ujian
        $.ajax({
            url: 'pages/guru/hasil/get-kelas-by-ujian.php',
            method: 'post',
            data: {id_ujian: id_ujian_param},
            dataType: 'json',
            success: function(data) {
                if(data.id_kelas) {
                    // Set dropdown kelas
                    $('#kelas').val(data.id_kelas);
                    
                    // Load mapel berdasarkan kelas
                    get_mapel(function() {
                        // Setelah mapel terload, set value ujian dan tampilkan
                        $('#mapel').val(id_ujian_param);
                        tabel_hasil();
                    });
                } else {
                    get_mapel();
                }
            },
            error: function() {
                get_mapel();
            }
        });
        <?php else: ?>
        get_mapel();
        tabel_hasil();
        <?php endif; ?>
    });


    $("#kelas").change(function() {
        get_mapel();
    });

    $('#tampilkan').on('click',function(){
        tabel_hasil();
    });


    function get_mapel(callback){
        var kelas=$("#kelas").val();
        $.ajax({
            url: 'pages/guru/hasil/get-mapel.php',
            method : "POST",
            data : {kelas:kelas},
            async : false,
            dataType : 'json',
            success: function(data){
                var html = '';
                var i;
                if(data.length == 0){
                    html = '<option value="">Tidak ada ujian</option>';
                } else {
                    for(i=0; i<data.length; i++){
                        html += '<option value='+data[i].id_ujian+'>'+data[i].kode_ujian+' - '+data[i].nama_mapel+'</option>';
                    }
                }
                $('#mapel').html(html);
                
                // Panggil callback jika ada
                if(typeof callback === 'function') {
                    callback();
                }
            }
        })
    }

    function tabel_hasil(){
        var id_ujian=$("#mapel").val();
        if(!id_ujian || id_ujian == ''){
            $('#tampil_data').html("<div class='alert alert-warning'>Belum ada data!</div>");
            return;
        }
        $.ajax({
            url: 'pages/guru/hasil/tabel-hasil.php',
            method: 'post',
            data: {id_ujian:id_ujian},
            success:function(data){
                $('#tampil_data').html(data);  
            }
        });
    }

</script>



