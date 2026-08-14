<?php

    if (isset($_POST['edit_kelas'])) {
        
        //Fungsi untuk mencegah inputan karakter yang tidak sesuai
        function input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        //Cek apakah ada kiriman form dari method post
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            include '../../../config/database.php';
            //Memulai transaksi
            mysqli_query($kon,"START TRANSACTION");

            $id_kelas=input($_POST["id_kelas"]);
            $nama_kelas=input($_POST["nama_kelas"]);
            $kode=input($_POST["kode"]);

            // Cek apakah kombinasi kategori dan jenjang sudah ada (selain data yang sedang diedit)
            $cek_duplikat = mysqli_query($kon, "SELECT * FROM kelas WHERE kode_kelas='$kode' AND nama_kelas='$nama_kelas' AND id_kelas != $id_kelas");
            if(mysqli_num_rows($cek_duplikat) > 0){
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=kelas&edit=duplikat");
                exit;
            }
      
            $sql="update kelas set
            kode_kelas='$kode',
            nama_kelas='$nama_kelas'
            where id_kelas=$id_kelas";
        
            //Menyimpan ke tabel kelas
            $simpan_kelas=mysqli_query($kon,$sql);

            //Update nis pada tabel ujian
            $sql="update ujian set
            id_kelas='$id_kelas'
            where id_kelas=$id_kelas";
            $ujian_kelas=mysqli_query($kon,$sql);


            if ($simpan_kelas and $ujian_kelas) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=kelas&edit=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=kelas&edit=gagal");
            }
        }
    }
?>

<?php 
    include '../../../config/database.php';
    $id_kelas=$_POST["id_kelas"];
    $sql="select * from kelas where id_kelas=$id_kelas limit 1";
    $hasil=mysqli_query($kon,$sql);
    $data = mysqli_fetch_array($hasil); 
?>

<form action="pages/admin/kelas/edit.php" method="post">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <input type="hidden" name="id_kelas" value="<?php echo $data['id_kelas'];?>" class="form-control" >
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Kategori:</label>
                <select name="kode" id="kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Terampil" <?php echo ($data['kode_kelas']=='Terampil')?'selected':''; ?>>Terampil</option>
                    <option value="Ahli" <?php echo ($data['kode_kelas']=='Ahli')?'selected':''; ?>>Ahli</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Jenjang:</label>
                <select name="nama_kelas" id="jenjang" class="form-control" required>
                    <option value="">Pilih Kategori terlebih dahulu</option>
                </select>
           
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <button type="submit" name="edit_kelas" id="submit" class="btn btn-warning">Update</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){
        // Set jenjang dropdown berdasarkan kategori yang sudah dipilih
        var kategori_awal = '<?php echo $data['kode_kelas']; ?>';
        var jenjang_awal = '<?php echo $data['nama_kelas']; ?>';
        
        if(kategori_awal){
            loadJenjang(kategori_awal, jenjang_awal);
        }
        
        $('#kategori').change(function(){
            var kategori = $(this).val();
            loadJenjang(kategori, '');
        });
        
        function loadJenjang(kategori, selected){
            var jenjang_dropdown = $('#jenjang');
            jenjang_dropdown.empty();
            
            if(kategori == 'Terampil'){
                jenjang_dropdown.append('<option value="">Pilih Jenjang</option>');
                jenjang_dropdown.append('<option value="Pemula" '+(selected=='Pemula'?'selected':'')+'>Pemula</option>');
                jenjang_dropdown.append('<option value="Terampil" '+(selected=='Terampil'?'selected':'')+'>Terampil</option>');
                jenjang_dropdown.append('<option value="Mahir" '+(selected=='Mahir'?'selected':'')+'>Mahir</option>');
                jenjang_dropdown.append('<option value="Penyelia" '+(selected=='Penyelia'?'selected':'')+'>Penyelia</option>');
            } else if(kategori == 'Ahli'){
                jenjang_dropdown.append('<option value="">Pilih Jenjang</option>');
                jenjang_dropdown.append('<option value="Ahli" '+(selected=='Ahli'?'selected':'')+'>Ahli</option>');
                jenjang_dropdown.append('<option value="Pertama" '+(selected=='Pertama'?'selected':'')+'>Pertama</option>');
                jenjang_dropdown.append('<option value="Muda" '+(selected=='Muda'?'selected':'')+'>Muda</option>');
                jenjang_dropdown.append('<option value="Madya" '+(selected=='Madya'?'selected':'')+'>Madya</option>');
                jenjang_dropdown.append('<option value="Utama" '+(selected=='Utama'?'selected':'')+'>Utama</option>');
            } else {
                jenjang_dropdown.append('<option value="">Pilih Kategori terlebih dahulu</option>');
            }
        }
    });
</script>

