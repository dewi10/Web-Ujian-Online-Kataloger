<?php 
    session_start();
    $id_siswa=$_SESSION["id_siswa"];
    require_once __DIR__ . '/../../../config/database.php';
    $id_ujian = mysqli_real_escape_string($kon, (string) ($_POST["id_ujian"] ?? ''));
    $id_siswa_esc = mysqli_real_escape_string($kon, (string) $id_siswa);
    if ($id_ujian === '') {
        exit;
    }
    $current_index = 1;

    if (isset($_POST["id_soal"])){
        //Jika soal dimuat dari nomor soal yang dipilih siswa
        $id_soal = mysqli_real_escape_string($kon, (string) $_POST["id_soal"]);

        $sql="select * from soal where id_soal='$id_soal' and id_ujian='$id_ujian' limit 1";
        $hasil=mysqli_query($kon,$sql);
        $data = ($hasil && mysqli_num_rows($hasil) > 0) ? mysqli_fetch_array($hasil) : false; 
        
        // Hitung nomor soal berdasarkan urutan
        if (isset($_SESSION['urutan_soal'][$id_ujian])) {
            $urutan = $_SESSION['urutan_soal'][$id_ujian];
            $idx = array_search((int) $id_soal, array_map('intval', $urutan));
            $current_index = ($idx !== false) ? ($idx + 1) : 1;
        }

    } else if (isset($_POST["continue_exam"]) && $_POST["continue_exam"]) {
        // Continue exam - mulai dari soal yang belum dijawab
        $id_soal = 0;
        $current_index = 1;
        
        if (isset($_SESSION['urutan_soal'][$id_ujian]) && is_array($_SESSION['urutan_soal'][$id_ujian])) {
            $urutan = $_SESSION['urutan_soal'][$id_ujian];
            
            // Cari soal pertama yang belum dijawab
            foreach ($urutan as $index => $soal_id) {
                $sid = mysqli_real_escape_string($kon, (string) $soal_id);
                $cek_jawaban = mysqli_query($kon, "SELECT * FROM hasil WHERE id_soal='$sid' AND id_siswa='$id_siswa_esc' AND id_ujian='$id_ujian' LIMIT 1");
                $jawaban = ($cek_jawaban && mysqli_num_rows($cek_jawaban) > 0) ? mysqli_fetch_assoc($cek_jawaban) : false;
                $ada_pg = $jawaban && (int) $jawaban['id_jawaban'] !== 0;
                $ada_es = $jawaban && (trim((string)($jawaban['essay'] ?? '')) !== '' || trim((string)($jawaban['jawaban_essay'] ?? '')) !== '');
                if (!$jawaban || (!$ada_pg && !$ada_es)) {
                    $id_soal = $soal_id;
                    $current_index = $index + 1;
                    break;
                }
            }
            
            // Jika semua soal sudah dijawab, ambil soal pertama
            if ($id_soal == 0 && count($urutan) > 0) {
                $id_soal = $urutan[0];
                $current_index = 1;
            }
        }

        $id_soal_sql = mysqli_real_escape_string($kon, (string) $id_soal);
        if ($id_soal > 0) {
            $sql="select * from soal where id_soal='$id_soal_sql' and id_ujian='$id_ujian' limit 1";
        } else {
            $sql="select * from soal where id_ujian='$id_ujian' order by id_soal asc limit 1";
        }
        $hasil=mysqli_query($kon,$sql);
        $data = ($hasil && mysqli_num_rows($hasil) > 0) ? mysqli_fetch_array($hasil) : false;
        if ($data) {
            $id_soal=$data['id_soal'];
        }
        
    } else {
        //Soal pertama mengikuti urutan acak yang tersimpan di session
        $id_soal = 0;
        if (isset($_SESSION['urutan_soal']) && isset($_SESSION['urutan_soal'][$id_ujian]) && is_array($_SESSION['urutan_soal'][$id_ujian]) && count($_SESSION['urutan_soal'][$id_ujian]) > 0) {
            $id_soal = (int)$_SESSION['urutan_soal'][$id_ujian][0];
        }

        $id_soal_sql = mysqli_real_escape_string($kon, (string) $id_soal);
        if ($id_soal > 0) {
            $sql="select * from soal where id_soal='$id_soal_sql' and id_ujian='$id_ujian' limit 1";
        } else {
            $sql="select * from soal where id_ujian='$id_ujian' order by id_soal asc limit 1";
        }
        $hasil=mysqli_query($kon,$sql);
        $data = ($hasil && mysqli_num_rows($hasil) > 0) ? mysqli_fetch_array($hasil) : false;
        if ($data) {
            $id_soal=$data['id_soal'];
        }
    }

    if (!$data) {
        exit;
    }

    //Menampilkan soal
    echo "<p>".$data['soal']."</p>";
    if ($data['gambar']!='') echo "<p> <img src='pages/guru/soal/gambar/".$data['gambar']."' width='40%' class='img-thumbnail'></p>";
?>

<script>
// Update nomor soal di header
document.getElementById("nomor").innerHTML = <?php echo $current_index; ?>;
</script>

