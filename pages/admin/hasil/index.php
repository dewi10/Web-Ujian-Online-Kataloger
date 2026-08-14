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
        <h3><i class="fas fa-clipboard-check"></i> Hasil Ujian</h3>
    </div>
    <div class="card-body">
    <script> $('title').text('HASIL UJIAN'); </script>

        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Kategori</label>
                    <select class="form-control" id="kelas">
                        <option value="">Pilih Kategori</option>
                        <?php
                            include 'config/database.php';
                            $sql="select DISTINCT k.id_kelas, k.kode_kelas, k.nama_kelas from kelas k 
                            inner join ujian u on k.id_kelas=u.id_kelas
                            where u.status_aktif='1'
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
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Pengawas</label>
                    <select class="form-control" id="guru">
    
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Kode Ujian/Mata Ujian</label>
                    <select class="form-control" id="mapel">

                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <input type="button" class="btn btn-warning btn-block" id="tampilkan" value="Tampilkan">
                </div>
            </div>
        </div>

        <div id="tampil_data"> </div>

     
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
        get_guru();
        get_mapel();
        hasil();
    });

    
    $("#kelas").change(function() {
        get_guru();
        get_mapel();
    });

    $("#guru").change(function() {
        get_mapel();
    });

    $('#tampilkan').on('click',function(){
        hasil();
    });


    function get_guru(){
        var kelas=$("#kelas").val();
        $.ajax({
            url: 'pages/admin/hasil/get-guru.php',
            method : "POST",
            data : {kelas:kelas},
            async : false,
            dataType : 'json',
            success: function(data){
                var html = '';
                var i;
                for(i=0; i<data.length; i++){
                    html += '<option value='+data[i].id_guru+'>'+data[i].nama_guru+'</option>';
                }
                $('#guru').html(html);
            }
        })
    }

    function get_mapel(){
        var kelas=$("#kelas").val();
        var guru=$("#guru").val();
        $.ajax({
            url: 'pages/admin/hasil/get-mapel.php',
            method : "POST",
            data : {kelas:kelas,guru:guru},
            async : false,
            dataType : 'json',
            success: function(data){
                var html = '';
                var i;
                for(i=0; i<data.length; i++){
                    html += '<option value='+data[i].id_ujian+'>'+data[i].kode_ujian+' - '+data[i].nama_mapel+'</option>';
                }
                $('#mapel').html(html);
            }
        })
    }




    function hasil(){
        var id_ujian=$("#mapel").val();
        $.ajax({
            url: 'pages/admin/hasil/tabel-hasil.php',
            method: 'post',
            data: {id_ujian:id_ujian},
            success:function(data){
                $('#tampil_data').html(data);  
            }
        });
    }
</script>

