
<?php 
    session_start();
    $id_guru=$_SESSION["id_guru"];
    include '../../../config/database.php';
    $id_ujian=$_GET['id_ujian'];

    $sql="select * from ujian u inner join soal s on s.id_ujian=u.id_ujian where u.id_ujian='$id_ujian' and u.id_guru='$id_guru' order by id_soal desc limit 1";
    $hasil=mysqli_query($kon,$sql);
    $data = mysqli_fetch_array($hasil); 

    $id_soal=$data['id_soal'];
?>


<p> <?php echo $data['soal'];?> <p>
<?php 
    if ($data['gambar']!=''){
        echo "<p> <img src='pages/guru/soal/gambar/".$data['gambar']."' width='50%' class='img-thumbnail'></p>";
    }
?>
<form id="form_pilih_jawaban" action="pages/guru/soal/simpan-jawaban.php" method="post">
    <input type="hidden" name="id_soal" value="<?php echo $id_soal; ?>" />
    <?php 

        $sql="select * from jawaban where id_soal='$id_soal'";
        $get=mysqli_query($kon,$sql);
        $no=0;
        $alfabet = array("A", "B", "C","D","E");
        //Menampilkan data dengan perulangan while
        while ($row = mysqli_fetch_array($get)):
        
    ?>
        <div class="form-check">
        <label class="form-check-label">
            <input type="radio" required <?php if ($row['jawaban']==1) echo "checked"; ?> name="pilihan" class="pilihan form-check-input" id_jawaban="<?php echo $row['id_jawaban'];?>" ><?php echo $alfabet[$no]; ?>. <?php echo $row['pilihan']; ?>
        </label>
        </div>
    <?php $no++; endwhile; ?>

    <input type="hidden" id="id_soal" name="id_soal" value="<?php echo $id_soal;?>" class="form-control"/>
    <input type="hidden" id="id_jawaban" name="id_jawaban" class="form-control"/>
    <input type="hidden" id="id_ujian" name="id_ujian" value="<?php echo $_GET['id_ujian'];?>" class="form-control"/>
    <input type="hidden" name="ajax" value="1" />
    <br>
    <button type="submit" name="tambah_soal" id="Submit" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit & Soal Berikutnya</button>
    <button type="button" id="selesai_input_soal" class="btn btn-secondary">Selesai</button>
</form>

<script>
    $('.pilihan').on('click',function(){
        var id_jawaban = $(this).attr("id_jawaban");
        $('#id_jawaban').val(id_jawaban);
    });

    $('#form_pilih_jawaban').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        var id_ujian = $('#id_ujian').val();

        $.ajax({
            type: 'POST',
            url: 'pages/guru/soal/simpan-jawaban.php',
            data: data,
            success: function(response){
                var res = {};
                try {
                    res = JSON.parse(response);
                } catch (err) {
                    alert('Terjadi kesalahan saat menyimpan jawaban.');
                    return;
                }

                if (res.status === 'success') {
                    $('#tampil').load('pages/guru/soal/tambah.php', {id_ujian:id_ujian});
                    document.getElementById('judul').innerHTML='Tambah Soal Pilihan Ganda';
                } else {
                    alert('Gagal menyimpan jawaban: ' + (res.message || 'Unknown error'));
                }
            },
            error: function(){
                alert('Gagal menyimpan jawaban.');
            }
        });
    });

    $('#selesai_input_soal').on('click', function(){
        var id_ujian = $('#id_ujian').val();
        window.location.href = 'index.php?page=input-soal&id=' + id_ujian + '&tambah=berhasil';
    });
</script>

