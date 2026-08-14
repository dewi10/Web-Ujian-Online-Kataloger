<?php

    if (isset($_POST['tambah_mapel'])) {
        
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

            $cek_kolom = mysqli_query($kon, "SHOW COLUMNS FROM mapel LIKE 'status_aktif'");
            if ($cek_kolom && mysqli_num_rows($cek_kolom) == 0) {
                mysqli_query($kon, "ALTER TABLE mapel ADD COLUMN status_aktif TINYINT(1) NOT NULL DEFAULT 1");
            }

            $nama_mapel=input($_POST["nama_mapel"]);

            $query = mysqli_query($kon, "SELECT max(id_mapel) as id_terbesar FROM mapel");
            $ambil= mysqli_fetch_array($query);
            $id_mapel = $ambil['id_terbesar'];
            $id_mapel++;
            //Membuat kode mapel
            $huruf = "K";
            $kode_mapel = $huruf . sprintf("%03s", $id_mapel);
      
            $sql="insert into mapel (kode_mapel,nama_mapel,status_aktif) values
            ('$kode_mapel','$nama_mapel','1')";
        
            //Menyimpan ke tabel mapel
            $simpan_mapel=mysqli_query($kon,$sql);

            if ($simpan_mapel) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=mapel&tambah=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=mapel&tambah=gagal");
            }
        }
    }
?>

<form action="pages/admin/mapel/tambah.php" method="post">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Nama Mata Ujian:</label>
                <input type="text" name="nama_mapel" class="form-control" placeholder="Masukan Nama Mata Ujian" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <button type="submit" name="tambah_mapel" id="Submit" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </div>
</form>