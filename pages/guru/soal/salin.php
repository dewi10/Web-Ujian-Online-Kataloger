<form id="form_salin_soal">
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info">
            Fitur salin soal digunakan untuk menyalin soal yang sama ke beberapa ujian di kelas yang berbeda.
            </div>
        </div>
    </div>

    <input type="hidden" name="id_ujian_tujuan" id="id_ujian_tujuan" value="<?php echo $_POST['id_ujian']; ?>"></input>
    <input type="hidden" name="tipe_soal" id="tipe_soal" value="<?php echo $_POST['tipe_soal']; ?>"></input>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Pilih Ujian:</label>
                <select class="form-control" name="id_ujian_asal" id="ujian">
                <?php
                    session_start();
                    $id_guru=$_SESSION['id_guru'];
                    $id_ujian=addslashes(trim($_POST['id_ujian']));
                    $tipe='';
                    if ($_POST['tipe_soal']=='pg'){
                        $tipe=1;
                    }else if ($_POST['tipe_soal']=='essay'){
                        $tipe=2;
                    }
                    include '../../../config/database.php';
                    $sql="select * from ujian u
                    inner join kelas k on u.id_kelas=k.id_kelas
                    inner join mapel m on m.id_mapel=u.id_mapel
                    where id_guru='$id_guru' and id_ujian not in($id_ujian) and u.tipe_soal='$tipe' and u.status_aktif='1'
                    order by k.id_kelas asc";
                    $no=0;
                    $hasil=mysqli_query($kon,$sql);
                    while ($data = mysqli_fetch_array($hasil)){
                    $no++;
                        ?>
                    <option value="<?php echo $data['id_ujian'];?>"><?php echo $data['kode_ujian'];?> - <?php echo $data['nama_kelas'];?> - <?php echo $data['nama_mapel'];?> </option>
                    <?php
                    }
                ?>
                </select>
            </div>
        </div>
    </div>
    <?php if ($no==0):?>
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning">
                Anda belum memiliki data ujian lain untuk penyalinan soal!
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-sm-12">
            <div id="data_soal">
                <!-- Di sini akan ditampilkan daftar soal -->
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-4">
            <button type="button" name="salin_soal" id="salin_soal" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit</button>
        </div>
    </div>
  
</form>
<script>
    $(document).ready( function () {
        data_soal();
    });

    $("#ujian").change(function() {
        data_soal();
    });

    function data_soal(){
        var ujian=$("#ujian").val();
        var tipe_soal=$("#tipe_soal").val();
        $.ajax({
            url: 'pages/guru/soal/data-soal.php',
            method: 'post',
            data: {ujian:ujian,tipe_soal:tipe_soal},
            success:function(data){
                $('#data_soal').html(data);  
            }
        });
    }

    $('#salin_soal').click(function () {
        konfirmasi = confirm("Apakah anda yakin ingin menyalin soal ini?");
        if (konfirmasi) {
            // Memanggil fungsi submit
            submit();
            return true;
        } else {
            return false;
        }
    });

    function submit() {
    var form = $('#form_salin_soal')[0];
    var id_ujian_tujuan = $("#id_ujian_tujuan").val();
    var tipe_soal = $("#tipe_soal").val();
    var selectedSoals = []; // Array untuk menyimpan ID soal yang dipilih

    // Mengambil semua checkbox yang dicentang
    $("input[name='soal[]']:checked").each(function() {
        selectedSoals.push($(this).val()); // Menambahkan ID soal ke dalam array
    });

    // Mengecek apakah ada soal yang dicentang
    if(selectedSoals.length === 0) {
        alert("Anda belum memilih soal untuk disalin.");
        return false;
    }

    // Menyiapkan data yang akan dikirim
    var data = new FormData(form);
    
    // Tentukan URL berdasarkan tipe soal
    var url = "pages/guru/soal/simpan-salinan.php";
    if(tipe_soal === 'essay') {
        url = "pages/guru/soal/simpan-salinan-essay.php";
    }

    // Melakukan pengiriman data melalui AJAX
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function (data) {
            window.location.href = "index.php?page=input-soal&id=" + id_ujian_tujuan + "&salin=berhasil";
        }
    });
}

</script>

