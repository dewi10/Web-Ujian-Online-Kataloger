<?php
    include '../../../config/database.php';
    $id_ujian = isset($_POST['id_ujian']) ? (int)$_POST['id_ujian'] : 0;
    $q_no = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM soal WHERE id_ujian='$id_ujian' AND tipe='1'");
    $d_no = mysqli_fetch_array($q_no);
    $nomor_soal = ((int)$d_no['jumlah']) + 1;
?>

<div id="tampil" class="soal-form-compact">
    <form id="form_tambah_soal" method="post" enctype="multipart/form-data">

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <input type="hidden" name="id_ujian" class="form-control" value="<?php echo $id_ujian;?>">
                </div>
            </div>
        </div>
        <div class="alert alert-info py-2 mb-3">
            <strong>Masukan soal ke <?php echo $nomor_soal; ?></strong>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group mb-2">
                    <!-- <label>Masukan Soal:</label> -->
                    <textarea class="form-control" name="soal" rows="3" required></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-7">
                <p class="mb-2">Masukan Pilihan Jawaban:</p>

                <div class="form-group mb-2">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2" style="width:18px;">A</label>
                        <textarea class="form-control opsi-jawaban" name="pilihan[]" rows="2"></textarea>
                        <input type="radio" name="kunci" value="1" required class="ml-2">
                    </div>
                </div>

                <div class="form-group mb-2">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2" style="width:18px;">B</label>
                        <textarea class="form-control opsi-jawaban" name="pilihan[]" rows="2"></textarea>
                        <input type="radio" name="kunci" value="2" class="ml-2">
                    </div>
                </div>

                <div class="form-group mb-2">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2" style="width:18px;">C</label>
                        <textarea class="form-control opsi-jawaban" name="pilihan[]" rows="2"></textarea>
                        <input type="radio" name="kunci" value="3" class="ml-2">
                    </div>
                </div>

                <div class="form-group mb-2">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2" style="width:18px;">D</label>
                        <textarea class="form-control opsi-jawaban" name="pilihan[]" rows="2"></textarea>
                        <input type="radio" name="kunci" value="4" class="ml-2">
                    </div>
                </div>

                <div class="form-group mb-2">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2" style="width:18px;">E</label>
                        <textarea class="form-control opsi-jawaban" name="pilihan[]" rows="2"></textarea>
                        <input type="radio" name="kunci" value="5" class="ml-2">
                    </div>
                </div>
            </div>

            <div class="col-sm-5">
                <div class="form-group mb-2">
                    <div id="msg"></div>
                    <label>Gambar: <small class="text-danger">Abaikan jika tidak menggunakan gambar</small></label>
                    <input type="file" name="gambar" class="file" >
                    <div class="input-group my-2">
                        <input type="text" class="form-control" disabled placeholder="Upload gambar" id="file">
                        <div class="input-group-append">
                            <button type="button" id="pilih_gambar" class="browse btn btn-dark">Pilih</button>
                        </div>
                    </div>
                    <img src="img/img80.png" id="preview" class="img-thumbnail" style="max-height:90px;">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 text-right">
                <button type="button" name="tambah_soal" id="tambah_soal" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit & Continue</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </div>
    </form>
</div>
<script>
        $(document).ready(function(){
            $('#tambah_soal').click(function(){
                var form = $('#form_tambah_soal')[0];
                var data = new FormData(form);
                $.ajax({
                    type	: 'POST',
                    enctype: 'multipart/form-data',
                    url	: "pages/guru/soal/simpan-soal.php",
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success	: function(data){
                        var respon = (data || '').toString().trim();
                        if (respon === 'ok'){
                            var id_ujian = $('input[name="id_ujian"]').val();
                            $('#tampil').load('pages/guru/soal/tambah.php', {id_ujian:id_ujian});
                            document.getElementById("judul").innerHTML='Tambah Soal Pilihan Ganda';
                        } else {
                            alert('Gagal simpan soal. ' + respon);
                        }
                    },
                    error: function(){
                        alert('Gagal simpan soal.');
                    }
                });
            });
        });
</script>

<style>
    .file {
    visibility: hidden;
    position: absolute;
    }
    .soal-form-compact textarea.form-control {
        min-height: 46px;
    }
    .soal-form-compact .opsi-jawaban {
        min-height: 56px;
        resize: vertical;
    }
</style>

<script>
    $(document).on("click", "#pilih_gambar", function() {
    var file = $(this).parents().find(".file");
    file.trigger("click");
    });
    $('input[type="file"]').change(function(e) {
    var fileName = e.target.files[0].name;
    $("#file").val(fileName);

    var reader = new FileReader();
    reader.onload = function(e) {
        // get loaded data and render thumbnail.
        document.getElementById("preview").src = e.target.result;
    };
    // read the image file as a data URL.
    reader.readAsDataURL(this.files[0]);
    });

</script>
