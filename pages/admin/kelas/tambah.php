<?php

    if (isset($_POST['tambah_kelas'])) {
        
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

            $kode=input($_POST["kode"]);
            $nama_kelas=input($_POST["nama_kelas"]);

            // Cek apakah kombinasi kategori dan jenjang sudah ada
            $cek_duplikat = mysqli_query($kon, "SELECT * FROM kelas WHERE kode_kelas='$kode' AND nama_kelas='$nama_kelas'");
            if(mysqli_num_rows($cek_duplikat) > 0){
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=kelas&tambah=duplikat");
                exit;
            }
        
      
            $sql="insert into kelas (kode_kelas,nama_kelas) values
            ('$kode','$nama_kelas')";
        
            //Menyimpan ke tabel kelas
            $simpan_kelas=mysqli_query($kon,$sql);

            if ($simpan_kelas) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=kelas&tambah=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=kelas&tambah=gagal");
            }
        }
    }
?>

<form action="pages/admin/kelas/tambah.php" method="post">

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Kategori:</label>
                <select name="kode" id="kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Terampil">Terampil</option>
                    <option value="Ahli">Ahli</option>
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
            <button type="submit" name="tambah_kelas" id="submit" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){
        $('#kategori').change(function(){
            var kategori = $(this).val();
            var jenjang_dropdown = $('#jenjang');
            
            jenjang_dropdown.empty();
            
            if(kategori == 'Terampil'){
                jenjang_dropdown.append('<option value="">Pilih Jenjang</option>');
                jenjang_dropdown.append('<option value="Pemula">Pemula</option>');
                jenjang_dropdown.append('<option value="Terampil">Terampil</option>');
                jenjang_dropdown.append('<option value="Mahir">Mahir</option>');
                jenjang_dropdown.append('<option value="Penyelia">Penyelia</option>');
            } else if(kategori == 'Ahli'){
                jenjang_dropdown.append('<option value="">Pilih Jenjang</option>');
                jenjang_dropdown.append('<option value="Ahli">Ahli</option>');
                jenjang_dropdown.append('<option value="Pertama">Pertama</option>');
                jenjang_dropdown.append('<option value="Muda">Muda</option>');
                jenjang_dropdown.append('<option value="Madya">Madya</option>');
                jenjang_dropdown.append('<option value="Utama">Utama</option>');
            } else {
                jenjang_dropdown.append('<option value="">Pilih Kategori terlebih dahulu</option>');
            }
        });
    });
</script>