<?php


    $cek_tipe=mysqli_query($kon,"select * from ujian where id_ujian='$id_ujian'");
    $tipe = ($cek_tipe && mysqli_num_rows($cek_tipe) > 0) ? mysqli_fetch_array($cek_tipe) : false;
    if (!$tipe) {
        exit;
    }

    if ($tipe['tipe_soal']=='1'):

    // $sql="select * from jawaban where id_soal='$id_soal' order by rand()";
    $id_soal_pg = mysqli_real_escape_string($kon, (string) $id_soal);
    $sql = "select * from jawaban where id_soal='$id_soal_pg' order by id_jawaban";
    $get=mysqli_query($kon,$sql);
    $no=0;
    $alfabet = array("A", "B", "C","D","E");
    while ($get && ($row = mysqli_fetch_array($get))):
        $ambil=mysqli_query($kon,"select * from hasil where id_soal='$id_soal_pg' and id_siswa='$id_siswa_esc' and id_ujian='$id_ujian' limit 1");
        $take = ($ambil && mysqli_num_rows($ambil) > 0) ? mysqli_fetch_array($ambil) : false;
        $take_jw = $take ? (int) ($take['id_jawaban'] ?? 0) : 0;
        
?>
        <div class="form-check">
            <label class="form-check-label">
                <input type="radio" name="pilih"  class="pilih form-check-input" id_siswa="<?php echo htmlspecialchars((string) $id_siswa); ?>"  id_jawaban="<?php echo $row['id_jawaban'];?>" id_soal="<?php echo (int) $id_soal;?>" <?php if ((int) $row['id_jawaban'] === $take_jw) echo "checked"; ?>  ><?php echo $alfabet[$no].". ".$row['pilihan']; ?>
            </label>
        </div>
    <?php 
    $no++; 
    endwhile;

    endif;
?>

<?php if ($tipe['tipe_soal']=='2'): ?>
    <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Masukan Jawaban:</label>
                    <textarea class="form-control" id="jawaban_essay" name="jawaban_essay" rows="5" ></textarea>
                </div>
            </div>
        </div>
<?php endif; ?>

    <input type="hidden" name="id_ujian" value="<?php echo htmlspecialchars($id_ujian); ?>" class="ambil-soal-id-ujian" />
    <input type="hidden" name="id_siswa" value="<?php echo $id_siswa; ?>" id="id_siswa" />
    <input type="hidden" name="id_soal" value="<?php echo $id_soal; ?>" id="id_soal" />
    <script>
        $('.pilih').on('click',function(){
            var id_jawaban = $(this).attr("id_jawaban");
            var id_soal = $(this).attr("id_soal");
            var id_siswa = $(this).attr("id_siswa");
            var id_ujian_val = <?php echo json_encode((string) $id_ujian); ?>;
            
            // Tampilkan indikator auto-save
            var $saveIndicator = $('<span class="text-success ml-2"><i class="fas fa-check-circle"></i> Tersimpan</span>');
            $(this).parent().append($saveIndicator);
            setTimeout(function() {
                $saveIndicator.fadeOut(function() {
                    $(this).remove();
                });
            }, 2000);
            
            $.ajax({
                url: 'pages/siswa/ujian/update-jawaban.php',
                method: 'post',
                data: {id_jawaban:id_jawaban,id_soal:id_soal,id_siswa:id_siswa,id_ujian:id_ujian_val},
                success:function(data){
                    $('#nomor_soal').load('pages/siswa/ujian/nomor-soal.php?id=<?php echo urlencode($id_ujian); ?>');
                }
            });

            $.ajax({
                url: 'pages/siswa/ujian/progress.php',
                method: 'post',
                data: {id_ujian:id_ujian_val},
                success:function(data){
                    $('#tampil_progress').html(data);
                }
            });

        });
    </script>

    <script>

        $(document).ready( function () {
            get_jawaban_essay();
        });


        $("#jawaban_essay").bind('keyup', function () {
            simpan_jawaban_essay();
           
        });

        $("#jawaban_essay").bind('change', function () {
            simpan_jawaban_essay();
        });
        function simpan_jawaban_essay(){
            var jawaban_essay = $('#jawaban_essay').val();
            var id_soal = $('#id_soal').val();
            var id_siswa =$('#id_siswa').val();
            var id_ujian_val = <?php echo json_encode((string) $id_ujian); ?>;
            $.ajax({
                url: 'pages/siswa/ujian/jawaban-essay.php',
                method: 'POST',
                data:{jawaban_essay:jawaban_essay,id_soal:id_soal,id_siswa:id_siswa,id_ujian:id_ujian_val},
                success:function(data){
                    $('#nomor_soal').load('pages/siswa/ujian/nomor-soal.php?id=<?php echo urlencode($id_ujian); ?>');
                    $.ajax({
                        url: 'pages/siswa/ujian/progress.php',
                        method: 'post',
                        data: { id_ujian: id_ujian_val },
                        success: function(html) {
                            $('#tampil_progress').html(html);
                        }
                    });
                }
            }); 
        }

        function get_jawaban_essay(){
            var id_soal = $('#id_soal').val();
            var id_siswa = $('#id_siswa').val();
            var id_ujian_val = <?php echo json_encode((string) $id_ujian); ?>;
            $.ajax({
                url: 'pages/siswa/ujian/get-jawaban-essay.php',
                method: 'POST',
                data:{id_soal:id_soal,id_siswa:id_siswa,id_ujian:id_ujian_val},
                success:function(data){
                    $('#jawaban_essay').val(data);
                }
            }); 
        }
    </script>

