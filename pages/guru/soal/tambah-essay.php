<div id="tampil" class="soal-form-compact">
    <form id="form_tambah_soal_essay" method="post" enctype="multipart/form-data">
    <?php
        include '../../../config/database.php';
        $id_ujian = isset($_POST['id_ujian']) ? (int)$_POST['id_ujian'] : 0;
        $query = mysqli_query($kon, "SELECT max(id_soal) as id_terbesar FROM soal");
        $ambil= mysqli_fetch_array($query);
        $id_soal = $ambil['id_terbesar'];
        $id_soal++;
        $huruf = "S";
        $kode_soal = $huruf . sprintf("%03s", $id_soal);

        $q_no = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM soal WHERE id_ujian='$id_ujian' AND tipe='2'");
        $d_no = mysqli_fetch_array($q_no);
        $nomor_soal = ((int)$d_no['jumlah']) + 1;
    ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <input type="hidden" name="kode_soal" class="form-control" value="<?php echo $kode_soal;?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <input type="hidden" name="id_ujian" id="id_ujian" class="form-control" value="<?php echo $id_ujian;?>">
                </div>
            </div>
        </div>
        <div class="alert alert-info py-2 mb-3">
            <strong>Masukan soal ke <?php echo $nomor_soal; ?></strong>
        </div>
        <div class="row">
            <div class="col-sm-7">
                <div class="form-group mb-2">
                    <label>Masukan Soal:</label>
                    <textarea class="form-control" name="soal" rows="4" required></textarea>
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
                <button type="button" name="tambah_soal" id="tambah_soal_essay" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit & Continue</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </div>
    </form>
</div>
<script>
        $(document).ready(function(){
            $('#tambah_soal_essay').click(function(){
                var form = $('#form_tambah_soal_essay')[0];
                var data = new FormData(form);
                var id_ujian = $("#id_ujian").val();
                $.ajax({
                    type	: 'POST',
                    enctype: 'multipart/form-data',
                    url	: "pages/guru/soal/simpan-soal-essay.php",
                    dataType: 'json',
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success	: function(data){
                        var res = data;
                        if (typeof data === 'string') {
                            try {
                                res = JSON.parse(data);
                            } catch (e) {
                                alert('Gagal simpan soal essay. Respon server tidak valid.');
                                return;
                            }
                        }

                        if (res.status === 'ok') {
                            var soalEsc = $('<div>').text(res.soal || '').html();
                            if (res.gambar && res.gambar !== '') {
                                soalEsc += "<p><img src='pages/guru/soal/gambar/" + encodeURIComponent(res.gambar) + "' width='50%' class='img-thumbnail'></p>";
                            }

                            var table = null;
                            try {
                                if ($('#tabel_soal').length && $.fn.dataTable) {
                                    table = $('#tabel_soal').DataTable();
                                }
                            } catch (e) {
                                table = null;
                            }

                            if (table) {
                                var noBaru = table.rows().count() + 1;
                                var checkboxHtml = "<div class='custom-control custom-checkbox'>"
                                    + "<input type='checkbox' class='custom-control-input' id='customCheckAjax" + res.id_soal + "' value='" + res.id_soal + "' name='soal[]' />"
                                    + "<label class='custom-control-label' for='customCheckAjax" + res.id_soal + "'></label>"
                                    + "</div>";

                                var aksiHtml = "<button type='button' class='btn_edit_essay btn btn-warning btn-circle' no='" + noBaru + "' id_soal='" + res.id_soal + "' id_ujian='" + id_ujian + "' data-toggle='tooltip' title='Edit'><i class='fa fa-edit'></i></button> "
                                    + "<a href='pages/guru/soal/hapus.php?id_soal=" + res.id_soal + "&id=" + id_ujian + "&gambar=" + encodeURIComponent(res.gambar || '') + "&tipe=essay' class='btn_hapus_essay btn btn-danger btn-circle' data-toggle='tooltip' title='Hapus'><i class='fas fa-trash'></i></a>";

                                table.row.add([checkboxHtml, noBaru, soalEsc, aksiHtml]).draw(false);
                            } else {
                                var noManual = $('#tabel_soal tbody tr').length + 1;
                                var trHtml = "<tr>"
                                    + "<td class='text-center'><div class='custom-control custom-checkbox'><input type='checkbox' class='custom-control-input' id='customCheckAjax" + res.id_soal + "' value='" + res.id_soal + "' name='soal[]' /><label class='custom-control-label' for='customCheckAjax" + res.id_soal + "'></label></div></td>"
                                    + "<td>" + noManual + "</td>"
                                    + "<td>" + soalEsc + "</td>"
                                    + "<td><button type='button' class='btn_edit_essay btn btn-warning btn-circle' no='" + noManual + "' id_soal='" + res.id_soal + "' id_ujian='" + id_ujian + "'><i class='fa fa-edit'></i></button> <a href='pages/guru/soal/hapus.php?id_soal=" + res.id_soal + "&id=" + id_ujian + "&gambar=" + encodeURIComponent(res.gambar || '') + "&tipe=essay' class='btn_hapus_essay btn btn-danger btn-circle'><i class='fas fa-trash'></i></a></td>"
                                    + "</tr>";
                                $('#tabel_soal tbody').append(trHtml);
                            }

                            $('#tampil').load('pages/guru/soal/tambah-essay.php', {id_ujian:id_ujian});
                            document.getElementById("judul").innerHTML='Tambah Soal Essay';
                        } else {
                            alert('Gagal simpan soal essay. ' + (res.message || 'Unknown error'));
                        }
                    },
                    error: function(xhr){
                        alert('Gagal simpan soal essay. ' + (xhr.responseText || ''));
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
